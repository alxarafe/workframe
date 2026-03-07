<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Model;

use Alxarafe\Base\Model\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WorkPart extends Model
{
    protected $table = 'work_parts';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'work_order_id',
        'foreman_id',
        'special_time',
        'has_image',
        'has_invoice',
        'notes',
        'date',
    ];

    protected $casts = [
        'special_time' => 'boolean',
        'date' => 'date',
    ];

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    public function foreman(): BelongsTo
    {
        return $this->belongsTo(Worker::class, 'foreman_id');
    }

    public function workers(): BelongsToMany
    {
        return $this->belongsToMany(Worker::class, 'part_workers', 'work_part_id', 'worker_id')
            ->withPivot([
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
            ]);
    }

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'part_vehicles', 'work_part_id', 'vehicle_id');
    }
}
