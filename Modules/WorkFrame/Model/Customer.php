<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Model;

use Alxarafe\Base\Model\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $table = 'customers';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'contact',
        'address',
        'zip',
        'locality',
        'town',
        'telephone',
        'email',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Customer $customer) {
            if ($customer->projectFiles()->exists()) {
                throw new \RuntimeException(
                    \Alxarafe\Lib\Trans::_('workframe.cannot_delete_has_project_files')
                );
            }
        });
    }

    public function notes(): HasMany
    {
        return $this->hasMany(CustomerNote::class, 'customer_id');
    }

    public function projectFiles(): HasMany
    {
        return $this->hasMany(ProjectFile::class, 'customer_id');
    }
}
