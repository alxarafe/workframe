<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Controller;

use Alxarafe\Attribute\Menu;
use Alxarafe\Base\Controller\ResourceController;
use Modules\WorkFrame\Model\WorkPart;
use Modules\WorkFrame\Model\WorkOrder;
use Modules\WorkFrame\Model\Worker;

#[Menu(menu: 'main_menu', label: 'Partes de trabajo', icon: 'fas fa-file-alt', order: 40, permission: 'WorkFrame.WorkPart', route: 'WorkFrame.WorkPart')]
class WorkPartController extends ResourceController
{
    protected bool $useTabs = true;

    protected function getModelClass(): string
    {
        return WorkPart::class;
    }

    protected function getListColumns(): array
    {
        return [
            new \Alxarafe\Component\Fields\Integer('id', 'ID', ['readonly' => true]),
            new \Alxarafe\Component\Fields\Text('name', $this->_('workframe.name')),
            new \Alxarafe\Component\Fields\Text('workOrder.name', $this->_('workframe.work_order')),
            new \Alxarafe\Component\Fields\Text('foreman.name', $this->_('workframe.foreman')),
            new \Alxarafe\Component\Fields\Date('date', $this->_('workframe.date')),
            new \Alxarafe\Component\Fields\Boolean('special_time', $this->_('workframe.special_time')),
        ];
    }

    protected function getEditFields(): array
    {
        $workOrders = WorkOrder::where('active', true)
            ->where('status', 1) // Only active orders
            ->pluck('name', 'id')
            ->toArray();

        $foremen = Worker::where('active', true)->pluck('name', 'id')->toArray();

        return [
            'main' => [
                'label' => $this->_('workframe.work_part_details'),
                'fields' => [
                    new \Alxarafe\Component\Fields\Text('name', $this->_('workframe.name'), ['required' => true, 'maxlength' => 60]),
                    new \Alxarafe\Component\Fields\Select('work_order_id', $this->_('workframe.work_order'), $workOrders, ['required' => true]),
                    new \Alxarafe\Component\Fields\Select('foreman_id', $this->_('workframe.foreman'), $foremen),
                    new \Alxarafe\Component\Fields\Date('date', $this->_('workframe.date')),
                    new \Alxarafe\Component\Fields\Boolean('special_time', $this->_('workframe.special_time')),
                    new \Alxarafe\Component\Fields\Textarea('notes', $this->_('workframe.notes')),
                ],
            ],
            'workers' => [
                'label' => $this->_('workframe.assigned_workers'),
                'fields' => [
                    new \Alxarafe\Component\Fields\RelationList('partWorkers', $this->_('workframe.assigned_workers'), [
                        'model' => \Modules\WorkFrame\Model\PartWorker::class,
                        'foreign_key' => 'work_part_id',
                        'columns' => [
                            ['field' => 'worker_id', 'label' => 'Operario', 'type' => 'select', 'values' => $foremen],
                            ['field' => 'morning_from', 'label' => 'Mañana desde', 'type' => 'time'],
                            ['field' => 'morning_to', 'label' => 'Mañana hasta', 'type' => 'time'],
                            ['field' => 'afternoon_from', 'label' => 'Tarde desde', 'type' => 'time'],
                            ['field' => 'afternoon_to', 'label' => 'Tarde hasta', 'type' => 'time'],
                            ['field' => 'going_start', 'label' => 'Ida desde', 'type' => 'time'],
                            ['field' => 'going_end', 'label' => 'Ida hasta', 'type' => 'time'],
                            ['field' => 'back_start', 'label' => 'Vuelta desde', 'type' => 'time'],
                            ['field' => 'back_end', 'label' => 'Vuelta hasta', 'type' => 'time'],
                            ['field' => 'allowances', 'label' => 'Dieta', 'type' => 'text'],
                        ],
                    ]),
                ],
            ],
        ];
    }
}
