<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table = 'students';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'student_id',
        'full_name',
        'institutional_email',
        'program',
        'program_code',
        'specialization',
        'department',
        'enrollment_status',
    ];

    protected $useTimestamps = true;
}
