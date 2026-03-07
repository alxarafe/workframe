<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Model;

use Alxarafe\Base\Model\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder extends Model
{
    protected $table = 'work_orders';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'project_file_id',
        'date',
        'end_date',
        'start_time',
        'foreman_id',
        'address',
        'zip',
        'locality',
        'town',
        'status',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'date' => 'date',
        'end_date' => 'date',
    ];

    protected static function booted(): void
    {
        // Inherit name from project file if empty
        static::saving(function (WorkOrder $order) {
            if (empty($order->name) && $order->project_file_id) {
                $order->name = $order->projectFile?->name ?? '';
            }
            // Default end_date to date if empty
            if (empty($order->end_date) && !empty($order->date)) {
                $order->end_date = $order->date;
            }
        });

        static::deleting(function (WorkOrder $order) {
            if ($order->workParts()->exists()) {
                throw new \RuntimeException(
                    \Alxarafe\Lib\Trans::_('workframe.cannot_delete_is_foreman')
                );
            }
        });
    }

    public function projectFile(): BelongsTo
    {
        return $this->belongsTo(ProjectFile::class, 'project_file_id');
    }

    public function foreman(): BelongsTo
    {
        return $this->belongsTo(Worker::class, 'foreman_id');
    }

    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'status');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(WorkOrderNote::class, 'work_order_id');
    }

    public function workParts(): HasMany
    {
        return $this->hasMany(WorkPart::class, 'work_order_id');
    }

    public function workers(): BelongsToMany
    {
        return $this->belongsToMany(Worker::class, 'work_order_workers', 'work_order_id', 'worker_id');
    }

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'work_order_vehicles', 'work_order_id', 'vehicle_id');
    }
}
