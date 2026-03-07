<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Model;

use Alxarafe\Base\Model\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectFile extends Model
{
    protected $table = 'project_files';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'customer_id',
        'date',
        'locality',
        'town',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'date' => 'date',
    ];

    protected static function booted(): void
    {
        static::deleting(function (ProjectFile $file) {
            if ($file->workOrders()->exists()) {
                throw new \RuntimeException(
                    \Alxarafe\Lib\Trans::_('workframe.cannot_delete_has_work_orders')
                );
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ProjectFileNote::class, 'project_file_id');
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class, 'project_file_id');
    }
}
