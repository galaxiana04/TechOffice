<?php

namespace App\Http\Controllers;

use App\Models\NewRbdFailureRate;
use App\Models\NewRbdInstance;
use App\Models\NewRbdNode;
use App\Models\NewRbdLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\ProjectType;
use App\Models\NewRbdModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class NewRbdController extends Controller
{


    public function updateNodePositions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rbd_instance_id' => 'required|exists:new_rbd_instances,id',
            'nodes' => 'required|array|min:1',
            'nodes.*.key_value' => [
                'required',
                'string',
                Rule::exists('new_rbd_nodes', 'key_value')->where(function ($query) use ($request) {
                    $query->where('rbd_instance_id', $request->rbd_instance_id);
                }),
            ],
            'nodes.*.x' => 'required|integer',
            'nodes.*.y' => 'required|integer',
        ]);

        if ($validator->fails()) {
            Log::error('Node Position Update Validation Failed', ['instance_id' => $request->rbd_instance_id, 'errors' => $validator->errors()]);
            return response()->json(['error' => $validator->errors()], 422);
        }

        $rbdInstanceId = $request->rbd_instance_id;
        $nodes = $request->nodes;

        DB::beginTransaction();
        try {
            $updatedNodes = [];
            foreach ($nodes as $nodeData) {
                $node = NewRbdNode::where('rbd_instance_id', $rbdInstanceId)
                    ->where('key_value', $nodeData['key_value'])
                    ->first();
                if ($node) {
                    $node->x = $nodeData['x'];
                    $node->y = $nodeData['y'];
                    $node->save();
                    $updatedNodes[] = $node->key_value;
                }
            }
            Log::info('Node Positions Updated', ['instance_id' => $rbdInstanceId, 'updated_nodes' => $updatedNodes]);
            DB::commit();
            return response()->json(['success' => 'Node positions updated']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Node Position Update Failed', ['instance_id' => $rbdInstanceId, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to update node positions'], 500);
        }
    }
    public function newrbdmodelindex()
    {
        $models = NewRbdModel::all();
        return view('newrbd.index', compact('models'));
    }

    private function generateUniqueComponentName($modelId, $baseName)
    {
        $name = $baseName;
        $counter = 1;

        while (NewRbdInstance::where('new_rbd_model_id', $modelId)
            ->where('componentname', $name)
            ->exists()
        ) {
            $name = "{$baseName} (Copy {$counter})";
            $counter++;
        }

        return $name;
    }

    private function generateUniqueFailureRateName($modelId, $baseName)
    {
        $name = $baseName;
        $counter = 1;

        // HANYA CEK NAMA – TIDAK PEDULI failure_rate atau source
        while (
            NewRbdFailureRate::where('new_rbd_model_id', $modelId)
            ->where('name', $name)
            ->exists()
        ) {
            $name = "{$baseName} (Copy {$counter})";
            $counter++;
        }

        return $name;
    }

    public function duplicateModel(Request $request, $id)
    {
        Log::info('=== DUPLIKASI MODEL DIMULAI ===', ['original_model_id' => $id]);

        // 1. Load model + semua relasi
        $originalModel = NewRbdModel::with([
            'failureRates',
            'instances.nodes',
            'instances.links'
        ])->findOrFail($id);

        Log::info('Original Model Loaded', [
            'id' => $originalModel->id,
            'name' => $originalModel->name,
            'failure_rates_count' => $originalModel->failureRates->count(),
            'instances_count' => $originalModel->instances->count(),
        ]);

        // 2. Validasi
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:new_rbd_models,name',
            'description' => 'nullable|string',
            'duplicate_failure_rates' => 'sometimes|in:1,0,true,false',
        ]);

        if ($validator->fails()) {
            Log::warning('Validasi Gagal', ['errors' => $validator->errors()->toArray()]);
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();
        try {
            // === 1. DUPLIKASI MODEL ===
            $newModel = $originalModel->replicate();
            $newModel->name = $request->name;
            $newModel->description = $request->description ?? $originalModel->description;
            $newModel->save();

            Log::info('Model Baru Dibuat', [
                'new_model_id' => $newModel->id,
                'new_name' => $newModel->name
            ]);


            // === 2. DUPLIKASI FAILURE RATES (SIMPLES & AMAN) ===
            $newFailureRateMap = [];
            if ($request->boolean('duplicate_failure_rates') && $originalModel->failureRates->isNotEmpty()) {

                // HAPUS DULU (opsional, tapi aman)
                DB::table('new_rbd_failure_rates')
                    ->where('new_rbd_model_id', $newModel->id)
                    ->delete();

                Log::info('Hapus semua failure rates di model baru', ['model_id' => $newModel->id]);

                foreach ($originalModel->failureRates as $fr) {
                    $baseName = trim($fr->name);
                    $source = trim($fr->source ?? '');
                    $rate = $fr->failure_rate;

                    // PAKAI FUNGSI BARU → HANYA CEK NAMA
                    $finalName = $this->generateUniqueFailureRateName($newModel->id, $baseName);

                    DB::insert("
                        INSERT INTO new_rbd_failure_rates 
                        (new_rbd_model_id, name, failure_rate, source, created_at, updated_at)
                        VALUES (?, ?, ?, ?, NOW(), NOW())
                    ", [$newModel->id, $finalName, $rate, $source . " duplikat"]);

                    $newFailureRateMap[$fr->id] = DB::getPdo()->lastInsertId();

                    Log::info('Failure Rate Diduplikasi', [
                        'original_name' => $baseName,
                        'final_name' => $finalName,
                        'rate' => $rate
                    ]);
                }
            }
            // === 3. DUPLIKASI INSTANCES + NODES + LINKS ===
            $instanceMap = [];
            $keyMap = [];

            if ($originalModel->instances->isNotEmpty()) {
                foreach ($originalModel->instances as $instance) {
                    $newInstance = $instance->replicate();
                    $newInstance->new_rbd_model_id = $newModel->id;
                    $newInstance->componentname = $this->generateUniqueComponentName($newModel->id, $instance->componentname);
                    $newInstance->save();

                    $instanceMap[$instance->id] = $newInstance->id;

                    Log::info('Instance Diduplikasi', [
                        'original_id' => $instance->id,
                        'new_id' => $newInstance->id,
                        'componentname' => $newInstance->componentname
                    ]);

                    // Nodes
                    if ($instance->nodes->isNotEmpty()) {
                        foreach ($instance->nodes as $node) {
                            $newNode = $node->replicate();
                            $newNode->rbd_instance_id = $newInstance->id;
                            if ($node->failure_rate_id && isset($newFailureRateMap[$node->failure_rate_id])) {
                                $newNode->failure_rate_id = $newFailureRateMap[$node->failure_rate_id];
                            }
                            $newNode->save();

                            $keyMap[$node->key_value] = $newNode->key_value;
                        }
                    }

                    // Links
                    if ($instance->links->isNotEmpty()) {
                        foreach ($instance->links as $link) {
                            $newLink = $link->replicate();
                            $newLink->rbd_instance_id = $newInstance->id;
                            $newLink->from_node_id = $keyMap[$link->from_node_id] ?? $link->from_node_id;
                            $newLink->to_node_id = $keyMap[$link->to_node_id] ?? $link->to_node_id;
                            $newLink->save();
                        }
                    }

                    // Update foreign_instance_id
                    if ($newInstance->nodes->isNotEmpty()) {
                        foreach ($newInstance->nodes as $node) {
                            if ($node->foreign_instance_id && isset($instanceMap[$node->foreign_instance_id])) {
                                $node->foreign_instance_id = $instanceMap[$node->foreign_instance_id];
                                $node->save();
                            }
                        }
                    }
                }
            }

            DB::commit();
            Log::info('=== DUPLIKASI SELESAI BERHASIL ===', ['new_model_id' => $newModel->id]);

            return response()->json([
                'success' => "RBD Model '{$newModel->name}' berhasil diduplikasi."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('=== DUPLIKASI GAGAL ===', [
                'original_id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Gagal menduplikasi model: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeFailureRate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_rbd_model_id' => 'required|exists:new_rbd_models,id',
            'name' => 'required|string|max:255',
            'failure_rate' => 'required|regex:/^[0-9]+(\.[0-9]+)?([eE][-+]?[0-9]+)?$/',
            'source' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        try {
            $name = trim($request->name);
            $source = trim($request->source ?? '');
            $rate = $request->failure_rate; // string

            // Cek unik
            $exists = NewRbdFailureRate::where('new_rbd_model_id', $request->new_rbd_model_id)
                ->where('name', $name)
                ->whereRaw('failure_rate = CAST(? AS DECIMAL(30,30))', [$rate])
                ->whereRaw('TRIM(source) = ?', [$source])
                ->exists();

            if ($exists) {
                return response()->json(['error' => 'Failure rate dengan nama, rate, dan source ini sudah ada.'], 422);
            }

            $fr = NewRbdFailureRate::create([
                'new_rbd_model_id' => $request->new_rbd_model_id,
                'name' => $name,
                'failure_rate' => $rate,
                'source' => $source,
            ]);

            return response()->json(['success' => 'Failure rate created.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }




    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_rbd_model_id' => 'required|exists:new_rbd_models,id',
            'componentname' => [
                'required',
                'string',
                'max:255',
                Rule::unique('new_rbd_instances', 'componentname'),
            ],
            'time_interval' => 'required|numeric|min:0.01',
            'diagram_url' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            Log::error('RBD Instance Creation Validation Failed', ['errors' => $validator->errors()]);
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();
        try {
            $instance = NewRbdInstance::create([
                'componentname' => $request->componentname,
                'time_interval' => $request->time_interval,
                'diagram_url' => $request->diagram_url,
                'new_rbd_model_id' => $request->new_rbd_model_id,
                'user_id' => auth()->id(), // <— tambahkan baris ini
            ]);
            Log::info('RBD Instance Created', ['instance_id' => $instance->id, 'componentname' => $instance->componentname]);
            DB::commit();
            return response()->json(['success' => 'RBD instance created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('RBD Instance Creation Failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to create RBD instance: ' . $e->getMessage()], 500);
        }
    }



    public function calculate($id)
    {
        $rbdInstance = NewRbdInstance::findOrFail($id);
        $timeInterval = $rbdInstance->time_interval;

        $nodes = NewRbdNode::where('rbd_instance_id', $id)
            ->with(['failureRate', 'foreignInstance'])
            ->get();

        $links = NewRbdLink::where('rbd_instance_id', $id)
            ->with(['fromNode', 'toNode'])
            ->get();

        $reliabilities = [];
        $calculationTrace = [];
        $nodeDetails = [];
        $linkDetails = [];

        try {
            // Ambil end node
            $endNode = $nodes->where('category', 'end')->first();
            if (!$endNode) {
                throw new \Exception("End node not found in instance #{$id}");
            }

            // Hitung reliabilitas sistem
            $total_R = $this->getReliability($endNode->key_value, $nodes, $links, $reliabilities, $timeInterval);

            $R_system = $total_R['value'];
            $R_expr = $total_R['expr'];
            $lambdas = $total_R['lambdas'];

            // Update nilai reliabilitas sementara
            $rbdInstance->update([
                'temporary_reliability_value' => $R_system,
            ]);

            // === BUILD DETAILED RESPONSE ===

            // 1. Node Details (ringkas)
            foreach ($nodes as $node) {
                $rel = $reliabilities[$node->key_value] ?? null;
                $nodeDetails[] = [
                    'key' => $node->key_value,
                    'id' => $node->id,
                    'category' => $node->category,
                    'configuration' => $node->configuration,
                    'quantity' => $node->quantity,
                    'k' => $node->k,
                    'n' => $node->n,
                    't_initial' => $node->t_initial,
                    'failure_rate_id' => $node->failure_rate_id,
                    'foreign_instance_id' => $node->foreign_instance_id,
                    'reliability_value' => $rel['value'] ?? null,
                    'reliability_expr' => $rel['expr'] ?? null,
                    'has_failure_rate' => !empty($node->failureRate),
                    'has_foreign_instance' => !empty($node->foreignInstance),
                ];
            }

            // 2. Link Details
            foreach ($links as $link) {
                $linkDetails[] = [
                    'from' => $link->fromNode?->key_value,
                    'to' => $link->toNode?->key_value,
                ];
            }

            // 3. Trace perhitungan (urutan rekursi)
            $calculationTrace = array_map(function ($key) use ($reliabilities) {
                $r = $reliabilities[$key];
                return [
                    'node' => $key,
                    'value' => $r['value'],
                    'expr' => $r['expr'],
                    'lambdas' => $r['lambdas'],
                ];
            }, array_keys($reliabilities));

            // 4. Validasi tambahan
            $warnings = [];
            if (empty($lambdas)) {
                $warnings[] = "Tidak ada failure rate (λ) yang digunakan. Pastikan minimal satu komponen memiliki λ atau foreign instance yang valid.";
            }
            if ($endNode->key_value !== 'end') {
                $warnings[] = "Key 'end' node bukan 'end'. Gunakan key 'end' untuk kejelasan.";
            }

            Log::info('System Reliability Calculated Successfully', [
                'instance_id' => $id,
                'R_system' => $R_system,
                'R_expr' => $R_expr,
                'lambdas_count' => count($lambdas),
                'node_count' => $nodes->count(),
                'link_count' => $links->count(),
            ]);

            // === RETURN JSON YANG LENGKAP ===
            return response()->json([
                'success' => true,
                'message' => 'Reliability calculated successfully.',
                'system' => [
                    'instance_id' => $id,
                    'component_name' => $rbdInstance->componentname,
                    'time_interval' => $timeInterval,
                    'reliability_value' => $R_system,
                    'reliability_expression' => $R_expr,
                    'lambdas' => $lambdas,
                ],
                'graph' => [
                    'nodes' => $nodeDetails,
                    'links' => $linkDetails,
                ],
                'calculation_trace' => $calculationTrace,
                'warnings' => $warnings,
                'debug' => [
                    'total_nodes_processed' => count($reliabilities),
                    'end_node_key' => $endNode->key_value,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Reliability Calculation Failed', [
                'instance_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to calculate reliability: ' . $e->getMessage(),
                'instance_id' => $id,
                'time_interval' => $rbdInstance->time_interval ?? null,
            ], 500);
        }
    }

    public function destroy($id)
    {
        $rbdInstance = NewRbdInstance::findOrFail($id);
        DB::beginTransaction();
        try {
            // Delete related nodes and links
            NewRbdNode::where('rbd_instance_id', $id)->delete();
            NewRbdLink::where('rbd_instance_id', $id)->delete();
            $rbdInstance->delete();
            Log::info('RBD Instance Deleted', ['instance_id' => $id]);
            DB::commit();
            return response()->json(['success' => 'RBD instance deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('RBD Instance Deletion Failed', ['instance_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to delete RBD instance: ' . $e->getMessage()], 500);
        }
    }
    public function editNodes($id)
    {
        $rbdInstance = NewRbdInstance::findOrFail($id);
        $nodes = NewRbdNode::where('rbd_instance_id', $id)->with(['failureRate', 'foreignInstance'])->get();
        $new_rbd_model_id = $rbdInstance->new_rbd_model_id;
        $failureRates = NewRbdFailureRate::where('new_rbd_model_id', $new_rbd_model_id)->get();

        $otherInstances = NewRbdInstance::where('id', '!=', $id)->where('new_rbd_model_id', $new_rbd_model_id)->get();
        Log::info('Edit Nodes Loaded', ['instance_id' => $id, 'node_count' => $nodes->count(), 'failure_rates_count' => $failureRates->count()]);

        return view('newrbd.edit-nodes', compact('rbdInstance', 'nodes', 'failureRates', 'otherInstances'));
    }

    public function updateNodes(Request $request, $id)
    {

        $validFailureRateIds = NewRbdFailureRate::pluck('id')->toArray();
        $validInstanceIds = NewRbdInstance::where('id', '!=', $id)->pluck('id')->toArray();

        $validator = Validator::make($request->all(), [
            'nodes' => 'required|array|min:1',
            'nodes.*.id' => 'nullable|exists:new_rbd_nodes,id,rbd_instance_id,' . $id,
            'nodes.*.key_value' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request, $id) {
                    $index = explode('.', $attribute)[1];
                    $nodeId = $request->input("nodes.$index.id");
                    $query = NewRbdNode::where('rbd_instance_id', $id)->where('key_value', $value);
                    if ($nodeId) {
                        $query->where('id', '!=', $nodeId);
                    }
                    if ($query->exists()) {
                        $fail("The key '$value' is already taken in this instance.");
                    }
                },
            ],
            'nodes.*.category' => 'required|in:start,end,junction,component',
            'nodes.*.configuration' => 'required_if:nodes.*.category,component|in:single,series,parallel,k-out-of-n',
            'nodes.*.quantity' => 'required_if:nodes.*.configuration,series,parallel,k-out-of-n|nullable|integer|min:1',
            'nodes.*.code' => 'nullable|string|max:255',
            'nodes.*.name' => 'nullable|string|max:255',
            'nodes.*.failure_rate_id' => [
                'nullable',
                'integer',
                Rule::in($validFailureRateIds),
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $category = $request->input("nodes.$index.category");
                    $configuration = $request->input("nodes.$index.configuration");
                    $foreignId = $request->input("nodes.$index.foreign_instance_id");
                    if ($category === 'component' && $configuration === 'single' && !$value && !$foreignId) {
                        $fail("Either failure rate or foreign instance is required for component nodes with single configuration.");
                    }
                    if ($category === 'component' && $configuration === 'single' && $value && $foreignId) {
                        $fail("Select only one: failure rate or foreign instance for component nodes with single configuration.");
                    }
                },
            ],
            'nodes.*.foreign_instance_id' => [
                'nullable',
                'integer',
                Rule::in($validInstanceIds),
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $category = $request->input("nodes.$index.category");
                    $configuration = $request->input("nodes.$index.configuration");
                    if ($value && $category !== 'component' && $category !== 'junction') {
                        $fail("Foreign instance is only allowed for component or junction nodes.");
                    }
                },
            ],
            'nodes.*.x' => 'nullable|integer',
            'nodes.*.y' => 'nullable|integer',
            'nodes.*.k' => [
                'required_if:nodes.*.category,junction',
                'required_if:nodes.*.configuration,k-out-of-n',
                'nullable',
                'integer',
                'min:1',
            ],
            'nodes.*.n' => [
                'required_if:nodes.*.category,junction',
                'required_if:nodes.*.configuration,k-out-of-n',
                'nullable',
                'integer',
                'min:1',
            ],
            'nodes.*.k' => [
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $n = $request->input("nodes.$index.n");
                    $category = $request->input("nodes.$index.category");
                    $configuration = $request->input("nodes.$index.configuration");
                    if (($category === 'junction' || $configuration === 'k-out-of-n') && $value > $n) {
                        $fail("The k value must not be greater than n.");
                    }
                },
            ],
            // --- TAMBAHAN: validasi t_initial ---
            'nodes.*.t_initial' => [
                'required_if:nodes.*.category,component',
                'numeric',
            ],

        ]);

        if ($validator->fails()) {
            Log::error('Node Update Validation Failed', ['instance_id' => $id, 'errors' => $validator->errors()]);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $existingNodeIds = NewRbdNode::where('rbd_instance_id', $id)->pluck('id')->toArray();
            $submittedNodeIds = array_filter(array_column($request->nodes, 'id'));
            $nodesToDelete = array_diff($existingNodeIds, $submittedNodeIds);
            if (!empty($nodesToDelete)) {
                NewRbdNode::whereIn('id', $nodesToDelete)->where('rbd_instance_id', $id)->delete();
                Log::info('Nodes Deleted', ['instance_id' => $id, 'deleted_ids' => $nodesToDelete]);
            }

            $createdUpdated = [];
            foreach ($request->nodes as $nodeData) {
                $data = [
                    'rbd_instance_id' => $id,
                    'key_value' => $nodeData['key_value'],
                    'category' => $nodeData['category'],
                    'configuration' => $nodeData['category'] === 'component' ? ($nodeData['configuration'] ?? 'single') : 'single',
                    'quantity' => $nodeData['category'] === 'component' && in_array($nodeData['configuration'], ['series', 'parallel', 'k-out-of-n']) ? ($nodeData['quantity'] ?? 1) : 1,
                    'code' => $nodeData['code'] ?? null,
                    'name' => $nodeData['name'] ?? null,
                    'x' => $nodeData['x'] ?? null,
                    'y' => $nodeData['y'] ?? null,
                    'foreign_instance_id' => $nodeData['foreign_instance_id'] ?? null,
                    'k' => null,
                    'n' => null,
                    'failure_rate_id' => null,
                ];
                $data['t_initial'] = 0;
                if ($nodeData['category'] === 'component') {
                    $data['failure_rate_id'] = $nodeData['failure_rate_id'] ?? null;
                    $data['t_initial'] = $nodeData['t_initial']; // AMBIL DARI INPUT (wajib)
                    if ($nodeData['configuration'] === 'single') {
                        $data['foreign_instance_id'] = $nodeData['foreign_instance_id'] ?? null;
                    } elseif ($nodeData['configuration'] === 'k-out-of-n') {
                        $data['k'] = $nodeData['k'] ?? null;
                        $data['n'] = $nodeData['n'] ?? null;
                    } else {
                    }
                } elseif ($nodeData['category'] === 'junction') {
                    $data['k'] = $nodeData['k'] ?? null;
                    $data['n'] = $nodeData['n'] ?? null;
                    $data['foreign_instance_id'] = $nodeData['foreign_instance_id'] ?? null;
                }

                $node = NewRbdNode::updateOrCreate(
                    [
                        'id' => $nodeData['id'] ?? null,
                        'rbd_instance_id' => $id,
                    ],
                    $data
                );
                $createdUpdated[] = ['id' => $node->id, 'key_value' => $node->key_value];
            }

            Log::info('Nodes Created/Updated', ['instance_id' => $id, 'changes' => $createdUpdated]);
            DB::commit();
            $newrbdinstance = NewRbdInstance::find($id);
            return redirect()->route('newrbd.newrbdinstances', ['id' => $newrbdinstance->new_rbd_model_id])->with('success', 'Node list updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Node Update Failed', ['instance_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update nodes: ' . $e->getMessage())->withInput();
        }
    }



    public function editLinks($id)
    {
        $id = (int) $id;
        if ($id <= 0) abort(404);

        $rbdInstance = NewRbdInstance::findOrFail($id);

        $links = NewRbdLink::where('rbd_instance_id', $id)
            ->with(['fromNode', 'toNode'])
            ->get()
            ->filter(fn($link) => $link->fromNode && $link->toNode)
            ->map(fn($link) => [
                'id' => $link->id,
                'from_node_id' => $link->fromNode->id,
                'to_node_id' => $link->toNode->id,
            ]);

        $nodes = NewRbdNode::where('rbd_instance_id', $id)->get();

        Log::info('Edit Links Loaded', [
            'instance_id' => $id,
            'link_count' => $links->count(),
            'node_count' => $nodes->count(),
            'user_id' => auth()->id() ?? 'guest'
        ]);

        return response()->view('newrbd.edit-links', compact('rbdInstance', 'links', 'nodes'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function updateLinks(Request $request, $id)
    {
        $id = (int) $id;
        if ($id <= 0) {
            Log::warning('Invalid instance ID in URL', ['path' => $request->path()]);
            abort(404);
        }

        $rbdInstance = NewRbdInstance::findOrFail($id);

        $formInstanceId = (int) $request->input('instance_id');
        if ($formInstanceId !== $id) {
            Log::warning('Instance ID mismatch', [
                'url_id' => $id,
                'form_id' => $formInstanceId,
                'user_id' => auth()->id() ?? 'guest',
                'ip' => $request->ip()
            ]);
            abort(403, 'Invalid instance reference.');
        }

        $linksData = $request->input('links', []);

        $validator = Validator::make($request->all(), [
            'instance_id' => 'required|integer',
            'links' => 'required|array|min:1',
            'links.*.from_node_id' => 'required|integer|exists:new_rbd_nodes,id,rbd_instance_id,' . $id,
            'links.*.to_node_id' => 'required|integer|exists:new_rbd_nodes,id,rbd_instance_id,' . $id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Validasi self-link & duplikat
        $errors = [];
        $seen = [];
        foreach ($linksData as $index => $link) {
            $from = $link['from_node_id'];
            $to = $link['to_node_id'];
            $sig = "$from|$to";

            if ($from === $to) {
                $errors["links.$index.to_node_id"] = "Cannot link a node to itself.";
                continue;
            }
            if (in_array($sig, $seen)) {
                $errors["links.$index.to_node_id"] = "Duplicate link.";
                continue;
            }
            $seen[] = $sig;
        }

        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        DB::beginTransaction();
        try {
            NewRbdLink::where('rbd_instance_id', $id)->delete();

            $insertData = array_map(fn($link) => [
                'rbd_instance_id' => $id,
                'from_node_id' => $link['from_node_id'],
                'to_node_id' => $link['to_node_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ], $linksData);

            if (!empty($insertData)) {
                NewRbdLink::insert($insertData);
            }

            DB::commit();

            Log::info('Links updated successfully', [
                'instance_id' => $id,
                'inserted' => count($insertData),
                'user_id' => auth()->id() ?? 'guest'
            ]);

            return redirect()
                ->route('newrbd.newrbdinstances', ['id' => $rbdInstance->new_rbd_model_id])
                ->with('success', 'Links updated successfully.')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update links', [
                'instance_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to save links: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function buildKoutofNExpression($p_expr, $k, $n)
    {
        $terms = [];
        for ($i = $k; $i <= $n; $i++) {
            $coef = $this->binomialCoefficient($n, $i);
            $power_p = $i == 1 ? "" : "^{$i}";

            // HANYA tambah (1-p) jika n-i > 0
            $q_part = '';
            if ($n - $i > 0) {
                $power_q = ($n - $i) == 1 ? "" : "^{" . ($n - $i) . "}";
                $q_part = "*(1-{$p_expr}){$power_q}";
            }
            // Jika n-i == 0 → q_part = KOSONG

            $terms[] = "{$coef}*({$p_expr}){$power_p}{$q_part}";
        }
        return "(" . implode(" + ", $terms) . ")";
    }


    private function getReliability($key, $nodes, $links, &$reliabilities, $timeInterval)
    {
        if (isset($reliabilities[$key])) {
            return $reliabilities[$key];
        }

        $node_lambdas = [];
        $node = $nodes->firstWhere('key_value', $key);
        $quantity = $node->quantity ?? 1;
        $R_node = '1';
        $R_expr = '1';

        if ($node->foreign_instance_id && $node->foreignInstance) {
            $foreignInstance = $node->foreignInstance;
            $foreignNodes = $foreignInstance->nodes()->with(['failureRate', 'foreignInstance'])->get();
            $foreignLinks = $foreignInstance->links;
            $foreignReliabilities = [];
            $endKey = $foreignNodes->where('category', 'end')->first()?->key_value ?? 'end';

            $foreignResult = $this->getReliability(
                $endKey,
                $foreignNodes,
                $foreignLinks,
                $foreignReliabilities,
                $foreignInstance->time_interval
            );

            $R_node = $foreignResult['value'];
            $R_expr = $foreignResult['expr'];
            $node_lambdas = $foreignResult['lambdas'];

            Log::info('FOREIGN INSTANCE FULLY RESOLVED', [
                'parent_key' => $key,
                'foreign_id' => $foreignInstance->id,
                'R_expr' => $R_expr,
                'lambdas_count' => count($node_lambdas)
            ]);
        } elseif ($node->failure_rate_id && $node->failureRate) {
            $lam = $node->failureRate->failure_rate;
            $deltaTime = $timeInterval - ($node->t_initial ?? 0);
            $exponent = - ($lam * $deltaTime);
            $R_node = sprintf('%.20f', exp($exponent));

            $safe_name = Str::slug($node->name, '_');
            $symbol_name = "lam_" . $safe_name;
            $R_expr = "exp(-{$symbol_name}*t)";
            $node_lambdas[$symbol_name] = (float)$lam;

            Log::info('Lambda Symbol Created', [
                'key' => $key,
                'node_name' => $node->name,
                'symbol' => $symbol_name,
                'value' => $lam,
                'R_expr' => $R_expr
            ]);
        } elseif (isset($node->reliability) && is_numeric($node->reliability)) {
            $R_node = (string)$node->reliability;
            $R_expr = $R_node;
        }

        // Component configuration
        if ($node->category === 'component' && $node->configuration !== 'single') {
            if ($node->configuration === 'series') {
                $R_node = bcpow($R_node, (string)$quantity, 20);
                $R_expr = "({$R_expr})^{$quantity}";
            } elseif ($node->configuration === 'parallel') {
                $R_single = $R_node;
                $R_node = bcsub('1', bcpow(bcsub('1', $R_single, 20), (string)$quantity, 20), 20);
                $R_expr = "1 - (1 - {$R_expr})^{$quantity}";
            } elseif ($node->configuration === 'k-out-of-n') {
                $k = (int)$node->k;
                $n = (int)$node->n;
                $R_total = $this->reliability_k_out_of_n($R_node, $k, $n);
                $R_node = (string)$R_total;
                $R_expr = $this->buildKoutofNExpression($R_expr, $k, $n);
            }
        }

        // Ganti: cari parent via to_node_id
        $parentNodeIds = $links->where('to_node_id', $node->id)->pluck('from_node_id')->toArray();
        $parentRs = [];
        $all_lambdas = $node_lambdas;

        foreach ($parentNodeIds as $parentNodeId) {
            $parentNode = $nodes->firstWhere('id', $parentNodeId);
            if (!$parentNode) continue;

            $parentKey = $parentNode->key_value;
            $parentR = $this->getReliability($parentKey, $nodes, $links, $reliabilities, $timeInterval);
            $parentRs[] = $parentR;
            $all_lambdas = array_merge($all_lambdas, $parentR['lambdas'] ?? []);
        }

        Log::info('Node Calculation', [
            'key' => $key,
            'instance_id' => $node->rbd_instance_id,
            'category' => $node->category,
            'configuration' => $node->configuration,
            'R_node' => $R_node,
            'parent_node_ids' => $parentNodeIds,
            'parent_Rs' => $parentRs
        ]);

        $final_expr = $R_expr;

        if ($node->category === 'start') {
            $R_total = $R_node;
            foreach ($parentRs as $r) {
                $R_total = bcmul($R_total, (string)$r['value'], 20);
                $final_expr = "({$final_expr}) * ({$r['expr']})";
            }

            $expr_parts = array_map(fn($r) => "({$r['expr']})", $parentRs);
            $parent_expr = !empty($expr_parts) ? implode(' * ', $expr_parts) : '1';
            $final_expr = $R_expr !== '1' ? "($R_expr)" . ($parent_expr !== '1' ? " * ($parent_expr)" : '') : $parent_expr;
            $final_expr = $final_expr ?: '1';

            $result = ['value' => $R_total, 'expr' => $final_expr, 'lambdas' => $all_lambdas];
            $reliabilities[$key] = $result;
            return $result;
        }

        if ($node->category === 'junction') {
            $k = $node->k ?? 1;
            $n = $node->n ?? count($parentRs);

            if ($k > $n || $k < 1) {
                throw new \Exception("Invalid k-of-n: k=$k, n=$n for junction '$key' in instance {$node->rbd_instance_id}");
            }

            if (count($parentRs) === 0) {
                $R_total = $R_node;
            } elseif ($k === 1) {
                $prod = '1';
                $expr_terms = [];
                foreach ($parentRs as $r) {
                    $q_i = "(1 - {$r['expr']})";
                    $prod = bcmul($prod, bcsub('1', (string)$r['value'], 20), 20);
                    $expr_terms[] = $q_i;
                }
                $R_total = bcsub('1', $prod, 20);
                $final_expr = "1 - (" . implode(' * ', $expr_terms) . ")";
            } elseif ($k === $n) {
                $R_total = '1';
                $expr_terms = [];
                foreach ($parentRs as $r) {
                    $R_total = bcmul($R_total, (string)$r['value'], 20);
                    $expr_terms[] = $r['expr'];
                }
                $final_expr = implode(' * ', $expr_terms);
            } else {
                $sum_p = '0';
                foreach ($parentRs as $r) {
                    $sum_p = bcadd($sum_p, (string)$r['value'], 20);
                }
                $p = count($parentRs) > 0 ? bcdiv($sum_p, (string)count($parentRs), 20) : $R_node;
                $q = bcsub('1', $p, 20);
                $R_total = '0';
                for ($i = $k; $i <= $n; ++$i) {
                    $coef = $this->binomialCoefficient($n, $i);
                    $p_pow_i = bcpow($p, (string)$i, 20);
                    $q_pow_ni = bcpow($q, (string)($n - $i), 20);
                    $term = bcmul($coef, bcmul($p_pow_i, $q_pow_ni, 20), 20);
                    $R_total = bcadd($R_total, $term, 20);
                }
                $p_expr = !empty($parentRs) ? $parentRs[0]['expr'] : $R_expr;
                $final_expr = $this->buildKoutofNExpression($p_expr, $k, $n);
            }

            $expr_parts = array_map(fn($r) => "({$r['expr']})", $parentRs);
            $parent_expr = !empty($expr_parts) ? implode(' * ', $expr_parts) : '1';
            $final_expr = $R_expr !== '1' ? "($R_expr)" . ($parent_expr !== '1' ? " * ($parent_expr)" : '') : $parent_expr;
            $final_expr = $final_expr ?: '1';

            $result = ['value' => $R_total, 'expr' => $final_expr, 'lambdas' => $all_lambdas];
            $reliabilities[$key] = $result;
            return $result;
        }

        if ($node->category === 'end') {
            $R_total = '1';
            $junctionReliabilities = $this->getAllJunctionReliabilities($key, $nodes, $links, $reliabilities, $timeInterval);
            $componentReliabilities = [];

            foreach ($parentNodeIds as $parentNodeId) {
                $parentNode = $nodes->firstWhere('id', $parentNodeId);
                if ($parentNode) {
                    $parentKey = $parentNode->key_value;
                    $total_R = $this->getReliability($parentKey, $nodes, $links, $reliabilities, $timeInterval);
                    $componentReliabilities[] = $total_R['value'];
                }
            }

            $allReliabilities = array_unique(array_merge($junctionReliabilities, $componentReliabilities));
            foreach ($allReliabilities as $r) {
                $R_total = bcmul($R_total, (string)$r, 20);
            }

            $expr_parts = array_map(fn($r) => "({$r['expr']})", $parentRs);
            $parent_expr = !empty($expr_parts) ? implode(' * ', $expr_parts) : '1';
            $final_expr = $R_expr !== '1' ? "($R_expr)" . ($parent_expr !== '1' ? " * ($parent_expr)" : '') : $parent_expr;
            $final_expr = $final_expr ?: '1';

            $result = ['value' => $R_total, 'expr' => $final_expr, 'lambdas' => $all_lambdas];
            $reliabilities[$key] = $result;
            return $result;
        }

        // Default: component or fallback
        $R_total = $R_node;
        foreach ($parentRs as $r) {
            $R_total = bcmul($R_total, (string)$r['value'], 20);
        }

        $expr_parts = array_map(fn($r) => "({$r['expr']})", $parentRs);
        $parent_expr = !empty($expr_parts) ? implode(' * ', $expr_parts) : '1';
        $final_expr = $R_expr !== '1' ? "($R_expr)" . ($parent_expr !== '1' ? " * ($parent_expr)" : '') : $parent_expr;
        $final_expr = $final_expr ?: '1';

        $result = ['value' => $R_total, 'expr' => $final_expr, 'lambdas' => $all_lambdas];
        $reliabilities[$key] = $result;
        return $result;
    }


    public function failureratecalculateAndSendToPython(Request $request, $instanceId)
    {
        $instance = NewRbdInstance::findOrFail($instanceId);
        $nodes = $instance->nodes;
        $links = $instance->links;
        $timeInterval = $instance->time_interval;

        $reliabilities = [];
        $endNode = $nodes->where('category', 'end')->first();
        $result = $this->getReliability($endNode->key_value, $nodes, $links, $reliabilities, $timeInterval);

        $R_t_expr = $result['expr'];
        $R_value  = $result['value'];
        $lambdas  = $result['lambdas']; // ← LANGSUNG DAPAT!

        // JIKA MASIH KOSONG → ERROR JELAS
        if (empty($lambdas)) {
            return "<pre style='color:red'>TIDAK ADA FAILURE RATE!<br>
                Pastikan minimal 1 komponen punya λ<br>
                Ekspresi: {$R_t_expr}</pre>";
        }

        // LIHAT DULU EKSPRESI (MODE DEBUG)
        if (request()->has('debug')) {
            return view('debug.rtexpr', compact('R_t_expr', 'R_value', 'lambdas', 'timeInterval'));
        }
        $ordo = $request->input('ordo', 2);
        // LANJUT KE PYTHON
        $payload = [
            'fungsi'   => $R_t_expr,
            'lambdas'  => $lambdas,
            't_values' => [$timeInterval],
            'ordo' => $ordo,
        ];

        $response = Http::timeout(600)->post('http://147.93.103.168:5632/calculate_hazard', $payload);

        try {
            $hazard_data = $response->json('data');
            $responseData = $hazard_data[0] ?? [];
            $hazardRate = $responseData['hazard_rate'] ?? null;
            $tValue = $response->json('t_value') ?? null;
            $hazard_rate_expression = $response->json('h');
            $frequency_expression = $response->json('f');
            $method = $response->json('method');

            $instance->update([
                'temporary_failure_rate_value' => $hazardRate,
                'r_t_symbolic'                => $R_t_expr,
                'hazard_rate_expression'      => $hazard_rate_expression,
                'frequency_expression'        => $frequency_expression,
            ]);
            try {
                $response = Http::timeout(600)->post('http://147.93.103.168:5632/calculate_t_r', $payload);
                $responseData = $response->json('data')[0] ?? [];
                $hazardRate = $responseData['hazard_rate'] ?? null;
                $tValue = $response->json('t_value') ?? null;
                $t_expression = $response->json('t_expression');
                $instance->update([
                    't_expression'                => $t_expression,
                    't_value'                     => $tValue,
                ]);


                return response()->json([
                    'success' => true,
                    'R_t_symbolic' => $R_t_expr,
                    'hazard_rate_expression' => $hazard_rate_expression,
                    'frequency_expression' => $frequency_expression,
                    't_expression' => $t_expression,
                    'method' => $method,
                    't_value' => $tValue,
                    'hazard_rate' => $hazard_data,
                    'lambdas_found' => $lambdas,
                ]);
            } catch (\Exception $e) {
                Log::error('Gagal update instance: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('Gagal update instance: ' . $e->getMessage());
        }
    }


    private function getAllJunctionReliabilities($key, $nodes, $links, &$reliabilities, $timeInterval)
    {
        $node = $nodes->firstWhere('key_value', $key);
        $parentNodeIds = $links->where('to_node_id', $node->id)->pluck('from_node_id')->toArray();
        $junctionReliabilities = [];

        foreach ($parentNodeIds as $parentNodeId) {
            $parentNode = $nodes->firstWhere('id', $parentNodeId);
            if ($parentNode && $parentNode->category === 'junction') {
                $total_R = $this->getReliability($parentNode->key_value, $nodes, $links, $reliabilities, $timeInterval);
                $junctionReliabilities[] = $total_R['value'];
            }
            // Rekursif
            $grandParentJunctions = $this->getAllJunctionReliabilities($parentNode->key_value ?? '', $nodes, $links, $reliabilities, $timeInterval);
            $junctionReliabilities = array_merge($junctionReliabilities, $grandParentJunctions);
        }

        return array_unique($junctionReliabilities);
    }

    private function reliability_k_out_of_n($p, $k, $n, $scale = 20)
    {
        // p bisa string atau float; pastikan string
        $p = (string)$p;
        $q = bcsub('1', $p, $scale);
        $R_total = '0';
        for ($i = $k; $i <= $n; ++$i) {
            $coef = (string)$this->binomialCoefficient($n, $i); // pastikan string
            $p_pow_i = bcpow($p, (string)$i, $scale);
            $q_pow_ni = bcpow($q, (string)($n - $i), $scale);
            $term = bcmul($coef, bcmul($p_pow_i, $q_pow_ni, $scale), $scale);
            $R_total = bcadd($R_total, $term, $scale);
        }
        return $R_total;
    }


    private function binomialCoefficient($n, $k)
    {
        if ($k < 0 || $k > $n) {
            return '0';
        }
        if ($k == 0 || $k == $n) {
            return '1';
        }
        $k = min($k, $n - $k);
        $result = '1';
        for ($i = 0; $i < $k; ++$i) {
            $result = bcmul($result, bcsub((string)($n - $i), '0', 20), 20);
            $result = bcdiv($result, bcadd((string)($i + 1), '0', 20), 20);
        }
        return $result;
    }

    public function newrbdinstances($new_rbd_model_id)
    {
        $model = NewRbdModel::findOrFail($new_rbd_model_id); // Pastikan model ada
        $instances = NewRbdInstance::withCount(['nodes', 'links'])
            ->where('new_rbd_model_id', $new_rbd_model_id)->get();

        $failureRates = NewRbdFailureRate::where('new_rbd_model_id', $new_rbd_model_id)->get();

        Log::info('RBD Instances by Model Loaded', [
            'model_id' => $new_rbd_model_id,
            'model_name' => $model->name,
            'instance_count' => $instances->count(),
        ]);

        return view('newrbd.newrbdinstances', compact(
            'instances',
            'failureRates',
            'model' // ← Tambahan: kirim model
        ));
    }

    public function show($id)
    {
        $rbdInstance = NewRbdInstance::findOrFail($id);
        $timeInterval = $rbdInstance->time_interval;
        $nodes = NewRbdNode::where('rbd_instance_id', $id)
            ->with(['failureRate', 'foreignInstance'])
            ->get();
        $links = NewRbdLink::where('rbd_instance_id', $id)->get();
        $formattedTimeInterval = $timeInterval;

        $nodeDataArray = $nodes->map(function ($node) use ($formattedTimeInterval) {
            $failureRate = $node->failureRate ? $node->failureRate->failure_rate : null;
            $foreignR = $node->foreignInstance ? $node->foreignInstance->temporary_reliability_value : null;
            $dataakhir = [
                'key_value' => $node->key_value,
                'category' => $node->category,
                'code' => $node->code,
                'name' => $node->name,
                'k' => $node->k,
                'n' => $node->n,
                'x' => $node->x ?? 0,
                'y' => $node->y ?? 0,
                'configuration' => $node->configuration,
                'quantity' => $node->quantity,
                'failure_rate' => $failureRate,
                'foreign_instance_id' => $node->foreign_instance_id,
                'foreign_r' => $foreignR ? number_format($foreignR, 6) : null,
                't_initial' => $node->t_initial,
                'time_interval' => $formattedTimeInterval,
            ];

            switch ($node->configuration) {
                case 'single':
                    $dataakhir['shownumber'] = '1';
                    break;
                case 'series':
                    $dataakhir['shownumber'] = "{$node->quantity}|{$node->quantity}";
                    break;
                case 'parallel':
                    $dataakhir['shownumber'] = "1|{$node->quantity}";
                    break;
                case 'k-out-of-n':
                    $dataakhir['shownumber'] = "{$node->k}|{$node->n}";
                    break;
                default:
                    $dataakhir['shownumber'] = '';
            }

            return $dataakhir;
        })->toArray();

        $linkDataArray = $links->map(function ($link) {
            return [
                'from' => $link->fromNode->key_value,
                'to'   => $link->toNode->key_value,
            ];
        })->toArray();

        Log::info('RBD Show Loaded', [
            'instance_id' => $id,
            'time_interval' => $rbdInstance->time_interval,

            'nodes_count' => count($nodeDataArray),
            'links_count' => count($linkDataArray)
        ]);
        Log::info('nodeDataArray', $nodeDataArray);
        Log::info('linkDataArray', $linkDataArray);

        $reliabilities = [];
        $R_system = '0';
        $R_expr = '1';
        $lambdas = [];


        try {
            $total_R = $this->getReliability('end', $nodes, $links, $reliabilities, $rbdInstance->time_interval);

            $R_system = $total_R['value'];
            $R_expr = $total_R['expr'] ?? '1';
            $lambdas = $total_R['lambdas'] ?? [];
            $failure_rate = $rbdInstance->temporary_failure_rate_value;
            // Update DB
            $rbdInstance->update(['temporary_reliability_value' => $R_system]);

            // TAMBAHAN LOG UNTUK EXCEL COMPARISON
            Log::info('RELIABILITY CALCULATION RESULT (FOR EXCEL)', [
                'instance_id' => $id,
                'time_interval_t' => $rbdInstance->time_interval,
                'R_system_numeric' => $R_system,                    // String BC Math (presisi 20)
                'R_system_float' => (float)$R_system,               // Untuk copy ke Excel
                'R_system_20digit' => sprintf('%.20f', (float)$R_system),
                'R_symbolic_expression' => $R_expr,                 // R(t) simbolik
                'lambdas_used' => $lambdas,                         // Semua λ: [lam_A => 0.001, ...]
                'lambdas_count' => count($lambdas),
                'all_reliabilities_cached' => array_map(fn($r) => [
                    'key' => $r['key'] ?? 'unknown',
                    'value' => $r['value'],
                    'expr' => $r['expr'],
                ], $reliabilities),
            ]);

            // Log khusus untuk Excel paste (1 baris)
            $lambda_str = '';
            foreach ($lambdas as $symbol => $value) {
                $lambda_str .= "$symbol = $value; ";
            }

            Log::info('EXCEL_PASTE_READY', [
                'COPY_PASTE_TO_EXCEL' => "R(t) = $R_expr | R = " . sprintf('%.20f', (float)$R_system) . " | λ_sys = " . sprintf('%.20f', $failure_rate) . " | t = $timeInterval | Lambdas: $lambda_str"
            ]);
        } catch (\Exception $e) {
            Log::error('Reliability Calculation Failed', [
                'instance_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $R_system = '0';
        }

        return view('newrbd.show', [
            'data' => [
                'nodeDataArray' => $nodeDataArray,
                'linkDataArray' => $linkDataArray,
            ],
            // KIRIM RAW VALUE → biar Blade yang format cerdas
            'systemReliability' => (float)$R_system,
            'failureRate' => (float)$failure_rate,
            'rbdInstanceId' => $rbdInstance->id,
            'timeInterval' => (float)$rbdInstance->time_interval,
            // === EKSPRESI SIMBOLIK & NILAI DARI PYTHON ===
            'r_t_symbolic'           => $rbdInstance->r_t_symbolic, // ← INI YANG BENAR (bukan time_interval!)
            'hazard_rate_expression' => $rbdInstance->hazard_rate_expression,
            'frequency_expression'   => $rbdInstance->frequency_expression,
            't_expression'           => $rbdInstance->t_expression,
            't_value'                => $rbdInstance->t_value, // bisa besar → tetap float

            // Debug: tetap simpan nilai mentah + format ilmiah
            'debug' => [
                'R_expr' => $R_expr,
                'R_raw' => $R_system,
                'R_scientific' => sprintf('%.6E', $R_system),
                'R_20digit' => sprintf('%.20f', $R_system),
                'failure_rate_scientific' => sprintf('%.2E', $failure_rate),
                'lambdas' => $lambdas,
            ]
        ]);
    }


    public function jsoncreatemodelview()
    {
        $models = NewRbdModel::withCount('instances')->orderByDesc('id')->get();

        return view('newrbd.jsoncreatemodel', compact('models'));
    }
    /**
     * [POST] /newrbdmodels/json/createmodel
     * Membuat seluruh model RBD dari JSON dalam satu request.
     */
    public function jsoncreatemodel(Request $request)
    {
        // Ambil data baik dari JSON body maupun form-data
        $data = $request->all() ?: $request->json()->all();

        // === VALIDASI ===
        $validator = Validator::make($data, [
            'model_name'        => 'required|string|max:255',
            'model_description' => 'nullable|string',
            'instances'         => 'required|array|min:1',
            'instances.*.name'  => 'required|string|max:255',
            'instances.*.time_interval' => 'required|numeric|min:0',
            'instances.*.nodes' => 'required|array|min:1',
            'instances.*.links' => 'required|array',

            'instances.*.nodes.*.key_value'     => 'required|string',
            'instances.*.nodes.*.category'      => 'required|in:start,junction,end,component',

            // Node start & end boleh tanpa name
            'instances.*.nodes.*.name'          => 'required_unless:instances.*.nodes.*.category,start,end|string',

            'instances.*.nodes.*.x'             => 'required|numeric',
            'instances.*.nodes.*.y'             => 'required|numeric',
            'instances.*.nodes.*.configuration' => 'nullable|in:single,series,parallel,k-out-of-n',
            'instances.*.nodes.*.quantity'      => 'nullable|integer|min:1',
            'instances.*.nodes.*.k'             => 'nullable|integer|min:1',
            'instances.*.nodes.*.n'             => 'nullable|integer|min:1',
            'instances.*.nodes.*.failure_rate'  => 'nullable|numeric|min:0',
            'instances.*.nodes.*.t_initial'     => 'nullable|numeric|min:0',

            'instances.*.links.*.from'          => 'required|string',
            'instances.*.links.*.to'            => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            // === Mulai Transaksi ===
            DB::beginTransaction();

            // === 1. Buat Model ===
            $model = NewRbdModel::create([
                'name'        => $data['model_name'],
                'description' => $data['model_description'] ?? null,
                'user_id'     => auth()->id() ?? null,
            ]);

            $createdInstances = [];

            foreach ($data['instances'] as $instData) {
                // === 2. Buat Instance ===
                $instance = NewRbdInstance::create([
                    'new_rbd_model_id' => $model->id,
                    'componentname'    => $instData['name'],
                    'time_interval'    => $instData['time_interval'],
                    'user_id' => auth()->id(), // <— tambahkan baris ini
                ]);

                $nodeKeyToId = [];
                $failureRateMap = [];

                // === 3. Buat Failure Rate (jika ada) ===
                foreach ($instData['nodes'] as $nodeData) {
                    if (!empty($nodeData['failure_rate'])) {
                        $fr = NewRbdFailureRate::create([
                            'new_rbd_model_id' => $model->id,
                            'failure_rate'     => $nodeData['failure_rate'],
                            'source'           => 'json_import', // TAMBAHKAN INI!
                            'name'             => ($nodeData['name'] ?? $nodeData['key_value']) . ' λ',
                        ]);
                        $failureRateMap[$nodeData['key_value']] = $fr->id;
                    }
                }

                // === 4. Buat Nodes ===
                foreach ($instData['nodes'] as $nodeData) {
                    $node = NewRbdNode::create([
                        'rbd_instance_id'   => $instance->id,
                        'key_value'         => $nodeData['key_value'],
                        'category'          => $nodeData['category'],
                        'code'              => $nodeData['code'] ?? null,
                        'name'              => $nodeData['name'] ?? strtoupper($nodeData['category']),
                        'x'                 => $nodeData['x'],
                        'y'                 => $nodeData['y'],
                        'configuration'     => $nodeData['configuration'] ?? 'single',
                        'quantity'          => $nodeData['quantity'] ?? 1,
                        'k'                 => $nodeData['k'] ?? null,
                        'n'                 => $nodeData['n'] ?? null,
                        'failure_rate_id'   => $failureRateMap[$nodeData['key_value']] ?? null,
                        't_initial'         => $nodeData['t_initial'] ?? null,
                    ]);

                    $nodeKeyToId[$nodeData['key_value']] = $node->id;
                }

                // === 5. Buat Links ===
                foreach ($instData['links'] as $linkData) {
                    // Cek validitas mapping
                    if (!isset($nodeKeyToId[$linkData['from']]) || !isset($nodeKeyToId[$linkData['to']])) {
                        throw new \Exception("Invalid link mapping: {$linkData['from']} → {$linkData['to']}");
                    }

                    NewRbdLink::create([
                        'rbd_instance_id' => $instance->id,
                        'from_node_id'    => $nodeKeyToId[$linkData['from']],
                        'to_node_id'      => $nodeKeyToId[$linkData['to']],
                    ]);
                }

                $createdInstances[] = [
                    'id'            => $instance->id,
                    'name'          => $instance->componentname,
                    'time_interval' => $instance->time_interval,
                    'nodes_count'   => count($instData['nodes']),
                    'links_count'   => count($instData['links']),
                ];
            }

            // === Commit transaksi ===
            DB::commit();

            Log::info('RBD Model created via JSON', [
                'model_id'     => $model->id,
                'model_name'   => $model->name,
                'instances'    => count($createdInstances),
                'user_id'      => auth()->id() ?? 'guest',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Model created successfully',
                'model'   => [
                    'id'          => $model->id,
                    'name'        => $model->name,
                    'description' => $model->description,
                ],
                'instances' => $createdInstances,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('JSON Create Model Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $data,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create model: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * [GET] /newrbd/jsonshowmodel/{model_id}
     * Mengembalikan seluruh struktur model dalam format JSON (untuk AI, debugging, export)
     */
    public function jsonshowmodel($id)
    {
        $model = NewRbdModel::with([
            'instances.nodes.failureRate',
            'instances.links.fromNode',
            'instances.links.toNode',
            'failureRates'
        ])
            ->findOrFail($id);

        $export = [
            'model_name'        => $model->name,
            'model_description' => $model->description,
            'instances'         => $model->instances->map(function ($instance) {
                return [
                    'name'          => $instance->componentname,
                    'time_interval' => (float) $instance->time_interval,
                    'nodes'         => $instance->nodes->map(function ($node) {
                        $data = [
                            'key_value' => $node->key_value,
                            'category'  => $node->category,
                            'code'  => $node->code,
                            'quantity'  => $node->quantity,
                            'x'         => (float) $node->x,
                            'y'         => (float) $node->y,
                        ];

                        // Hanya tambahkan field berikut jika category adalah 'component'
                        if ($node->category === 'component') {
                            $data['name']          = $node->name;
                            $data['failure_rate']  = $node->failureRate ? (float) $node->failureRate->failure_rate : null;
                            $data['code']          = $node->code;
                        }

                        // Untuk start/end, tetap minimal
                        return $data;
                    })->values()->toArray(), // values() untuk reset index array

                    'links' => $instance->links->map(function ($link) {
                        return [
                            'from' => $link->fromNode->key_value,
                            'to'   => $link->toNode->key_value,
                        ];
                    })->values()->toArray(),
                ];
            })->values()->toArray(),
        ];

        Log::info('RBD Model JSON Exported', [
            'model_id'   => $model->id,
            'model_name' => $model->name,
            'instances'  => count($export['instances']),
            'user_id'    => auth()->id() ?? 'guest',
        ]);

        return response()->json($export, 200, [], JSON_PRETTY_PRINT);
    }


    public function modeldestroy($id)
    {
        try {
            // Cari model + eager load relasi untuk efisiensi
            $model = NewRbdModel::with(['instances.nodes', 'instances.links', 'failureRates'])
                ->find($id);

            if (!$model) {
                return response()->json([
                    'success' => false,
                    'message' => 'Model not found.'
                ], 404);
            }

            // === KEPEMILIKAN: Hanya admin (id=1) atau pemilik ===
            if (Auth::id() !== 1 && Auth::id() !== $model->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You do not own this model.'
                ], 403);
            }

            DB::beginTransaction();

            // === Hapus semua relasi ===
            foreach ($model->instances as $instance) {
                // Hapus links
                $instance->links()->delete();

                // Hapus nodes (otomatis hapus failure rate jika ada relasi cascade)
                $instance->nodes()->delete();
            }

            // Hapus semua instances
            $model->instances()->delete();

            // Hapus failure rates yang terkait model
            $model->failureRates()->delete();

            // Hapus model utama
            $model->delete();

            DB::commit();

            Log::info('RBD Model deleted successfully', [
                'model_id'   => $id,
                'model_name' => $model->name,
                'user_id'    => Auth::id() ?? 'guest',
                'deleted_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Model and all related data deleted successfully.',
                'deleted_model_id' => $id
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Failed to delete RBD Model', [
                'model_id' => $id,
                'error'    => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
                'user_id'  => Auth::id() ?? 'guest',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete model: ' . $e->getMessage()
            ], 500);
        }
    }
}
