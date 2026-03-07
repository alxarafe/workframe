<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Controller;

use Alxarafe\Attribute\Menu;
use Alxarafe\Base\Controller\ResourceController;
use Modules\WorkFrame\Model\ProjectFile;
use Modules\WorkFrame\Model\Customer;

#[Menu(menu: 'main_menu', label: 'Expedientes', icon: 'fas fa-folder-open', order: 20, permission: 'WorkFrame.ProjectFile', route: 'WorkFrame.ProjectFile')]
class ProjectFileController extends ResourceController
{
    protected function getModelClass(): string
    {
        return ProjectFile::class;
    }

    protected function getListColumns(): array
    {
        return [
            new \Alxarafe\Component\Fields\Integer('id', 'ID', ['readonly' => true]),
            new \Alxarafe\Component\Fields\Text('name', $this->_('workframe.name')),
            new \Alxarafe\Component\Fields\Text('customer.name', $this->_('workframe.customer')),
            new \Alxarafe\Component\Fields\Date('date', $this->_('workframe.date')),
            new \Alxarafe\Component\Fields\Text('locality', $this->_('workframe.locality')),
            new \Alxarafe\Component\Fields\Text('town', $this->_('workframe.town')),
            new \Alxarafe\Component\Fields\Boolean('active', $this->_('workframe.active')),
        ];
    }

    protected function getEditFields(): array
    {
        $customers = Customer::where('active', true)->pluck('name', 'id')->toArray();

        return [
            'main' => [
                'label' => $this->_('workframe.project_file_details'),
                'fields' => [
                    new \Alxarafe\Component\Fields\Text('name', $this->_('workframe.name'), ['required' => true, 'maxlength' => 60]),
                    new \Alxarafe\Component\Fields\Select('customer_id', $this->_('workframe.customer'), $customers, ['required' => true]),
                    new \Alxarafe\Component\Fields\Date('date', $this->_('workframe.date')),
                    new \Alxarafe\Component\Fields\Text('locality', $this->_('workframe.locality'), ['maxlength' => 50]),
                    new \Alxarafe\Component\Fields\Text('town', $this->_('workframe.town'), ['maxlength' => 50]),
                    new \Alxarafe\Component\Fields\Boolean('active', $this->_('workframe.active'), ['default_value' => true]),
                ],
            ],
            'notes' => [
                'label' => $this->_('workframe.notes'),
                'fields' => [
                    new \Alxarafe\Component\Fields\RelationList('notes', $this->_('workframe.notes'), [
                        'model' => \Modules\WorkFrame\Model\ProjectFileNote::class,
                        'foreign_key' => 'project_file_id',
                        'columns' => [
                            ['field' => 'notes', 'label' => 'Nota', 'type' => 'textarea'],
                            ['field' => 'created_at', 'label' => 'Fecha', 'type' => 'datetime', 'readonly' => true],
                        ],
                    ]),
                ],
            ],
        ];
    }
}
