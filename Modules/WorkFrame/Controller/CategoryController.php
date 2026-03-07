<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Controller;

use Alxarafe\Attribute\Menu;
use Alxarafe\Base\Controller\ResourceController;
use Modules\WorkFrame\Model\Category;

#[Menu(
    menu: 'main_menu',
    label: 'Categorías',
    icon: 'fas fa-tags',
    parent: 'admin_group',
    order: 20,
    permission: 'WorkFrame.Category',
    route: 'WorkFrame.Category'
)]
class CategoryController extends ResourceController
{
    protected function getModelClass(): string
    {
        return Category::class;
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
