<?php

namespace App\Http\Controllers;

use App\Models\ProjectType;
use App\Models\RbdBlock;
use App\Models\RbdIdentity;
use App\Models\RbdNode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\NewRbdInstance;
use App\Models\NewRbdNode;
use App\Models\NewRbdLink;

class RbdController extends Controller
{
    /**
     * Show the form for editing the node list.
     */
    public function editNodes($rbdidentity_id)
    {
        $rbdIdentity = RbdIdentity::with('rbdBlocks')->findOrFail($rbdidentity_id);
        $nodes = RbdNode::where('rbdidentity_id', $rbdidentity_id)->get();

        return view('rbd.edit-nodes', compact('rbdIdentity', 'nodes'));
    }

    public function updateNodes(Request $request, $rbdidentity_id)
    {
        $rbdIdentity = RbdIdentity::findOrFail($rbdidentity_id);
        $validBlockIds = $rbdIdentity->rbdBlocks()->pluck('id')->toArray();

        $validator = Validator::make($request->all(), [
            'nodes' => 'required|array',
            'nodes.*.id' => 'nullable|exists:rbd_nodes,id',
            'nodes.*.node_type' => 'required|in:series,parallel,k-out-of-n,block',
            'nodes.*.parent_id' => 'nullable|exists:rbd_nodes,id',
            'nodes.*.block_id' => [
                'required_if:nodes.*.node_type,block',
                function ($attribute, $value, $fail) use ($validBlockIds) {
                    if (!empty($value) && !in_array($value, $validBlockIds)) {
                        $fail("The selected {$attribute} is invalid or not associated with this RBD Identity.");
                    }
                },
            ],
            'nodes.*.block_group_type' => 'required_if:nodes.*.node_type,block|in:single,series,parallel,k-out-of-n',
            'nodes.*.block_count' => [
                'exclude_if:nodes.*.node_type,series,parallel,k-out-of-n',
                'exclude_if:nodes.*.block_group_type,single',
                'required_if:nodes.*.block_group_type,series,parallel,k-out-of-n',
                'integer',
                'min:1'
            ],
            'nodes.*.k_value' => [
                'exclude_unless:nodes.*.node_type,k-out-of-n',
                'exclude_unless:nodes.*.block_group_type,k-out-of-n',
                'required_if:nodes.*.node_type,k-out-of-n',
                'required_if:nodes.*.block_group_type,k-out-of-n',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $blockGroupType = $request->input("nodes.$index.block_group_type");
                    $blockCount = $request->input("nodes.$index.block_count");
                    if ($blockGroupType == 'k-out-of-n' && $value > $blockCount) {
                        $fail("The $attribute must not be greater than Block Count.");
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $existingNodeIds = RbdNode::where('rbdidentity_id', $rbdidentity_id)->pluck('id')->toArray();
        $submittedNodeIds = array_filter(array_column($request->nodes, 'id'));
        $nodesToDelete = array_diff($existingNodeIds, $submittedNodeIds);
        RbdNode::whereIn('id', $nodesToDelete)->delete();

        foreach ($request->nodes as $nodeData) {
            $data = [
                'type' => $nodeData['node_type'],
                'parent_id' => $nodeData['parent_id'] ?? null,
                'rbdidentity_id' => $rbdIdentity->id,
            ];

            if ($nodeData['node_type'] === 'block') {
                $data['block_group_type'] = $nodeData['block_group_type'] ?? null;
                $data['rbd_block_id'] = $nodeData['block_id'] ?? null;
                $data['block_count'] = ($nodeData['block_group_type'] ?? 'single') !== 'single' ? ($nodeData['block_count'] ?? null) : null;
                if ($nodeData['block_group_type'] === 'k-out-of-n') {
                    $data['k_value'] = $nodeData['k_value'] ?? null;
                } else {
                    $data['k_value'] = null;
                }
            } elseif ($nodeData['node_type'] === 'k-out-of-n') {
                $data['k_value'] = $nodeData['k_value'] ?? null;
                $data['block_group_type'] = null;
                $data['rbd_block_id'] = null;
                $data['block_count'] = null;
            } else {
                $data['block_group_type'] = null;
                $data['rbd_block_id'] = null;
                $data['block_count'] = null;
                $data['k_value'] = null;
            }

            RbdNode::updateOrCreate(
                ['id' => $nodeData['id'] ?? null, 'rbdidentity_id' => $rbdIdentity->id],
                $data
            );
        }

        return redirect()->route('rbd.index')->with('success', 'Node list updated successfully.');
    }

    /**
     * Show the form for editing the block list.
     */
    public function editBlocks($rbdidentity_id)
    {
        $rbdIdentity = RbdIdentity::findOrFail($rbdidentity_id);
        $blocks = RbdBlock::where('rbdidentity_id', $rbdidentity_id)->get();

        return view('rbd.edit-blocks', compact('rbdIdentity', 'blocks'));
    }

    /**
     * Update the block list.
     */
    public function updateBlocks(Request $request, $rbdidentity)
    {
        $rbdIdentity = RbdIdentity::findOrFail($rbdidentity);

        $validator = Validator::make($request->all(), [
            'blocks' => 'required|array',
            'blocks.*.id' => 'nullable|exists:rbd_blocks,id',
            'blocks.*.block_name' => 'required|string|max:255',
            'blocks.*.lambda' => 'required|numeric|min:0',
            'blocks.*.source' => 'nullable|string|', // ✅ tambahkan ini
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $existingBlockIds = RbdBlock::where('rbdidentity_id', $rbdidentity)->pluck('id')->toArray();
        $submittedBlockIds = array_filter(array_column($request->blocks, 'id'));
        $blocksToDelete = array_diff($existingBlockIds, $submittedBlockIds);
        RbdBlock::whereIn('id', $blocksToDelete)->delete();

        foreach ($request->blocks as $blockData) {
            RbdBlock::updateOrCreate(
                ['id' => $blockData['id'] ?? null, 'rbdidentity_id' => $rbdIdentity->id],
                [
                    'name' => $blockData['block_name'],
                    'lambda' => $blockData['lambda'],
                    'source' => $blockData['source'] ?? null, // ✅ update source
                ]
            );
        }

        return redirect()->route('rbd.index')->with('success', 'Block list updated successfully.');
    }

    public function calculateReliabilityAndStore(Request $request, $rbdidentity_id)
    {
        $rbdIdentity = RbdIdentity::findOrFail($rbdidentity_id);
        $root = RbdNode::with(['rbdBlock.rbdIdentity', 'children'])
            ->whereNull('parent_id')
            ->where('rbdidentity_id', $rbdidentity_id)
            ->first();

        if (!$root) {
            return redirect()->route('rbd.index')->with('error', 'No root node found for this RBD Identity.');
        }

        $t = (float) ($rbdIdentity->time_interval ?? 1000);
        $reliability = $this->calculateReliability($root, $t);

        $rbdIdentity->temporary_reliability_value = $reliability;
        $rbdIdentity->save();

        return redirect()->route('rbd.index')->with('success', 'Reliability calculated and stored successfully.');
    }

    public function index(Request $request)
    {
        $yourauth = auth()->user();
        $projectTypes = ProjectType::all();

        $query = RbdIdentity::with('projectType');
        if ($request->has('project_filter') && $request->project_filter) {
            $query->where('proyek_type_id', $request->project_filter);
        }
        $rbdIdentities = $query->get();

        return view('rbd.index', compact('rbdIdentities', 'projectTypes', 'yourauth'));
    }

    public function json(Request $request)
    {
        $query = RbdIdentity::with('projectType');
        if ($request->has('project_filter') && $request->project_filter) {
            $query->where('proyek_type_id', $request->project_filter);
        }
        $rbdIdentities = $query->get();

        return response()->json([
            'rbdIdentities' => $rbdIdentities->map(function ($rbd) {
                return [
                    'id' => $rbd->id,
                    'componentname' => $rbd->componentname,
                    'project_type' => $rbd->projectType ? ['id' => $rbd->projectType->id, 'title' => $rbd->projectType->title] : null,
                    'time_interval' => $rbd->time_interval,
                    'temporary_reliability_value' => $rbd->temporary_reliability_value,
                ];
            }),
            'yourauth_id' => auth()->user()->id,
            'routes' => [
                'project' => route('rbd.project', ['rbdidentity_id' => ':rbdidentity_id']),
                'calculate' => route('rbd.calculate', ['rbdidentity_id' => ':rbdidentity_id']),
                'nodes_edit' => route('rbd.nodes.edit', ['rbdidentity_id' => ':rbdidentity_id']),
                'blocks_edit' => route('rbd.blocks.edit', ['rbdidentity_id' => ':rbdidentity_id']),
                'destroy' => route('rbd.destroy', ['rbdidentity_id' => ':rbdidentity_id']),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'componentname' => 'required|string|max:255',
            'proyek_type_id' => 'nullable|exists:project_types,id',
            'time_interval' => 'nullable|integer|min:1',
            'temporary_reliability_value' => 'nullable|numeric',
            'diagram_url' => 'nullable|url|max:255', // ✅ validasi url
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        RbdIdentity::create([
            'componentname' => $request->componentname,
            'proyek_type_id' => $request->proyek_type_id,
            'time_interval' => $request->time_interval ?? 1000,
            'temporary_reliability_value' => $request->temporary_reliability_value,
            'diagram_url' => $request->diagram_url, // ✅ simpan url
        ]);

        return redirect()->route('rbd.index')->with('success', 'RBD Identity created successfully.');
    }

    public function update(Request $request, RbdIdentity $rbdIdentity)
    {
        $validator = Validator::make($request->all(), [
            'componentname' => 'required|string|max:255',
            'proyek_type_id' => 'nullable|exists:project_types,id',
            'time_interval' => 'nullable|integer|min:1',
            'temporary_reliability_value' => 'nullable|numeric',
            'diagram_url' => 'nullable|url|max:255', // ✅ validasi url
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $rbdIdentity->update([
            'componentname' => $request->componentname,
            'proyek_type_id' => $request->proyek_type_id,
            'time_interval' => $request->time_interval ?? 1000,
            'temporary_reliability_value' => $request->temporary_reliability_value,
            'diagram_url' => $request->diagram_url, // ✅ update url
        ]);

        return redirect()->route('rbd.index')->with('success', 'RBD Identity updated successfully.');
    }


    public function destroy(RbdIdentity $rbdidentity_id)
    {
        $rbdidentity_id->rbdBlocks()->delete();
        $rbdidentity_id->delete();
        return redirect()->route('rbd.index')->with('success', 'RBD Identity deleted successfully.');
    }

    public function project(Request $request, $rbdidentity_id)
    {
        $roots = RbdNode::with(['rbdBlock.rbdIdentity', 'children'])
            ->whereNull('parent_id')
            ->where('rbdidentity_id', $rbdidentity_id)
            ->first();

        $blocks = RbdBlock::whereIn('id', RbdNode::where('rbdidentity_id', $rbdidentity_id)
            ->whereNotNull('rbd_block_id')
            ->pluck('rbd_block_id'))
            ->get();
        if (!$roots) {
            return view('rbd.visual', [
                'diagram' => null,
                'reliability' => null,
                't' => null,
                'rbdIdentity' => RbdIdentity::find($rbdidentity_id),
                'roots' => null,
                'message' => 'No root node found.'
            ]);
        }

        $rbdIdentity = RbdIdentity::find($rbdidentity_id);
        $defaultT = $rbdIdentity->time_interval ?? 1000;
        $t = (float) $request->input('t', $defaultT);

        $reliability = $this->calculateReliability($roots, $t);
        $diagram = $this->buildMermaidWithReliability($roots, $t);

        return view('rbd.visual', compact('diagram', 'reliability', 't', 'rbdIdentity', 'roots', 'blocks'));
    }

    private function calculateReliability(RbdNode $node, float $t): float
    {
        if ($node->type === 'block') {
            $block = $node->rbdBlock;
            $lambda = $block->lambda ?? 0;

            if ($node->block_group_type === 'single' || !$node->block_group_type) {
                return exp(-$lambda * $t);
            }

            $n = $node->block_count ?? 1;

            if ($node->block_group_type === 'series') {
                return pow(exp(-$lambda * $t), $n);
            }

            if ($node->block_group_type === 'parallel') {
                return 1 - pow(1 - exp(-$lambda * $t), $n);
            }

            if ($node->block_group_type === 'k-out-of-n') {
                $k = $node->k_value ?? 1;
                if ($k > $n || $k <= 0) {
                    return 0;
                }
                $reliability = exp(-$lambda * $t);
                $result = 0;
                for ($i = $k; $i <= $n; $i++) {
                    $result += $this->binomialCoefficient($n, $i) *
                        pow($reliability, $i) *
                        pow(1 - $reliability, $n - $i);
                }
                return $result;
            }
        }

        if ($node->type === 'series') {
            $result = 1;
            foreach ($node->children as $child) {
                $result *= $this->calculateReliability($child, $t);
            }
            return $result;
        }

        if ($node->type === 'parallel') {
            $result = 1;
            foreach ($node->children as $child) {
                $result *= (1 - $this->calculateReliability($child, $t));
            }
            return 1 - $result;
        }

        if ($node->type === 'k-out-of-n') {
            $children = $node->children;
            $n = count($children);
            $k = $node->k_value;

            if ($k > $n || $k <= 0) {
                return 0;
            }

            $reliability = $this->calculateReliability($children[0], $t);
            $result = 0;
            for ($i = $k; $i <= $n; $i++) {
                $result += $this->binomialCoefficient($n, $i) *
                    pow($reliability, $i) *
                    pow(1 - $reliability, $n - $i);
            }
            return $result;
        }

        return 1;
    }

    private function binomialCoefficient(int $n, int $k): float
    {
        if ($k < 0 || $k > $n) {
            return 0;
        }
        $k = min($k, $n - $k);
        $coefficient = 1;
        for ($i = 0; $i < $k; $i++) {
            $coefficient *= ($n - $i) / ($i + 1);
        }
        return $coefficient;
    }

    private function buildMermaidWithReliability(RbdNode $node, float $t): string
    {
        if ($node->type === 'block') {
            $r = $this->calculateReliability($node, $t);
            $block = $node->rbdBlock;
            $name = addslashes($block->name); // Escape special characters
            $lambda = $block->lambda ?? 0;
            $r = number_format($r, 6);
            $source = !empty($block->source) ? addslashes($block->source) : 'No source'; // Include source

            if ($node->block_group_type === 'single' || !$node->block_group_type) {
                return "block{$node->id}[\"{$name}\\nSource: {$source}\\nλ={$lambda}\\nR={$r}\"]";
            }

            $label = $node->block_group_type === 'k-out-of-n'
                ? "{$name}\\nSource: {$source}\\n{$node->block_group_type} (k={$node->k_value}, n={$node->block_count})\\nλ={$lambda}\\nR={$r}"
                : "{$name}\\nSource: {$source}\\n{$node->block_group_type} (n={$node->block_count})\\nλ={$lambda}\\nR={$r}";

            return "block{$node->id}[\"{$label}\"]";
        }

        $diagram = '';

        if ($node->type === 'series') {
            $nodeR = $this->calculateReliability($node, $t);
            $diagram .= "S{$node->id}((Series R={$nodeR}))\n";
            foreach ($node->children as $child) {
                $childDiagram = $this->buildMermaidWithReliability($child, $t);
                $childLabel = strtok($childDiagram, "[(");
                $diagram .= "S{$node->id} --> $childLabel\n";
                $diagram .= $childDiagram . "\n";
            }
        } elseif ($node->type === 'parallel') {
            $nodeR = $this->calculateReliability($node, $t);
            $diagram .= "P{$node->id}((Parallel R={$nodeR}))\n";
            foreach ($node->children as $child) {
                $childDiagram = $this->buildMermaidWithReliability($child, $t);
                $childLabel = strtok($childDiagram, "[(");
                $diagram .= "P{$node->id} --> $childLabel\n";
                $diagram .= $childDiagram . "\n";
            }
        } elseif ($node->type === 'k-out-of-n') {
            $nodeR = $this->calculateReliability($node, $t);
            $n = count($node->children);
            $k = $node->k_value;
            $diagram .= "K{$node->id}(({$k}-out-of-{$n} R={$nodeR}))\n";
            foreach ($node->children as $child) {
                $childDiagram = $this->buildMermaidWithReliability($child, $t);
                $childLabel = strtok($childDiagram, "[(");
                $diagram .= "K{$node->id} --> $childLabel\n";
                $diagram .= $childDiagram . "\n";
            }
        }

        return $diagram;
    }










    private function getCombinations($array, $k, $n)
    {
        $result = [];
        $this->combine($array, 0, [], $k, $n, $result);
        return $result;
    }

    private function combine($array, $start, $current, $k, $n, &$result)
    {
        if (count($current) == $n) {
            if (array_sum(array_map(fn($x) => $x !== null, $current)) >= $k) {
                $result[] = $current;
            }
            return;
        }

        for ($i = $start; $i < count($array); $i++) {
            $this->combine($array, $i + 1, array_merge($current, [$array[$i]]), $k, $n, $result);
            $this->combine($array, $i + 1, array_merge($current, [null]), $k, $n, $result);
        }
    }

    private function getReliability($key, $nodes, $links, &$reliabilities, $timeInterval)
    {
        if (isset($reliabilities[$key])) {
            return $reliabilities[$key];
        }

        $node = $nodes->firstWhere('key_value', $key);
        if (!$node) {
            throw new \Exception("Node with key '$key' not found in nodes.");
        }

        $childrenKeys = $links->where('from_key', $key)->pluck('to_key')->toArray();
        $childRs = array_map(fn($childKey) => $this->getReliability($childKey, $nodes, $links, $reliabilities, $timeInterval), $childrenKeys);

        if ($node->category === 'start' || $node->category === 'end') {
            if (count($childRs) === 0) {
                $reliabilities[$key] = $node->reliability ?? 1;
                return $reliabilities[$key];
            }
            $R_series = array_product($childRs);
            $reliabilities[$key] = $R_series;
            return $R_series;
        }

        if ($node->category === 'junction') {
            $k = $node->k ?? 1;
            $n = $node->n ?? count($childRs);
            $R_parallel = 0;

            if ($k <= $n && $n === count($childRs)) {
                $combinations = $this->getCombinations(array_keys($childRs), $k, $n);
                foreach ($combinations as $combo) {
                    $term = 1;
                    foreach ($combo as $i => $index) {
                        $term = bcmul($term, ($index !== null && $i < $k) ? $childRs[$index] : (1 - ($index !== null ? $childRs[$index] : 0)), 20);
                    }
                    $R_parallel = bcadd($R_parallel, $term, 20);
                }
            } else {
                $R_parallel = 1 - array_product(array_map(fn($r) => 1 - $r, $childRs));
            }

            $reliabilities[$key] = $R_parallel;
            return $R_parallel;
        }

        // Calculate reliability dynamically if failure_rate_id exists
        $reliability = $node->reliability ?? 1;
        if ($node->failure_rate_id && $node->failureRate) {
            $exponent = - ($node->failureRate->failure_rate * $timeInterval);
            $reliability = sprintf('%.20f', exp($exponent));
            $node->update(['reliability' => $reliability]);
        }

        $R_series = bcmul(array_product($childRs), $reliability, 20);
        $reliabilities[$key] = $R_series;
        return $R_series;
    }

    public function ujicoba()
    {
        $rbdInstance = NewRbdInstance::where('componentname', 'Sample RBD')->first();
        if (!$rbdInstance) {
            throw new \Exception('RBD instance not found.');
        }

        $nodes = NewRbdNode::where('rbd_instance_id', $rbdInstance->id)
            ->with('failureRate')
            ->get();
        $links = NewRbdLink::where('rbd_instance_id', $rbdInstance->id)->get();

        // Map linkDataArray to use 'from' and 'to' for GoJS
        $linkDataArray = $links->map(function ($link) {
            return [
                'from' => $link->from_key,
                'to' => $link->to_key,
            ];
        })->toArray();



        $reliabilities = [];
        $R_system = $this->getReliability('start', $nodes, $links, $reliabilities, $rbdInstance->time_interval);

        // Update temporary_reliability_value
        $rbdInstance->update(['temporary_reliability_value' => $R_system]);

        return view('rbd.ujicoba', [
            'data' => [
                'nodeDataArray' => $nodes->toArray(),
                'linkDataArray' => $linkDataArray,
            ],
            'systemReliability' => number_format($R_system, 6),
        ]);
    }
}
