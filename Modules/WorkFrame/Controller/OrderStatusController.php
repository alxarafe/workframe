<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Controller;

use Alxarafe\Attribute\Menu;
use Alxarafe\Base\Controller\ResourceController;
use Modules\WorkFrame\Model\OrderStatus;

#[Menu(menu: 'main_menu', label: 'Estados de órdenes', icon: 'fas fa-flag', parent: 'admin_group', order: 70, permission: 'WorkFrame.OrderStatus', route: 'WorkFrame.OrderStatus')]
class OrderStatusController extends ResourceController
{
    protected function getModelClass(): string
    {
        return OrderStatus::class;
    }

    protected function getListColumns(): array
    {
        return [
            new \Alxarafe\Component\Fields\Integer('id', 'ID', ['readonly' => true]),
            new \Alxarafe\Component\Fields\Text('name', $this->_('workframe.name')),
            new \Alxarafe\Component\Fields\Boolean('visible', $this->_('workframe.visible')),
            new \Alxarafe\Component\Fields\Boolean('active', $this->_('workframe.active')),
        ];
    }

    protected function getEditFields(): array
    {
        return [
            new \Alxarafe\Component\Fields\Text('name', $this->_('workframe.name'), ['required' => true, 'maxlength' => 25]),
            new \Alxarafe\Component\Fields\Boolean('visible', $this->_('workframe.visible'), ['default_value' => false]),
            new \Alxarafe\Component\Fields\Boolean('active', $this->_('workframe.active'), ['default_value' => true]),
        ];
    }
}
