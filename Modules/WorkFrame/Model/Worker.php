<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Model;

use Alxarafe\Base\Model\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Worker extends Model
{
    protected $table = 'workers';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'work_center_id',
        'category_id',
        'email',
        'available_from',
        'available_until',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'available_from' => 'date',
        'available_until' => 'date',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Worker $worker) {
            if ($worker->foremanOrders()->where('status', 1)->exists()) {
                throw new \RuntimeException(
                    \Alxarafe\Lib\Trans::_('workframe.cannot_delete_is_foreman')
                );
            }
        });
    }

    public function workCenter(): BelongsTo
    {
        return $this->belongsTo(WorkCenter::class, 'work_center_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function workOrders(): BelongsToMany
    {
        return $this->belongsToMany(WorkOrder::class, 'work_order_workers', 'worker_id', 'work_order_id');
    }

    public function foremanOrders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WorkOrder::class, 'foreman_id');
    }
}
