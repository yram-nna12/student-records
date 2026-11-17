<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class StudentVerificationInputTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private bool $hadEnvApiKey;

    private mixed $previousEnvApiKey;

    private bool $hadServerApiKey;

    private mixed $previousServerApiKey;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hadEnvApiKey = array_key_exists('studentRecords.apiKey', $_ENV);
        $this->previousEnvApiKey = $_ENV['studentRecords.apiKey'] ?? null;
        $this->hadServerApiKey = array_key_exists('studentRecords.apiKey', $_SERVER);
        $this->previousServerApiKey = $_SERVER['studentRecords.apiKey'] ?? null;

        $_ENV['studentRecords.apiKey'] = 'feature-test-key';
        $_SERVER['studentRecords.apiKey'] = 'feature-test-key';
    }

    protected function tearDown(): void
    {
        if ($this->hadEnvApiKey) {
            $_ENV['studentRecords.apiKey'] = $this->previousEnvApiKey;
        } else {
            unset($_ENV['studentRecords.apiKey']);
        }

        if ($this->hadServerApiKey) {
            $_SERVER['studentRecords.apiKey'] = $this->previousServerApiKey;
        } else {
            unset($_SERVER['studentRecords.apiKey']);
        }

        parent::tearDown();
    }

    public function testRejectsArrayStudentId(): void
    {
        $response = $this->withHeaders([
            'X-API-Key' => 'feature-test-key',
        ])->get('/api/students/verify?student_id%5B%5D=ABC123');

        $this->assertSame(400, $response->response()->getStatusCode());
    }

    public function testRejectsInvalidStudentIdCharacters(): void
    {
        $query = http_build_query(['student_id' => 'ABC/123']);
        $response = $this->withHeaders([
            'X-API-Key' => 'feature-test-key',
        ])->get('/api/students/verify?' . $query);

        $this->assertSame(400, $response->response()->getStatusCode());
    }

    public function testRejectsStudentIdLongerThanRegistrationLimit(): void
    {
        $query = http_build_query(['student_id' => str_repeat('A', 31)]);
        $response = $this->withHeaders([
            'X-API-Key' => 'feature-test-key',
        ])->get('/api/students/verify?' . $query);

        $this->assertSame(400, $response->response()->getStatusCode());
    }
}
