<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Model;

use Alxarafe\Base\Model\Model;

class Section extends Model
{
    protected $table = 'sections';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
