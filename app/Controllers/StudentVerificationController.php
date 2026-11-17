<?php

namespace App\Controllers;

use App\Models\StudentModel;
use App\Services\VerificationLogger;
use CodeIgniter\RESTful\ResourceController;

class StudentVerificationController extends ResourceController
{
    protected $format = 'json';

    public function verify()
    {
        $expectedApiKey = (string) env('studentRecords.apiKey', '');
        $providedApiKey = $this->request->getHeaderLine('X-API-Key');

        if (
            $expectedApiKey === '' ||
            !hash_equals($expectedApiKey, $providedApiKey)
        ) {
            $this->log('unauthorized', $this->request->getGet('student_id'));
            return $this->failUnauthorized(
                'Invalid or missing API key.'
            );
        }

        $rawStudentId = $this->request->getGet('student_id');
        if ($rawStudentId === null || $rawStudentId === '') {
            $this->log('invalid_request', $rawStudentId);
            return $this->failValidationErrors(
                'Student ID is required.'
            );
        }

        if (! is_string($rawStudentId)) {
            $this->log('invalid_request', null);
            return $this->failValidationErrors(
                'Student ID is invalid.'
            );
        }

        $studentId = trim($rawStudentId);
        if ($studentId === '') {
            $this->log('invalid_request', $rawStudentId);
            return $this->failValidationErrors(
                'Student ID is required.'
            );
        }

        if (
            strlen($studentId) > 30 ||
            preg_match('/\A[A-Za-z0-9-]+\z/', $studentId) !== 1
        ) {
            $this->log('invalid_request', $rawStudentId);
            return $this->failValidationErrors(
                'Student ID is invalid.'
            );
        }

        $studentModel = new StudentModel();

        $student = $studentModel
            ->where('student_id', $studentId)
            ->first();

        if (!$student) {
            $this->log('student_not_found', $studentId);
            return $this->respond([
                'verified' => false,
                'message' => 'Student record not found.',
            ]);
        }

        if ($student['enrollment_status'] !== 'enrolled') {
            $this->log('not_enrolled', $studentId);
            return $this->respond([
                'verified' => false,
                'message' => 'Student is not currently enrolled.',
            ]);
        }

        $this->log('verified', $studentId);
        return $this->respond([
            'verified' => true,
            'message' => 'Student is currently enrolled.',
            'student' => [
                'student_id' => $student['student_id'],
                'full_name' => $student['full_name'],
                'institutional_email' =>
                    $student['institutional_email'],
                'program' => $student['program'],
                'program_code' => $student['program_code'],
                'specialization' => $student['specialization'],
                'department' => $student['department'],
            ],
        ]);
    }

    private function log(string $result, mixed $studentIdentifier): void
    {
        (new VerificationLogger())->record($result, $studentIdentifier);
    }
}
