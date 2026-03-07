<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\WorkFrame\Controller\SectionController;
use Modules\WorkFrame\Controller\CategoryController;
use Modules\WorkFrame\Controller\WorkCenterController;
use Modules\WorkFrame\Controller\WorkerController;
use Modules\WorkFrame\Controller\VehicleController;
use Modules\WorkFrame\Controller\OrderStatusController;
use Modules\WorkFrame\Controller\CustomerController;
use Modules\WorkFrame\Controller\ProjectFileController;
use Modules\WorkFrame\Controller\WorkOrderController;
use Modules\WorkFrame\Controller\WorkPartController;
use Modules\WorkFrame\Controller\DashboardController;
use Modules\WorkFrame\Controller\SearchController;
use Modules\WorkFrame\Controller\ReportController;
use Modules\WorkFrame\Controller\IndexController;
use Modules\WorkFrame\Controller\MailController;
use Modules\WorkFrame\Controller\DatabaseController;

class ControllerTest extends TestCase
{
    public function testAllControllersExist(): void
    {
        $controllers = [
            SectionController::class,
            CategoryController::class,
            WorkCenterController::class,
            WorkerController::class,
            VehicleController::class,
            OrderStatusController::class,
            CustomerController::class,
            ProjectFileController::class,
            WorkOrderController::class,
            WorkPartController::class,
            DashboardController::class,
            SearchController::class,
            ReportController::class,
            IndexController::class,
            MailController::class,
            DatabaseController::class,
        ];

        foreach ($controllers as $controller) {
            $this->assertTrue(class_exists($controller), "Controller $controller should exist");
        }
    }

    public function testApiControllersExist(): void
    {
        $apis = [
            \Modules\WorkFrame\Api\CustomerApiController::class,
            \Modules\WorkFrame\Api\WorkOrderApiController::class,
            \Modules\WorkFrame\Api\WorkPartApiController::class,
        ];

        foreach ($apis as $api) {
            $this->assertTrue(class_exists($api), "API Controller $api should exist");
        }
    }

    public function testResourceControllersHaveRequiredMethods(): void
    {
        $controllers = [
            SectionController::class,
            CategoryController::class,
            WorkCenterController::class,
            WorkerController::class,
            VehicleController::class,
            OrderStatusController::class,
            CustomerController::class,
            ProjectFileController::class,
            WorkOrderController::class,
            WorkPartController::class,
        ];

        foreach ($controllers as $controller) {
            $reflection = new \ReflectionClass($controller);
            $this->assertTrue($reflection->isSubclassOf(\Alxarafe\Base\Controller\ResourceController::class), "$controller should extend ResourceController");
            $this->assertTrue($reflection->hasMethod('getModelClass'), "$controller should define getModelClass()");
        }
    }
}
