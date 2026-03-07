<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Seeders;

use Alxarafe\Base\Model\Model;
use Alxarafe\Base\Seeder;
use Illuminate\Database\Capsule\Manager as Capsule;
use Modules\WorkFrame\Model\Section;
use Modules\WorkFrame\Model\Category;
use Modules\WorkFrame\Model\WorkCenter;
use Modules\WorkFrame\Model\OrderStatus;

class WorkFrameSeeder extends Seeder
{
    /**
     * Logic to execute the seeding process.
     *
     * @param class-string<Model> $modelClass
     */
    #[\Override]
    protected function run(string $modelClass): void
    {
        // Sections
        if (Capsule::table('sections')->count() === 0) {
            Capsule::table('sections')->insert([
                ['name' => 'Construction Materials', 'active' => true],
                ['name' => 'Plumbing', 'active' => true],
                ['name' => 'Miscellaneous Materials', 'active' => true],
                ['name' => 'Tools', 'active' => true],
            ]);
        }

        // Categories
        if (Capsule::table('categories')->count() === 0) {
            Capsule::table('categories')->insert([
                ['name' => 'Plumber', 'active' => true],
                ['name' => 'Carpenter', 'active' => true],
            ]);
        }

        // Work Centers
        if (Capsule::table('work_centers')->count() === 0) {
            Capsule::table('work_centers')->insert([
                ['name' => 'Madrid', 'active' => true],
                ['name' => 'Sevilla', 'active' => true],
            ]);
        }

        // Order Statuses
        if (Capsule::table('order_statuses')->count() === 0) {
            Capsule::table('order_statuses')->insert([
                ['name' => 'Active', 'visible' => true, 'active' => true],
                ['name' => 'Completed', 'visible' => false, 'active' => true],
                ['name' => 'Cancelled', 'visible' => false, 'active' => true],
            ]);
        }
    }

    /**
     * Returns the Model class name associated with this seeder.
     * Note: This seeder handles multiple models, so we return Section as representative.
     *
     * @return class-string<Model>
     */
    #[\Override]
    protected static function model(): string
    {
        return Section::class;
    }
}
