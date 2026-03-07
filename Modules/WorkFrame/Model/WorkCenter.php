<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Model;

use Alxarafe\Base\Model\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkCenter extends Model
{
    protected $table = 'work_centers';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function workers(): HasMany
    {
        return $this->hasMany(Worker::class, 'work_center_id');
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'work_center_id');
    }
}
