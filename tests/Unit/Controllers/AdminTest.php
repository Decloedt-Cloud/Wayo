<?php

namespace Tests\Unit\Controllers;

use Tests\Support\UnitTestCase;
use App\Controllers\Admin;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\Response;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;

class AdminTest extends CIUnitTestCase
{
    use ControllerTestTrait;

    protected $admin_controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin_controller = new Admin();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testSyllabusRouteRequiresAuthentication()
    {
        $_SESSION = [];

        $response = $this->controller(Admin::class)
            ->execute('syllabus');

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testSyllabusRouteRejectsUnauthorizedUserType()
    {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_type'] = 'student';

        $response = $this->controller(Admin::class)
            ->execute('syllabus');

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testSyllabusCreateReturnsJson()
    {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_type'] = 'admin';

        $_POST['title'] = 'Test Syllabus';
        $_POST['class_id'] = 1;
        $_POST['school_id'] = 1;
        $_POST['session_id'] = 1;

        $response = $this->controller(Admin::class)
            ->execute('syllabus', 'create');

        $this->assertIsArray(json_decode($response->getBody(), true));
    }

    public function testSyllabusListRendersView()
    {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_type'] = 'admin';

        $response = $this->controller(Admin::class)
            ->execute('syllabus', 'list', 'all', '1');

        $this->assertNotEmpty($response->getBody());
    }
}
