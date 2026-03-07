<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Model;

use Alxarafe\Base\Model\Model;

class OrderStatus extends Model
{
    protected $table = 'order_statuses';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'visible',
        'active',
    ];

    protected $casts = [
        'visible' => 'boolean',
        'active' => 'boolean',
    ];
}
