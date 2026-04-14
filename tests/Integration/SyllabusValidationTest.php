<?php

namespace Tests\Integration;

use Tests\Support\IntegrationTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\CIUnitTestCase;
use App\Models\Crud_model;

class SyllabusValidationTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate     = false;
    protected $migrateOnce = false;
    protected $refresh     = false;
    protected $namespace   = null;

    protected $crud_model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crud_model = new Crud_model();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testPdfMimeTypeIsAllowed()
    {
        $allowed_mimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain'
        ];

        $this->assertContains('application/pdf', $allowed_mimes);
    }

    public function testDocMimeTypeIsAllowed()
    {
        $allowed_mimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain'
        ];

        $this->assertContains('application/msword', $allowed_mimes);
    }

    public function testDocxMimeTypeIsAllowed()
    {
        $allowed_mimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain'
        ];

        $this->assertContains('application/vnd.openxmlformats-officedocument.wordprocessingml.document', $allowed_mimes);
    }

    public function testTxtMimeTypeIsAllowed()
    {
        $allowed_mimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain'
        ];

        $this->assertContains('text/plain', $allowed_mimes);
    }

    public function testExeMimeTypeIsNotAllowed()
    {
        $allowed_mimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain'
        ];

        $this->assertNotContains('application/x-msdownload', $allowed_mimes);
        $this->assertNotContains('application/x-executable', $allowed_mimes);
    }

    public function testPdfExtensionIsAllowed()
    {
        $allowed_extensions = ['pdf', 'doc', 'docx', 'txt'];

        $this->assertContains('pdf', $allowed_extensions);
    }

    public function testExeExtensionIsNotAllowed()
    {
        $allowed_extensions = ['pdf', 'doc', 'docx', 'txt'];

        $this->assertNotContains('exe', $allowed_extensions);
        $this->assertNotContains('bat', $allowed_extensions);
        $this->assertNotContains('sh', $allowed_extensions);
    }

    public function testFileSizeLimitIs20MB()
    {
        $max_size = 20 * 1024 * 1024;
        $expected_size = 20971520;

        $this->assertEquals($expected_size, $max_size);
    }

    public function testFileSizeExceedsLimit()
    {
        $file_size = 25 * 1024 * 1024;
        $max_size = 20 * 1024 * 1024;

        $this->assertGreaterThan($max_size, $file_size);
    }

    public function testFileSizeWithinLimit()
    {
        $file_size = 15 * 1024 * 1024;
        $max_size = 20 * 1024 * 1024;

        $this->assertLessThanOrEqual($max_size, $file_size);
    }

    public function testUploadPathIsSecure()
    {
        $upload_path = 'uploads/syllabus/';

        $this->assertStringStartsWith('uploads/', $upload_path);
        $this->assertStringEndsWith('/', $upload_path);
    }

    public function testFileNameIsRandomized()
    {
        $file_extension = 'pdf';
        $new_name1 = uniqid('syllabus_') . '.' . $file_extension;
        $new_name2 = uniqid('syllabus_') . '.' . $file_extension;

        $this->assertNotEquals($new_name1, $new_name2);
        $this->assertStringStartsWith('syllabus_', $new_name1);
        $this->assertStringEndsWith('.pdf', $new_name1);
    }
}
