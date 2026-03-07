<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void
    {
        // ─── Lookup tables ───────────────────────────────────────────────

        Capsule::schema()->create('sections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 60);
            $table->boolean('active')->default(true);
            $table->index('name');
        });

        Capsule::schema()->create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 60);
            $table->boolean('active')->default(true);
            $table->index('name');
        });

        Capsule::schema()->create('work_centers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 60);
            $table->boolean('active')->default(true);
            $table->index('name');
        });

        Capsule::schema()->create('order_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 25);
            $table->boolean('visible')->default(false);
            $table->boolean('active')->default(true);
            $table->index('name');
        });

        // ─── Resources ──────────────────────────────────────────────────

        Capsule::schema()->create('workers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 60);
            $table->unsignedInteger('work_center_id')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->string('email', 50)->nullable();
            $table->date('available_from')->nullable();
            $table->date('available_until')->nullable();
            $table->boolean('active')->default(true);

            $table->foreign('work_center_id')->references('id')->on('work_centers')->nullOnDelete();
            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
            $table->index('work_center_id');
            $table->index('category_id');
        });

        Capsule::schema()->create('vehicles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 60);
            $table->unsignedInteger('work_center_id')->nullable();
            $table->string('license_plate', 15)->nullable();
            $table->boolean('active')->default(true);

            $table->foreign('work_center_id')->references('id')->on('work_centers')->nullOnDelete();
            $table->index('work_center_id');
            $table->index('license_plate');
        });

        // ─── Customers ──────────────────────────────────────────────────

        Capsule::schema()->create('customers', function (Blueprint $table) {
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

            $table->index('name');
        });

        Capsule::schema()->create('customer_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->index('customer_id');
        });

        // ─── Project Files (Expedientes) ────────────────────────────────

        Capsule::schema()->create('project_files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 60);
            $table->unsignedInteger('customer_id');
            $table->date('date')->nullable();
            $table->string('locality', 50)->nullable();
            $table->string('town', 50)->nullable();
            $table->boolean('active')->default(true);

            $table->foreign('customer_id')->references('id')->on('customers')->restrictOnDelete();
            $table->index('customer_id');
        });

        Capsule::schema()->create('project_file_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_file_id');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('project_file_id')->references('id')->on('project_files')->cascadeOnDelete();
            $table->index('project_file_id');
        });

        // ─── Work Orders ────────────────────────────────────────────────

        Capsule::schema()->create('work_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 60);
            $table->unsignedInteger('project_file_id')->nullable();
            $table->date('date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable()->default('09:00:00');
            $table->unsignedInteger('foreman_id')->nullable();
            $table->string('address', 50)->nullable();
            $table->string('zip', 10)->nullable();
            $table->string('locality', 50)->nullable();
            $table->string('town', 50)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->boolean('active')->default(true);

            $table->foreign('project_file_id')->references('id')->on('project_files')->nullOnDelete();
            $table->foreign('foreman_id')->references('id')->on('workers')->nullOnDelete();
            $table->index('project_file_id');
            $table->index('foreman_id');
            $table->index('status');
            $table->index(['date', 'end_date']);
        });

        Capsule::schema()->create('work_order_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('work_order_id');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('work_order_id')->references('id')->on('work_orders')->cascadeOnDelete();
            $table->index('work_order_id');
        });

        // ─── Pivots: Work Order ↔ Workers / Vehicles ────────────────────

        Capsule::schema()->create('work_order_workers', function (Blueprint $table) {
            $table->unsignedInteger('work_order_id');
            $table->unsignedInteger('worker_id');
            $table->primary(['work_order_id', 'worker_id']);

            $table->foreign('work_order_id')->references('id')->on('work_orders')->cascadeOnDelete();
            $table->foreign('worker_id')->references('id')->on('workers')->cascadeOnDelete();
        });

        Capsule::schema()->create('work_order_vehicles', function (Blueprint $table) {
            $table->unsignedInteger('work_order_id');
            $table->unsignedInteger('vehicle_id');
            $table->primary(['work_order_id', 'vehicle_id']);

            $table->foreign('work_order_id')->references('id')->on('work_orders')->cascadeOnDelete();
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->cascadeOnDelete();
        });

        // ─── Work Parts ─────────────────────────────────────────────────

        Capsule::schema()->create('work_parts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 60);
            $table->unsignedInteger('work_order_id');
            $table->unsignedInteger('foreman_id')->nullable();
            $table->boolean('special_time')->default(false);
            $table->char('has_image', 3)->nullable();
            $table->char('has_invoice', 3)->nullable();
            $table->text('notes')->nullable();
            $table->date('date')->nullable();

            $table->foreign('work_order_id')->references('id')->on('work_orders')->restrictOnDelete();
            $table->foreign('foreman_id')->references('id')->on('workers')->nullOnDelete();
            $table->index('work_order_id');
            $table->index('foreman_id');
        });

        // ─── Part detail pivots ─────────────────────────────────────────

        Capsule::schema()->create('part_vehicles', function (Blueprint $table) {
            $table->unsignedInteger('work_part_id');
            $table->unsignedInteger('vehicle_id');
            $table->primary(['work_part_id', 'vehicle_id']);

            $table->foreign('work_part_id')->references('id')->on('work_parts')->cascadeOnDelete();
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->cascadeOnDelete();
        });

        Capsule::schema()->create('part_workers', function (Blueprint $table) {
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

            $table->foreign('work_part_id')->references('id')->on('work_parts')->cascadeOnDelete();
            $table->foreign('worker_id')->references('id')->on('workers')->cascadeOnDelete();
        });

        // ─── Mail configuration ─────────────────────────────────────────

        Capsule::schema()->create('mail_configs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('host', 100)->nullable();
            $table->integer('port')->default(993);
            $table->string('username', 100)->nullable();
            $table->string('password', 100)->nullable();
            $table->boolean('ssl')->default(true);
        });
    }

    public function down(): void
    {
        $tables = [
            'part_workers',
            'part_vehicles',
            'work_parts',
            'work_order_vehicles',
            'work_order_workers',
            'work_order_notes',
            'work_orders',
            'project_file_notes',
            'project_files',
            'customer_notes',
            'customers',
            'vehicles',
            'workers',
            'order_statuses',
            'work_centers',
            'categories',
            'sections',
            'mail_configs',
        ];
        foreach ($tables as $table) {
            Capsule::schema()->dropIfExists($table);
        }
    }
};
