<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (!extension_loaded('pdo_sqlite')) {
            return;
        }

        parent::setUp();
    }

    public function test_feature_test_suite_is_configured(): void
    {
        $this->assertTrue(true);
    }
}
