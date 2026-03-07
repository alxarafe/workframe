<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\WorkFrame\Model\Customer;
use Modules\WorkFrame\Model\CustomerNote;
use Modules\WorkFrame\Model\ProjectFile;
use Modules\WorkFrame\Model\ProjectFileNote;
use Modules\WorkFrame\Model\WorkOrder;
use Modules\WorkFrame\Model\WorkOrderNote;
use Modules\WorkFrame\Model\WorkPart;
use Modules\WorkFrame\Model\Worker;
use Modules\WorkFrame\Model\Vehicle;
use Modules\WorkFrame\Model\WorkCenter;
use Modules\WorkFrame\Model\Category;
use Modules\WorkFrame\Model\Section;
use Modules\WorkFrame\Model\OrderStatus;

class ModelTest extends TestCase
{
    public function testSectionModelHasCorrectTable(): void
    {
        $model = new Section();
        $this->assertEquals('sections', $model->getTable());
        $this->assertFalse($model->usesTimestamps());
    }

    public function testCategoryModelHasCorrectTable(): void
    {
        $model = new Category();
        $this->assertEquals('categories', $model->getTable());
    }

    public function testWorkCenterModelHasCorrectTable(): void
    {
        $model = new WorkCenter();
        $this->assertEquals('work_centers', $model->getTable());
    }

    public function testWorkerModelHasCorrectTable(): void
    {
        $model = new Worker();
        $this->assertEquals('workers', $model->getTable());
        $this->assertContains('name', $model->getFillable());
        $this->assertContains('work_center_id', $model->getFillable());
        $this->assertContains('category_id', $model->getFillable());
        $this->assertContains('email', $model->getFillable());
        $this->assertContains('available_from', $model->getFillable());
        $this->assertContains('available_until', $model->getFillable());
    }

    public function testVehicleModelHasCorrectTable(): void
    {
        $model = new Vehicle();
        $this->assertEquals('vehicles', $model->getTable());
        $this->assertContains('license_plate', $model->getFillable());
    }

    public function testCustomerModelHasCorrectTable(): void
    {
        $model = new Customer();
        $this->assertEquals('customers', $model->getTable());
        $this->assertContains('telephone', $model->getFillable());
        $this->assertContains('email', $model->getFillable());
    }

    public function testCustomerNoteModelHasCorrectTable(): void
    {
        $model = new CustomerNote();
        $this->assertEquals('customer_notes', $model->getTable());
        $this->assertContains('customer_id', $model->getFillable());
    }

    public function testProjectFileModelHasCorrectTable(): void
    {
        $model = new ProjectFile();
        $this->assertEquals('project_files', $model->getTable());
        $this->assertContains('customer_id', $model->getFillable());
    }

    public function testProjectFileNoteModelHasCorrectTable(): void
    {
        $model = new ProjectFileNote();
        $this->assertEquals('project_file_notes', $model->getTable());
    }

    public function testOrderStatusModelHasCorrectTable(): void
    {
        $model = new OrderStatus();
        $this->assertEquals('order_statuses', $model->getTable());
        $this->assertContains('visible', $model->getFillable());
    }

    public function testWorkOrderModelHasCorrectTable(): void
    {
        $model = new WorkOrder();
        $this->assertEquals('work_orders', $model->getTable());
        $this->assertContains('project_file_id', $model->getFillable());
        $this->assertContains('foreman_id', $model->getFillable());
        $this->assertContains('start_time', $model->getFillable());
    }

    public function testWorkOrderNoteModelHasCorrectTable(): void
    {
        $model = new WorkOrderNote();
        $this->assertEquals('work_order_notes', $model->getTable());
        $this->assertContains('work_order_id', $model->getFillable());
    }

    public function testWorkPartModelHasCorrectTable(): void
    {
        $model = new WorkPart();
        $this->assertEquals('work_parts', $model->getTable());
        $this->assertContains('work_order_id', $model->getFillable());
        $this->assertContains('has_image', $model->getFillable());
        $this->assertContains('has_invoice', $model->getFillable());
    }

    public function testAllModelsHaveTimestampsDisabled(): void
    {
        $models = [
            Section::class,
            Category::class,
            WorkCenter::class,
            Worker::class,
            Vehicle::class,
            Customer::class,
            CustomerNote::class,
            ProjectFile::class,
            ProjectFileNote::class,
            OrderStatus::class,
            WorkOrder::class,
            WorkOrderNote::class,
            WorkPart::class,
        ];

        foreach ($models as $modelClass) {
            $model = new $modelClass();
            $this->assertFalse($model->usesTimestamps(), "$modelClass should have timestamps disabled");
        }
    }

    public function testWorkerModelHasCorrectCasts(): void
    {
        $model = new Worker();
        $casts = $model->getCasts();
        $this->assertEquals('boolean', $casts['active']);
        $this->assertEquals('date', $casts['available_from']);
        $this->assertEquals('date', $casts['available_until']);
    }

    public function testWorkOrderModelHasCorrectCasts(): void
    {
        $model = new WorkOrder();
        $casts = $model->getCasts();
        $this->assertEquals('boolean', $casts['active']);
        $this->assertEquals('date', $casts['date']);
        $this->assertEquals('date', $casts['end_date']);
    }
}
