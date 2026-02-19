<?php

namespace App\Http\Controllers;

use App\Models\FmecaPart;
use App\Models\FmecaItem;
use App\Models\ProjectType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Exports\CriticalItemsExport;
use Maatwebsite\Excel\Facades\Excel;

class FMECAController extends Controller
{
    public function index()
    {
        $fmecaParts = FmecaPart::with(['fmecaIdentity', 'fmecaItems'])->get();
        $projectTypes = ProjectType::with('fmecaIdentities')->get();

        return view('fmeca.index', [
            'fmecaParts' => $fmecaParts,
            'projectTypes' => $projectTypes,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'fmeca_identity_id' => 'required|exists:fmeca_identities,id',
        ]);

        FmecaPart::create($request->only('name', 'fmeca_identity_id'));

        return redirect()->route('fmeca.index')->with('success', 'FMECA Part created successfully.');
    }

    public function items(FmecaPart $fmecaPart)
    {
        $items = $fmecaPart->fmecaItems()->orderBy('order')->get();
        return response()->json([
            'items' => $items,
        ]);
    }

    public function viewItems(FmecaPart $fmecaPart)
    {
        $items = $fmecaPart->fmecaItems()->orderBy('order')->get();
        return view('fmeca.items', [
            'fmecaPart' => $fmecaPart,
            'items' => $items,
        ]);
    }

    public function update(Request $request, FmecaItem $fmecaItem)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'item_ref' => 'required|string|max:255',
            'subsystem' => 'nullable|string|max:255',
            'item_name' => 'required|string|max:255',
            'function' => 'nullable|string',
            'operational_mode' => 'nullable|string',
            'is_safety' => 'boolean',
            'failure_mode' => 'nullable|string',
            'failure_causes' => 'nullable|string',
            'failure_base' => 'nullable|string',
            'ratio' => 'nullable|numeric',
            'failure_rate' => 'required|numeric',
            'items_per_train' => 'required|integer',
            'data_source' => 'nullable|string',
            'failure_effect_item' => 'nullable|string',
            'failure_effect_subsystem' => 'nullable|string',
            'failure_effect_system' => 'nullable|string',
            'reference' => 'nullable|string',
            'safety_risk_severity_class' => $request->is_safety ? 'required|in:Insignificant,Marginal,Critical,Catastrophic' : 'nullable|in:Insignificant,Marginal,Critical,Catastrophic',
            'reliability_risk_severity_class' => !$request->is_safety ? 'required|in:Insignificant,Marginal,Critical,Catastrophic' : 'nullable|in:Insignificant,Marginal,Critical,Catastrophic',
            'failure_detection_means' => 'nullable|string',
            'available_contingency' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            Log::info('Update Request Data:', $request->all());

            $failureRate = $request->failure_rate;
            $itemsPerTrain = $request->items_per_train;
            $trainYearlyHours = $fmecaItem->fmecaPart->fmecaIdentity->train_yearly_hours ?? 0;
            $riskLevel = null;

            if ($failureRate && $itemsPerTrain && $trainYearlyHours && ($failureRate * $itemsPerTrain * $trainYearlyHours) != 0) {
                $mtbf = 1 / ($failureRate * $itemsPerTrain * $trainYearlyHours);
                Log::info('MTBF Calculated:', ['mtbf' => $mtbf]);

                if ($mtbf > 16000) {
                    $riskLevel = 'Incredible';
                } elseif ($mtbf > 1600) {
                    $riskLevel = 'Improbable';
                } elseif ($mtbf > 16) {
                    $riskLevel = 'Remote';
                } elseif ($mtbf > 2) {
                    $riskLevel = 'Occasional';
                } elseif ($mtbf > 8 / 52) {
                    $riskLevel = 'Probable';
                } else {
                    $riskLevel = 'Frequent';
                }
            } else {
                Log::warning('Risk level calculation skipped: invalid inputs', [
                    'failure_rate' => $failureRate,
                    'items_per_train' => $itemsPerTrain,
                    'train_yearly_hours' => $trainYearlyHours,
                ]);
            }

            $severity = $request->is_safety ? $request->safety_risk_severity_class : $request->reliability_risk_severity_class;
            $en50126RiskLevel = $this->getEn50126RiskLevel($severity, $riskLevel);

            Log::info('Risk Levels:', [
                'risk_level' => $riskLevel,
                'en50126_risk_level' => $en50126RiskLevel,
                'is_safety' => $request->is_safety,
                'severity' => $severity,
            ]);

            $fmecaItem->update([
                'item_ref' => $request->item_ref,
                'subsystem' => $request->subsystem,
                'item_name' => $request->item_name,
                'function' => $request->function,
                'operational_mode' => $request->operational_mode,
                'is_safety' => $request->is_safety,
                'failure_mode' => $request->failure_mode,
                'failure_causes' => $request->failure_causes,
                'failure_base' => $request->failure_base,
                'ratio' => $request->ratio,
                'failure_rate' => $request->failure_rate,
                'items_per_train' => $request->items_per_train,
                'data_source' => $request->data_source,
                'failure_effect_item' => $request->failure_effect_item,
                'failure_effect_subsystem' => $request->failure_effect_subsystem,
                'failure_effect_system' => $request->failure_effect_system,
                'reference' => $request->reference,
                'safety_risk_severity_class' => $request->safety_risk_severity_class,
                'safety_risk_frequency' => $request->is_safety ? $riskLevel : null,
                'safety_risk_level' => $request->is_safety ? $en50126RiskLevel : null,
                'reliability_risk_severity_class' => $request->reliability_risk_severity_class,
                'reliability_risk_frequency' => !$request->is_safety ? $riskLevel : null,
                'reliability_risk_level' => !$request->is_safety ? $en50126RiskLevel : null,
                'failure_detection_means' => $request->failure_detection_means,
                'available_contingency' => $request->available_contingency,
                'remarks' => $request->remarks,
            ]);

            return response()->json([
                'message' => 'FMECA Item updated successfully.',
                'data' => $fmecaItem
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating FMECA item:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Failed to update item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(FmecaItem $fmecaItem)
    {
        $fmecaPartId = $fmecaItem->fmeca_part_id;
        $fmecaItem->delete();
        // Reorder remaining items
        $items = FmecaItem::where('fmeca_part_id', $fmecaPartId)->orderBy('order')->get();
        foreach ($items as $index => $item) {
            $item->update(['order' => $index + 1]);
        }
        return redirect()->route('fmeca.items.view', $fmecaPartId)
            ->with('success', 'FMECA Item deleted successfully.');
    }

    public function reorder(Request $request, FmecaPart $fmecaPart)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:fmeca_items,id',
        ]);

        foreach ($request->order as $index => $itemId) {
            FmecaItem::where('id', $itemId)
                ->where('fmeca_part_id', $fmecaPart->id)
                ->update(['order' => $index + 1]);
        }

        return response()->json(['message' => 'Order updated successfully']);
    }

    private function getEn50126RiskLevel($severity, $frequency)
    {
        $matrix = [
            'Insignificant' => [
                'Incredible' => 'Negligible',
                'Improbable' => 'Negligible',
                'Remote' => 'Negligible',
                'Occasional' => 'Tolerable',
                'Probable' => 'Tolerable',
                'Frequent' => 'Tolerable',
            ],
            'Marginal' => [
                'Incredible' => 'Negligible',
                'Improbable' => 'Negligible',
                'Remote' => 'Tolerable',
                'Occasional' => 'Tolerable',
                'Probable' => 'Undesirable',
                'Frequent' => 'Undesirable',
            ],
            'Critical' => [
                'Incredible' => 'Negligible',
                'Improbable' => 'Tolerable',
                'Remote' => 'Undesirable',
                'Occasional' => 'Undesirable',
                'Probable' => 'Intolerable',
                'Frequent' => 'Intolerable',
            ],
            'Catastrophic' => [
                'Incredible' => 'Tolerable',
                'Improbable' => 'Undesirable',
                'Remote' => 'Undesirable',
                'Occasional' => 'Intolerable',
                'Probable' => 'Intolerable',
                'Frequent' => 'Intolerable',
            ],
        ];

        return $matrix[$severity][$frequency] ?? null;
    }
    public function criticalItems()
    {
        $criticalItems = FmecaItem::where(function ($query) {
            $query->whereIn('safety_risk_level', ['Undesirable', 'Intolerable'])
                ->orWhereIn('reliability_risk_level', ['Undesirable', 'Intolerable']);
        })->with(['fmecaPart.fmecaIdentity.projectType'])->get();
        $projectTypes = ProjectType::all();

        return view('fmeca.critical-items', [
            'criticalItems' => $criticalItems,
            'projectTypes' => $projectTypes,
        ]);
    }

    public function exportCriticalItems()
    {
        $filename = 'critical_items_' . date('Ymd') . '.csv';
        return Excel::download(new CriticalItemsExport, $filename);
    }
}
