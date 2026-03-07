<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Controller;

use Alxarafe\Attribute\Menu;
use Alxarafe\Base\Controller\ResourceController;
use Modules\WorkFrame\Model\Worker;
use Modules\WorkFrame\Model\WorkCenter;
use Modules\WorkFrame\Model\Category;

#[Menu(menu: 'main_menu', label: 'Empleados', icon: 'fas fa-hard-hat', parent: 'admin_group', order: 40, permission: 'WorkFrame.Worker', route: 'WorkFrame.Worker')]
class WorkerController extends ResourceController
{
    protected function getModelClass(): string
    {
        return Worker::class;
    }

    protected function getListColumns(): array
    {
        return [
            new \Alxarafe\Component\Fields\Integer('id', 'ID', ['readonly' => true]),
            new \Alxarafe\Component\Fields\Text('name', $this->_('workframe.name')),
            new \Alxarafe\Component\Fields\Text('workCenter.name', $this->_('workframe.work_center')),
            new \Alxarafe\Component\Fields\Text('category.name', $this->_('workframe.category')),
            new \Alxarafe\Component\Fields\Text('email', $this->_('workframe.email')),
            new \Alxarafe\Component\Fields\Boolean('active', $this->_('workframe.active')),
        ];
    }

    protected function getEditFields(): array
    {
        $workCenters = WorkCenter::where('active', true)->pluck('name', 'id')->toArray();
        $categories = Category::where('active', true)->pluck('name', 'id')->toArray();

        return [
            'main' => [
                'label' => $this->_('workframe.worker_details'),
                'fields' => [
                    new \Alxarafe\Component\Fields\Text('name', $this->_('workframe.name'), ['required' => true, 'maxlength' => 60]),
                    new \Alxarafe\Component\Fields\Select('work_center_id', $this->_('workframe.work_center'), $workCenters),
                    new \Alxarafe\Component\Fields\Select('category_id', $this->_('workframe.category'), $categories),
                    new \Alxarafe\Component\Fields\Text('email', $this->_('workframe.email'), ['maxlength' => 50]),
                    new \Alxarafe\Component\Fields\Date('available_from', $this->_('workframe.available_from')),
                    new \Alxarafe\Component\Fields\Date('available_until', $this->_('workframe.available_until')),
                    new \Alxarafe\Component\Fields\Boolean('active', $this->_('workframe.active'), ['default_value' => true]),
                ],
            ],
        ];
    }
}
