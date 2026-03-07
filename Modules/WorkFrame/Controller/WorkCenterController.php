<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Controller;

use Alxarafe\Attribute\Menu;
use Alxarafe\Base\Controller\ResourceController;
use Modules\WorkFrame\Model\WorkCenter;

#[Menu(menu: 'main_menu', label: 'Delegaciones', icon: 'fas fa-building', parent: 'admin_group', order: 30, permission: 'WorkFrame.WorkCenter', route: 'WorkFrame.WorkCenter')]
class WorkCenterController extends ResourceController
{
    protected function getModelClass(): string
    {
        return WorkCenter::class;
    }

    protected function getListColumns(): array
    {
        return [
            new \Alxarafe\Component\Fields\Integer('id', 'ID', ['readonly' => true]),
            new \Alxarafe\Component\Fields\Text('name', $this->_('workframe.name')),
            new \Alxarafe\Component\Fields\Boolean('active', $this->_('workframe.active')),
        ];
    }

    protected function getEditFields(): array
    {
        return [
            new \Alxarafe\Component\Fields\Text('name', $this->_('workframe.name'), ['required' => true, 'maxlength' => 60]),
            new \Alxarafe\Component\Fields\Boolean('active', $this->_('workframe.active'), ['default_value' => true]),
        ];
    }
}
