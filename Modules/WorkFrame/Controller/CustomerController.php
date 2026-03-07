<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Controller;

use Alxarafe\Attribute\Menu;
use Alxarafe\Base\Controller\ResourceController;
use Modules\WorkFrame\Model\Customer;

#[Menu(menu: 'main_menu', label: 'Clientes', icon: 'fas fa-users', order: 10, permission: 'WorkFrame.Customer', route: 'WorkFrame.Customer')]
class CustomerController extends ResourceController
{
    protected function getModelClass(): string
    {
        return Customer::class;
    }

    protected function getListColumns(): array
    {
        return [
            new \Alxarafe\Component\Fields\Integer('id', 'ID', ['readonly' => true]),
            new \Alxarafe\Component\Fields\Text('name', $this->_('workframe.name')),
            new \Alxarafe\Component\Fields\Text('contact', $this->_('workframe.contact')),
            new \Alxarafe\Component\Fields\Text('locality', $this->_('workframe.locality')),
            new \Alxarafe\Component\Fields\Text('town', $this->_('workframe.town')),
            new \Alxarafe\Component\Fields\Text('telephone', $this->_('workframe.telephone')),
            new \Alxarafe\Component\Fields\Text('email', $this->_('workframe.email')),
            new \Alxarafe\Component\Fields\Boolean('active', $this->_('workframe.active')),
        ];
    }

    protected function getEditFields(): array
    {
        return [
            'main' => [
                'label' => $this->_('workframe.customer_details'),
                'fields' => [
                    new \Alxarafe\Component\Fields\Text('name', $this->_('workframe.name'), ['required' => true, 'maxlength' => 60]),
                    new \Alxarafe\Component\Fields\Text('contact', $this->_('workframe.contact'), ['maxlength' => 60]),
                    new \Alxarafe\Component\Fields\Text('address', $this->_('workframe.address'), ['maxlength' => 50]),
                    new \Alxarafe\Component\Fields\Text('zip', $this->_('workframe.zip'), ['maxlength' => 10]),
                    new \Alxarafe\Component\Fields\Text('locality', $this->_('workframe.locality'), ['maxlength' => 50]),
                    new \Alxarafe\Component\Fields\Text('town', $this->_('workframe.town'), ['maxlength' => 25]),
                    new \Alxarafe\Component\Fields\Text('telephone', $this->_('workframe.telephone'), ['maxlength' => 15]),
                    new \Alxarafe\Component\Fields\Text('email', $this->_('workframe.email'), ['maxlength' => 50]),
                    new \Alxarafe\Component\Fields\Boolean('active', $this->_('workframe.active'), ['default_value' => true]),
                ],
            ],
            'notes' => [
                'label' => $this->_('workframe.notes'),
                'fields' => [
                    new \Alxarafe\Component\Fields\RelationList('notes', $this->_('workframe.notes'), [
                        'model' => \Modules\WorkFrame\Model\CustomerNote::class,
                        'foreign_key' => 'customer_id',
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
