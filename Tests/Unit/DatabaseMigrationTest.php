<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\WorkFrame\Service\DatabaseMigration;

class DatabaseMigrationTest extends TestCase
{
    public function testMigrationClassExists(): void
    {
        $this->assertTrue(class_exists(DatabaseMigration::class));
    }

    public function testMigrationHasRunMethod(): void
    {
        $this->assertTrue(method_exists(DatabaseMigration::class, 'run'));
    }

    public function testMigrationHasSeedMethod(): void
    {
        $this->assertTrue(method_exists(DatabaseMigration::class, 'seed'));
    }
}
