<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Model;

use Alxarafe\Base\Model\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Vehicle extends Model
{
    protected $table = 'vehicles';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'work_center_id',
        'license_plate',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function workCenter(): BelongsTo
    {
        return $this->belongsTo(WorkCenter::class, 'work_center_id');
    }

    public function workOrders(): BelongsToMany
    {
        return $this->belongsToMany(WorkOrder::class, 'work_order_vehicles', 'vehicle_id', 'work_order_id');
    }
}
