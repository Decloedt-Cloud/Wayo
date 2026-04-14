<?php

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase as BaseCIUnitTestCase;

abstract class IntegrationTestCase extends BaseCIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
