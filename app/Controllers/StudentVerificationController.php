<?php

namespace App\Controllers;

use App\Models\StudentModel;
use CodeIgniter\RESTful\ResourceController;

class StudentVerificationController extends ResourceController
{
    protected $format = 'json';

    public function verify()
    {
        $studentId = trim(
            (string) $this->request->getGet('student_id')
        );

$expectedApiKey = (string) env('studentRecords.apiKey', '');
$providedApiKey = $this->request->getHeaderLine('X-API-Key');

if (
    $expectedApiKey === '' ||
    !hash_equals($expectedApiKey, $providedApiKey)
) {
    return $this->failUnauthorized(
        'Invalid or missing API key.'
    );
}


        if ($studentId === '') {
            return $this->failValidationErrors(
                'Student ID is required.'
            );
        }

        $studentModel = new StudentModel();

        $student = $studentModel
            ->where('student_id', $studentId)
            ->first();

        if (!$student) {
            return $this->respond([
                'verified' => false,
                'message' => 'Student record not found.',
            ]);
        }

        if ($student['enrollment_status'] !== 'enrolled') {
            return $this->respond([
                'verified' => false,
                'message' => 'Student is not currently enrolled.',
            ]);
        }

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
}
