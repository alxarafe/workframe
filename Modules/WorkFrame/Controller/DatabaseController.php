<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Controller;

use Alxarafe\Base\Controller\Controller;
use Alxarafe\Lib\Messages;
use Modules\WorkFrame\Service\DatabaseMigration;

/**
 * Database setup/migration controller.
 */
class DatabaseController extends Controller
{
    public function doIndex(): bool
    {
        DatabaseMigration::run();
        Messages::addMessage('Database tables verified/created.');

        if (isset($_GET['seed']) && $_GET['seed'] === 'true') {
            DatabaseMigration::seed();
            Messages::addMessage('Seed data inserted.');
        }

        $this->setDefaultTemplate('database_status');
        return true;
    }
}
