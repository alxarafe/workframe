<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Model;

use Alxarafe\Base\Model\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Pivot model for Work Part ↔ Worker with time tracking fields.
 */
class PartWorker extends Model
{
    protected $table = 'part_workers';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'work_part_id',
        'worker_id',
        'going_start',
        'going_end',
        'back_start',
        'back_end',
        'morning_from',
        'morning_to',
        'afternoon_from',
        'afternoon_to',
        'allowances',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function workPart(): BelongsTo
    {
        return $this->belongsTo(WorkPart::class, 'work_part_id');
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }
}
