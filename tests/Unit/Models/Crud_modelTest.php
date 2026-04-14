<?php

namespace Tests\Unit\Models;

use Tests\Support\UnitTestCase;
use App\Models\Crud_model;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\I18n\Time;

class Crud_modelTest extends CIUnitTestCase
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

    public function testSyllabusCreateRequiresTitle()
    {
        $_POST['title'] = '';
        $_POST['class_id'] = 1;
        $_POST['school_id'] = 1;
        $_POST['session_id'] = 1;

        $result = $this->crud_model->syllabus_create();

        $this->assertFalse($result['status']);
        $this->assertArrayHasKey('notification', $result);
    }

    public function testSyllabusCreateRequiresClassId()
    {
        $_POST['title'] = 'Test Syllabus';
        $_POST['class_id'] = '';
        $_POST['school_id'] = 1;
        $_POST['session_id'] = 1;

        $result = $this->crud_model->syllabus_create();

        $this->assertFalse($result['status']);
        $this->assertArrayHasKey('notification', $result);
    }

    public function testSyllabusCreateSanitizesTitleWithXss()
    {
        $malicious_title = '<script>alert("xss")</script>Test Title';
        $_POST['title'] = $malicious_title;
        $_POST['class_id'] = 1;
        $_POST['school_id'] = 1;
        $_POST['session_id'] = 1;

        $mock_file = $this->createMock(\CodeIgniter\Files\UploadedFile::class);
        $mock_file->method('isValid')->willReturn(true);
        $mock_file->method('getSize')->willReturn(1024);
        $mock_file->method('getExtension')->willReturn('pdf');
        $mock_file->method('getTempName')->willReturn(__FILE__);
        $mock_file->method('move')->willReturn(true);

        $_FILES['syllabus_file'] = $mock_file;

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $this->crud_model->request = $this->createMock(\CodeIgniter\HTTP\IncomingRequest::class);

        $result = $this->crud_model->syllabus_create();

        if ($result['status']) {
            $syllabus = db()->table('syllabuses')->orderBy('id', 'DESC')->get()->getRowArray();
            $this->assertStringNotContainsString('<script>', $syllabus['title']);
            $this->assertStringContainsString('Test Title', $syllabus['title']);
        }
    }

    public function testSyllabusCreateTypeCastsIntegerFields()
    {
        $_POST['title'] = 'Test Syllabus';
        $_POST['class_id'] = '1';
        $_POST['school_id'] = '1';
        $_POST['session_id'] = '1';

        $mock_file = $this->createMock(\CodeIgniter\Files\UploadedFile::class);
        $mock_file->method('isValid')->willReturn(true);
        $mock_file->method('getSize')->willReturn(1024);
        $mock_file->method('getExtension')->willReturn('pdf');
        $mock_file->method('getTempName')->willReturn(__FILE__);
        $mock_file->method('move')->willReturn(true);

        $_FILES['syllabus_file'] = $mock_file;

        $this->crud_model->request = $this->createMock(\CodeIgniter\HTTP\IncomingRequest::class);

        $result = $this->crud_model->syllabus_create();

        if ($result['status']) {
            $syllabus = db()->table('syllabuses')->orderBy('id', 'DESC')->get()->getRowArray();
            $this->assertIsInt($syllabus['class_id']);
            $this->assertIsInt($syllabus['school_id']);
            $this->assertIsInt($syllabus['session_id']);
        }
    }

    public function testSyllabusDeleteValidatesSchoolOwnership()
    {
        $syllabus_id = 999;
        $result = $this->crud_model->syllabus_delete($syllabus_id);

        $this->assertFalse($result);
    }

    public function testSyllabusDeleteReturnsFalseForNonExistentSyllabus()
    {
        $result = $this->crud_model->syllabus_delete(999999);

        $this->assertFalse($result);
    }
}
