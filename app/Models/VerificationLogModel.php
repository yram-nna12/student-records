<?php
namespace App\Models;
use CodeIgniter\Model;
class VerificationLogModel extends Model { protected $table='verification_logs'; protected $primaryKey='id'; protected $returnType='array'; protected $useTimestamps=false; protected $allowedFields=['student_identifier_hash','verification_result','response_category','requested_at']; }
