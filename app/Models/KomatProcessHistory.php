<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class KomatProcessHistory extends Model
{
    use HasFactory;

    protected $table = 'komat_process_history';

    protected $fillable = [
        'komat_requirement_id',
        'discussion_number',
        'komat_process_id',
        'unit_distributor_id',
        'documentstatus',
        'logisticauthoritylevel',
        'status',
        'note',
        'revision',
        'komat_supplier_id',
        'project_type_id',
        'rejectedreason',
        'no_prefix',
        'no_counter',
        'no_midcode',
        'no_year',
        'no_dokumen',
    ];

    protected static function boot()
    {
        parent::boot();

        // Handle creation
        static::creating(function ($model) {
            Log::info('KomatProcessHistory creating event', [
                'unit_distributor_id' => $model->unit_distributor_id,
            ]);

            // Generate only for specific unit_distributor_id
            if (!in_array($model->unit_distributor_id, [2, 5, 6, 7, 8, 9, 10, 11, 12])) {
                Log::info('Skipping no_dokumen generation: unit_distributor_id not in allowed list', [
                    'unit_distributor_id' => $model->unit_distributor_id,
                ]);
                return;
            }

            // Set default values
            $model->no_prefix = $model->no_prefix ?? 'AP';
            $model->no_year = $model->no_year ?? date('Y');
            $model->no_midcode = $model->no_midcode ?? self::determineMidCode($model->unit_distributor_id);

            // Stop if no valid midcode
            if (!$model->no_midcode) {
                Log::warning('No valid midcode for unit_distributor_id', [
                    'unit_distributor_id' => $model->unit_distributor_id,
                ]);
                return;
            }

            // Get the next counter
            $model->no_counter = self::nextCounter($model->no_midcode, $model->no_year);

            // Generate document number: AP001/311/2025
            $model->no_dokumen = "{$model->no_prefix}"
                . str_pad($model->no_counter, 3, '0', STR_PAD_LEFT)
                . "/{$model->no_midcode}/{$model->no_year}";

            Log::info('Generated no_dokumen on create', [
                'document_id' => $model->id,
                'no_dokumen' => $model->no_dokumen,
                'no_prefix' => $model->no_prefix,
                'no_counter' => $model->no_counter,
                'no_midcode' => $model->no_midcode,
                'no_year' => $model->no_year,
            ]);
        });
    }

    /**
     * Determine midcode based on unit_distributor_id
     */
    public static function determineMidCode($unitDistributorId)
    {
        $midcode = match ($unitDistributorId) {
            2 => '311',
            5 => '312',
            6 => '312',
            7 => '312',
            8 => '312',
            9 => '313',
            10 => '313',
            11 => '313',
            12 => '313',
            default => null,
        };

        Log::info('Determined midcode', [
            'unit_distributor_id' => $unitDistributorId,
            'midcode' => $midcode,
        ]);

        return $midcode;
    }

    /**
     * Get the next counter based on midcode and year
     */
    public static function nextCounter($midcode, $year)
    {
        $last = self::where('no_midcode', $midcode)
            ->where('no_year', $year)
            ->orderByDesc('no_counter')
            ->first();

        $counter = $last ? $last->no_counter + 1 : 1;

        Log::info('Determined next counter', [
            'midcode' => $midcode,
            'year' => $year,
            'counter' => $counter,
        ]);

        return $counter;
    }

    /*----------------------------------------
    | RELASI
    -----------------------------------------*/

    public function feedbacks()
    {
        return $this->hasMany(KomatFeedback::class, 'komat_process_history_id');
    }

    public function komatProcess()
    {
        return $this->belongsTo(KomatProcess::class, 'komat_process_id');
    }

    public function unitDistributor()
    {
        return $this->belongsTo(Unit::class, 'unit_distributor_id');
    }

    public function komatRequirements()
    {
        return $this->belongsToMany(
            KomatRequirement::class,
            'komat_hist_req',
            'komat_process_history_id',
            'komat_requirement_id'
        )->withTimestamps();
    }

    public function komatHistReqs()
    {
        return $this->hasMany(KomatHistReq::class, 'komat_process_history_id');
    }

    public function systemLogs()
    {
        return $this->morphMany(SystemLog::class, 'loggable');
    }

    public function notifsystem()
    {
        return $this->morphMany(Notification::class, 'notifmessage');
    }

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class, 'project_type_id');
    }

    public function requirement()
    {
        return $this->belongsTo(KomatRequirement::class, 'komat_requirement_id');
    }

    public function supplier()
    {
        return $this->belongsTo(KomatSupplier::class, 'komat_supplier_id');
    }
}
