<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Controller;

use Alxarafe\Attribute\Menu;
use Alxarafe\Base\Controller\ResourceController;
use Modules\WorkFrame\Model\Vehicle;
use Modules\WorkFrame\Model\WorkCenter;

#[Menu(menu: 'main_menu', label: 'Vehículos', icon: 'fas fa-truck', parent: 'admin_group', order: 50, permission: 'WorkFrame.Vehicle', route: 'WorkFrame.Vehicle')]
class VehicleController extends ResourceController
{
    protected function getModelClass(): string
    {
        return Vehicle::class;
    }

    protected function getListColumns(): array
    {
        return [
            new \Alxarafe\Component\Fields\Integer('id', 'ID', ['readonly' => true]),
            new \Alxarafe\Component\Fields\Text('name', $this->_('workframe.name')),
            new \Alxarafe\Component\Fields\Text('license_plate', $this->_('workframe.license_plate')),
            new \Alxarafe\Component\Fields\Text('workCenter.name', $this->_('workframe.work_center')),
            new \Alxarafe\Component\Fields\Boolean('active', $this->_('workframe.active')),
        ];
    }

    protected function getEditFields(): array
    {
        $workCenters = WorkCenter::where('active', true)->pluck('name', 'id')->toArray();

        return [
            'main' => [
                'label' => $this->_('workframe.vehicle_details'),
                'fields' => [
                    new \Alxarafe\Component\Fields\Text('name', $this->_('workframe.name'), ['required' => true, 'maxlength' => 60]),
                    new \Alxarafe\Component\Fields\Select('work_center_id', $this->_('workframe.work_center'), $workCenters),
                    new \Alxarafe\Component\Fields\Text('license_plate', $this->_('workframe.license_plate'), ['maxlength' => 15]),
                    new \Alxarafe\Component\Fields\Boolean('active', $this->_('workframe.active'), ['default_value' => true]),
                ],
            ],
        ];
    }
}
