<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Service;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;

/**
 * Database migration service: creates all WorkFrame tables with PSR-compliant naming.
 */
class DatabaseMigration
{
    public static function run(): void
    {
        $schema = DB::schema();

        // Simple lookup tables
        if (!$schema->hasTable('sections')) {
            $schema->create('sections', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 60);
                $table->boolean('active')->default(true);
                $table->index('name');
            });
        }

        if (!$schema->hasTable('categories')) {
            $schema->create('categories', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 60);
                $table->boolean('active')->default(true);
                $table->index('name');
            });
        }

        if (!$schema->hasTable('work_centers')) {
            $schema->create('work_centers', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 60);
                $table->boolean('active')->default(true);
                $table->index('name');
            });
        }

        if (!$schema->hasTable('order_statuses')) {
            $schema->create('order_statuses', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 25);
                $table->boolean('visible')->default(false);
                $table->boolean('active')->default(true);
                $table->index('name');
            });
        }

        // Workers and Vehicles
        if (!$schema->hasTable('workers')) {
            $schema->create('workers', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 60);
                $table->unsignedInteger('work_center_id')->nullable();
                $table->unsignedInteger('category_id')->nullable();
                $table->string('email', 50)->nullable();
                $table->date('available_from')->nullable();
                $table->date('available_until')->nullable();
                $table->boolean('active')->default(true);
            });
        }

        if (!$schema->hasTable('vehicles')) {
            $schema->create('vehicles', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 60);
                $table->unsignedInteger('work_center_id')->nullable();
                $table->string('license_plate', 15)->nullable();
                $table->boolean('active')->default(true);
            });
        }

        // Customers
        if (!$schema->hasTable('customers')) {
            $schema->create('customers', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 60);
                $table->string('contact', 60)->nullable();
                $table->string('address', 50)->nullable();
                $table->string('zip', 10)->nullable();
                $table->string('locality', 50)->nullable();
                $table->string('town', 25)->nullable();
                $table->string('telephone', 15)->nullable();
                $table->string('email', 50)->nullable();
                $table->boolean('active')->default(true);
            });
        }

        if (!$schema->hasTable('customer_notes')) {
            $schema->create('customer_notes', function (Blueprint $table) {
                $table->timestamp('id')->useCurrent();
                $table->unsignedInteger('customer_id');
                $table->boolean('source')->default(false);
                $table->text('notes')->nullable();
                $table->primary('id');
            });
        }

        // Project Files (Expedientes)
        if (!$schema->hasTable('project_files')) {
            $schema->create('project_files', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 60);
                $table->unsignedInteger('customer_id');
                $table->date('date')->nullable();
                $table->string('locality', 50)->nullable();
                $table->string('town', 50)->nullable();
                $table->boolean('active')->default(true);
            });
        }

        if (!$schema->hasTable('project_file_notes')) {
            $schema->create('project_file_notes', function (Blueprint $table) {
                $table->timestamp('id')->useCurrent();
                $table->unsignedInteger('project_file_id');
                $table->boolean('source')->default(false);
                $table->text('notes')->nullable();
                $table->primary('id');
            });
        }

        // Work Orders
        if (!$schema->hasTable('work_orders')) {
            $schema->create('work_orders', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 60);
                $table->unsignedInteger('project_file_id')->nullable();
                $table->date('date')->nullable();
                $table->date('end_date')->nullable()->default('9999-12-31');
                $table->time('start_time')->nullable()->default('09:00:00');
                $table->unsignedInteger('foreman_id')->nullable();
                $table->string('address', 50)->nullable();
                $table->string('zip', 10)->nullable();
                $table->string('locality', 50)->nullable();
                $table->string('town', 50)->nullable();
                $table->tinyInteger('status')->default(1);
                $table->boolean('active')->default(true);
            });
        }

        if (!$schema->hasTable('work_order_notes')) {
            $schema->create('work_order_notes', function (Blueprint $table) {
                $table->timestamp('id')->useCurrent();
                $table->unsignedInteger('work_order_id');
                $table->boolean('source')->default(false);
                $table->text('notes')->nullable();
                $table->primary('id');
            });
        }

        // Pivot: Work Order ↔ Workers
        if (!$schema->hasTable('work_order_workers')) {
            $schema->create('work_order_workers', function (Blueprint $table) {
                $table->unsignedInteger('work_order_id');
                $table->unsignedInteger('worker_id');
                $table->primary(['work_order_id', 'worker_id']);
            });
        }

        // Pivot: Work Order ↔ Vehicles
        if (!$schema->hasTable('work_order_vehicles')) {
            $schema->create('work_order_vehicles', function (Blueprint $table) {
                $table->unsignedInteger('work_order_id');
                $table->unsignedInteger('vehicle_id');
                $table->primary(['work_order_id', 'vehicle_id']);
            });
        }

        // Work Parts
        if (!$schema->hasTable('work_parts')) {
            $schema->create('work_parts', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 60);
                $table->unsignedInteger('work_order_id');
                $table->unsignedInteger('foreman_id')->nullable();
                $table->boolean('special_time')->default(false);
                $table->char('has_image', 3)->nullable();
                $table->char('has_invoice', 3)->nullable();
                $table->text('notes')->nullable();
                $table->date('date')->nullable();
            });
        }

        // Pivot: Part ↔ Vehicles
        if (!$schema->hasTable('part_vehicles')) {
            $schema->create('part_vehicles', function (Blueprint $table) {
                $table->unsignedInteger('work_part_id');
                $table->unsignedInteger('vehicle_id');
                $table->primary(['work_part_id', 'vehicle_id']);
            });
        }

        // Pivot: Part ↔ Workers (with time tracking)
        if (!$schema->hasTable('part_workers')) {
            $schema->create('part_workers', function (Blueprint $table) {
                $table->unsignedInteger('work_part_id');
                $table->unsignedInteger('worker_id');
                $table->time('going_start')->nullable();
                $table->time('going_end')->nullable();
                $table->time('back_start')->nullable();
                $table->time('back_end')->nullable();
                $table->time('morning_from')->nullable();
                $table->time('morning_to')->nullable();
                $table->time('afternoon_from')->nullable();
                $table->time('afternoon_to')->nullable();
                $table->char('allowances', 1)->nullable();
                $table->boolean('active')->default(true);
                $table->primary(['work_part_id', 'worker_id']);
            });
        }

        // Mail config
        if (!$schema->hasTable('mail_configs')) {
            $schema->create('mail_configs', function (Blueprint $table) {
                $table->increments('id');
                $table->string('host', 100)->nullable();
                $table->integer('port')->default(993);
                $table->string('username', 100)->nullable();
                $table->string('password', 100)->nullable();
                $table->boolean('ssl')->default(true);
            });
        }
    }

    /**
     * Seed initial data.
     */
    public static function seed(): void
    {
        // Sections
        if (DB::table('sections')->count() === 0) {
            DB::table('sections')->insert([
                ['name' => 'Construction Materials', 'active' => true],
                ['name' => 'Plumbing', 'active' => true],
                ['name' => 'Miscellaneous Materials', 'active' => true],
                ['name' => 'Tools', 'active' => true],
            ]);
        }

        // Categories
        if (DB::table('categories')->count() === 0) {
            DB::table('categories')->insert([
                ['name' => 'Plumber', 'active' => true],
                ['name' => 'Carpenter', 'active' => true],
            ]);
        }

        // Work Centers
        if (DB::table('work_centers')->count() === 0) {
            DB::table('work_centers')->insert([
                ['name' => 'Madrid', 'active' => true],
                ['name' => 'Sevilla', 'active' => true],
            ]);
        }

        // Order Statuses
        if (DB::table('order_statuses')->count() === 0) {
            DB::table('order_statuses')->insert([
                ['name' => 'Active', 'visible' => true, 'active' => true],
                ['name' => 'Completed', 'visible' => false, 'active' => true],
                ['name' => 'Cancelled', 'visible' => false, 'active' => true],
            ]);
        }
    }
}
