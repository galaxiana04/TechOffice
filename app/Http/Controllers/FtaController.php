<?php

namespace App\Http\Controllers;

use App\Models\ProjectType;
use App\Models\FtaEvent;
use App\Models\FtaIdentity;
use App\Models\FtaNode;
use App\Models\FmecaItem; // Untuk populate events dari FMECA
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class FtaController extends Controller
{
    public function editNodes($ftaidentity_id)
    {
        $ftaIdentity = FtaIdentity::with('ftaEvents')->findOrFail($ftaidentity_id);
        $nodes = FtaNode::where('fta_identity_id', $ftaidentity_id)->get();
        $fmecaItems = FmecaItem::all();

        return view('fta.edit-nodes', compact('ftaIdentity', 'nodes', 'fmecaItems'));
    }

    public function updateNodes(Request $request, $fta_identity_id)
    {
        $ftaIdentity = FtaIdentity::findOrFail($fta_identity_id);
        $validEventIds = $ftaIdentity->ftaEvents()->pluck('id')->toArray();

        $validator = Validator::make($request->all(), [
            'nodes' => 'required|array',
            'nodes.*.id' => 'nullable|exists:fta_nodes,id',
            'nodes.*.node_type' => 'required|in:and,or,basic_event',
            'nodes.*.event_name' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $nodeType = $request->input("nodes.$index.node_type");
                    if (in_array($nodeType, ['and', 'or']) && empty($value)) {
                        $fail("The $attribute field is required for $nodeType nodes.");
                    }
                },
            ],
            'nodes.*.parent_id' => 'nullable|exists:fta_nodes,id',
            'nodes.*.fta_event_id' => [
                'required_if:nodes.*.node_type,basic_event',
                function ($attribute, $value, $fail) use ($validEventIds) {
                    if (!empty($value) && !in_array($value, $validEventIds)) {
                        $fail("The selected $attribute is invalid or not associated with this FTA Identity.");
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $existingNodeIds = FtaNode::where('fta_identity_id', $fta_identity_id)->pluck('id')->toArray();
        $submittedNodeIds = array_filter(array_column($request->nodes, 'id'));
        $nodesToDelete = array_diff($existingNodeIds, $submittedNodeIds);
        FtaNode::whereIn('id', $nodesToDelete)->delete();

        foreach ($request->nodes as $nodeData) {
            $data = [
                'type' => $nodeData['node_type'],
                'event_name' => $nodeData['event_name'] ?? null,
                'parent_id' => $nodeData['parent_id'] ?? null,
                'fta_identity_id' => $ftaIdentity->id,
            ];

            if ($nodeData['node_type'] === 'basic_event') {
                $data['fta_event_id'] = $nodeData['fta_event_id'] ?? null;
                $data['event_name'] = null;
            } else {
                $data['fta_event_id'] = null;
            }

            FtaNode::updateOrCreate(
                ['id' => $nodeData['id'] ?? null, 'fta_identity_id' => $ftaIdentity->id],
                $data
            );
        }

        return redirect()->route('fta.index')->with('success', 'Node tree updated successfully.');
    }

    public function editEvents($ftaidentity_id)
    {
        $ftaIdentity = FtaIdentity::findOrFail($ftaidentity_id);
        $events = FtaEvent::where('fta_identity_id', $ftaidentity_id)->with('fmecaItem')->get();

        $usedFmecaIds = $events->pluck('fmeca_item_id')->toArray();
        $availableFmecaItems = FmecaItem::whereNotNull('failure_mode')->whereRaw('LENGTH(failure_mode) >= 3')->where(function ($query) use ($usedFmecaIds) {
            $query->whereDoesntHave('ftaEvents')
                ->orWhereIn('id', $usedFmecaIds);
        })->get();

        return view('fta.edit-events', compact('ftaIdentity', 'events', 'availableFmecaItems'));
    }

    public function updateEvents(Request $request, $ftaidentity_id)
    {
        $ftaIdentity = FtaIdentity::findOrFail($ftaidentity_id);

        $validator = Validator::make($request->all(), [
            'events' => 'required|array',
            'events.*.id' => 'nullable|exists:fta_events,id',
            'events.*.fmeca_item_id' => 'required|exists:fmeca_items,id',
            'events.*.name' => 'required|string|max:255',
            'events.*.source' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $existingEventIds = FtaEvent::where('fta_identity_id', $ftaidentity_id)->pluck('id')->toArray();
        $submittedEventIds = array_filter(array_column($request->events, 'id'));
        $eventsToDelete = array_diff($existingEventIds, $submittedEventIds);
        FtaEvent::whereIn('id', $eventsToDelete)->delete();

        foreach ($request->events as $eventData) {
            $fmecaItem = FmecaItem::findOrFail($eventData['fmeca_item_id']);
            FtaEvent::updateOrCreate(
                ['id' => $eventData['id'] ?? null, 'fta_identity_id' => $ftaIdentity->id],
                [
                    'fmeca_item_id' => $eventData['fmeca_item_id'],
                    'name' => $eventData['name'],
                    'failure_rate' => $fmecaItem->failure_rate,
                    'source' => $eventData['source'] ?? $fmecaItem->reference,
                ]
            );
        }

        return redirect()->route('fta.index')->with('success', 'Event list updated successfully.');
    }

    public function calculateCFIAndStore(Request $request, $ftaidentity_id)
    {
        $ftaIdentity = FtaIdentity::findOrFail($ftaidentity_id);
        $root = FtaNode::with(['ftaEvent.fmecaItem', 'children'])
            ->whereNull('parent_id')
            ->where('fta_identity_id', $ftaidentity_id)
            ->first();

        if (!$root) {
            return redirect()->route('fta.index')->with('error', 'No root node found for this FTA Identity.');
        }

        $cfi = $this->calculateCombinedFailureRate($root);

        $ftaIdentity->cfi = $cfi;
        $ftaIdentity->save();

        return redirect()->route('fta.index')->with('success', 'Conditional Failure Intensity calculated and stored successfully.');
    }

    public function index(Request $request)
    {
        $yourauth = auth()->user();
        $projectTypes = ProjectType::all();

        $query = FtaIdentity::with('projectType');
        if ($request->has('project_filter') && $request->project_filter) {
            $query->where('proyek_type_id', $request->project_filter);
        }
        $ftaIdentities = $query->get();

        return view('fta.index', compact('ftaIdentities', 'projectTypes', 'yourauth'));
    }

    public function json(Request $request)
    {
        $query = FtaIdentity::with('projectType');
        if ($request->has('project_filter') && $request->project_filter) {
            $query->where('proyek_type_id', $request->project_filter);
        }
        $ftaIdentities = $query->get();

        return response()->json([
            'ftaIdentities' => $ftaIdentities->map(function ($fta) {
                return [
                    'id' => $fta->id,
                    'componentname' => $fta->componentname,
                    'project_type' => $fta->projectType ? ['id' => $fta->projectType->id, 'title' => $fta->projectType->title] : null,
                    'cfi' => $fta->cfi,
                ];
            }),
            'yourauth_id' => auth()->user()->id,
            'routes' => [
                'project' => route('fta.project', ['fta_identity_id' => ':fta_identity_id']),
                'calculate' => route('fta.calculate', ['fta_identity_id' => ':fta_identity_id']),
                'nodes_edit' => route('fta.nodes.edit', ['fta_identity_id' => ':fta_identity_id']),
                'events_edit' => route('fta.events.edit', ['fta_identity_id' => ':fta_identity_id']),
                'destroy' => route('fta.destroy', ['fta_identity_id' => ':fta_identity_id']),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'componentname' => 'required|string|max:255',
            'proyek_type_id' => 'nullable|exists:project_types,id',
            'time_interval' => 'nullable|integer|min:1',
            'cfi' => 'nullable|numeric',
            'diagram_url' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        FtaIdentity::create([
            'componentname' => $request->componentname,
            'proyek_type_id' => $request->proyek_type_id,
            'cfi' => $request->cfi,
            'diagram_url' => $request->diagram_url,
        ]);

        return redirect()->route('fta.index')->with('success', 'FTA Identity created successfully.');
    }

    public function update(Request $request, FtaIdentity $ftaIdentity)
    {
        $validator = Validator::make($request->all(), [
            'componentname' => 'required|string|max:255',
            'proyek_type_id' => 'nullable|exists:project_types,id',
            'time_interval' => 'nullable|integer|min:1',
            'cfi' => 'nullable|numeric',
            'diagram_url' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $ftaIdentity->update([
            'componentname' => $request->componentname,
            'proyek_type_id' => $request->proyek_type_id,
            'cfi' => $request->cfi,
            'diagram_url' => $request->diagram_url,
        ]);

        return redirect()->route('fta.index')->with('success', 'FTA Identity updated successfully.');
    }

    public function destroy(FtaIdentity $ftaidentity)
    {
        $ftaidentity->ftaEvents()->delete();
        $ftaidentity->delete();
        return redirect()->route('fta.index')->with('success', 'FTA Identity deleted successfully.');
    }

    public function project(Request $request, $fta_identity_id)
    {
        $roots = FtaNode::with('ftaEvent.fmecaItem')
            ->whereNull('parent_id')
            ->where('fta_identity_id', $fta_identity_id)
            ->first();

        $events = FtaEvent::whereIn('id', FtaNode::where('fta_identity_id', $fta_identity_id)
            ->whereNotNull('fta_event_id')
            ->pluck('fta_event_id'))
            ->with('fmecaItem')
            ->get();

        if (!$roots) {
            Log::warning('No root node found for fta_identity_id', ['fta_identity_id' => $fta_identity_id]);
            return view('fta.visual', [
                'diagram' => null,
                'cfi' => null,
                't' => null,
                'ftaIdentity' => FtaIdentity::find($fta_identity_id),
                'roots' => null,
                'message' => 'No root node found.'
            ]);
        }

        Log::info('Root node loaded', [
            'root_id' => $roots->id,
            'type' => $roots->type,
            'event_name' => $roots->event_name,
            'fta_event_id' => $roots->fta_event_id
        ]);

        $this->loadFullTree($roots);

        $ftaIdentity = FtaIdentity::find($fta_identity_id);
        $cfi = $this->calculateCombinedFailureRate($roots);
        $diagram = $this->buildMermaidWithCFI($roots);

        Log::info('Generated diagram', ['diagram' => $diagram]);

        return view('fta.visual', compact('diagram', 'cfi', 'ftaIdentity', 'roots', 'events'));
    }

    private function loadFullTree(FtaNode $node)
    {
        if (!($node instanceof \App\Models\FtaNode)) {
            Log::error('loadFullTree received non-FtaNode', ['type' => get_class($node)]);
            throw new \InvalidArgumentException('Expected FtaNode, received ' . get_class($node));
        }

        $children = FtaNode::with('ftaEvent.fmecaItem')
            ->where('parent_id', $node->id)
            ->where('fta_identity_id', $node->fta_identity_id)
            ->get();

        Log::info('Loaded children for node', [
            'node_id' => $node->id,
            'type' => $node->type,
            'children_count' => $children->count(),
            'children_ids' => $children->pluck('id')->toArray()
        ]);

        $node->setRelation('children', $children);

        foreach ($children as $child) {
            if ($child instanceof \App\Models\FtaNode) {
                $this->loadFullTree($child);
            } else {
                Log::error('Non-FtaNode child detected in loadFullTree', [
                    'child_type' => get_class($child),
                    'parent_node_id' => $node->id
                ]);
            }
        }
    }

    private function buildMermaidWithCFI(FtaNode $node): string
    {
        if (!($node instanceof \App\Models\FtaNode)) {
            Log::error('buildMermaidWithCFI received non-FtaNode', ['type' => get_class($node)]);
            throw new \InvalidArgumentException('Expected FtaNode, received ' . get_class($node));
        }

        $cfi = $this->calculateCombinedFailureRate($node);

        if ($node->type === 'basic_event') {
            $event = $node->ftaEvent;
            $name = addslashes($event->name ?? 'N/A');
            $source = !empty($event->source) ? addslashes($event->source) : 'No source';
            Log::info('Building basic_event node', [
                'node_id' => $node->id,
                'name' => $name,
                'lambda' => $cfi,
                'source' => $source
            ]);
            return "BE{$node->id}[\"{$name}\\nSource: {$source}\\nλ={$cfi}\"]";
        }

        $name = addslashes($node->event_name ?? ucfirst($node->type) . ' Node ' . $node->id);
        $label = ucfirst($node->type) . ": {$name}";
        $combinedLabel = ($node->type === 'and' || $node->type === 'or') ? "\\nλ={$cfi}" : "";
        $diagram = '';

        if (!($node->children instanceof \Illuminate\Database\Eloquent\Collection)) {
            Log::warning('Children relation is not a Collection in buildMermaidWithCFI', [
                'node_id' => $node->id,
                'children_type' => get_class($node->children ?? 'null')
            ]);
            $node->load('children.ftaEvent.fmecaItem');
        }

        if ($node->type === 'and') {
            $diagram .= "AND{$node->id}{{\"{$label}{$combinedLabel}\"}}";
            foreach ($node->children as $child) {
                $childDiagram = $this->buildMermaidWithCFI($child);
                $childLabel = strtok($childDiagram, "[{");
                $diagram .= "\nAND{$node->id} --> $childLabel";
                $diagram .= "\n" . $childDiagram;
            }
        } elseif ($node->type === 'or') {
            $diagram .= "OR{$node->id}{{\"{$label}{$combinedLabel}\"}}";
            foreach ($node->children as $child) {
                $childDiagram = $this->buildMermaidWithCFI($child);
                $childLabel = strtok($childDiagram, "[{");
                $diagram .= "\nOR{$node->id} --> $childLabel";
                $diagram .= "\n" . $childDiagram;
            }
        }

        Log::info('Built node diagram', ['node_id' => $node->id, 'type' => $node->type, 'children_count' => $node->children->count(), 'diagram' => $diagram]);
        return $diagram;
    }

    private function calculateCombinedFailureRate(FtaNode $node): float
    {
        if (!($node instanceof \App\Models\FtaNode)) {
            Log::error('calculateCombinedFailureRate received non-FtaNode', ['type' => get_class($node)]);
            throw new \InvalidArgumentException('Expected FtaNode, received ' . get_class($node));
        }

        if ($node->type === 'basic_event') {
            $lambda = (float) ($node->ftaEvent->failure_rate ?? 0);
            Log::info('Returning failure rate for basic_event', [
                'node_id' => $node->id,
                'lambda' => $lambda
            ]);
            return $lambda;
        }

        if (!($node->children instanceof \Illuminate\Database\Eloquent\Collection)) {
            Log::warning('Children relation is not a Collection', [
                'node_id' => $node->id,
                'children_type' => get_class($node->children ?? 'null')
            ]);
            $node->load('children.ftaEvent.fmecaItem');
        }

        $childFailureRates = $node->children->map(function ($child) {
            return $this->calculateCombinedFailureRate($child);
        })->filter(function ($rate) {
            return $rate > 0; // Filter out zero or invalid rates
        });

        if ($childFailureRates->isEmpty()) {
            Log::warning('No valid child failure rates for node', ['node_id' => $node->id]);
            return 0.0;
        }

        if ($node->type === 'and') {
            $combinedLambda = $childFailureRates->reduce(function ($carry, $lambda) {
                return $carry * $lambda;
            }, 1.0);
            Log::info('Calculated AND gate failure rate', [
                'node_id' => $node->id,
                'combined_lambda' => $combinedLambda
            ]);
            return $combinedLambda;
        }

        if ($node->type === 'or') {
            $combinedLambda = $childFailureRates->sum();
            Log::info('Calculated OR gate failure rate', [
                'node_id' => $node->id,
                'combined_lambda' => $combinedLambda
            ]);
            return $combinedLambda;
        }

        Log::warning('Unknown node type', [
            'node_id' => $node->id,
            'type' => $node->type
        ]);
        return 0.0;
    }
}
