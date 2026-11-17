<?php
namespace App\Models;
use CodeIgniter\Model;
class StudentAuditLogModel extends Model { protected $table='student_audit_logs'; protected $primaryKey='id'; protected $returnType='array'; protected $useTimestamps=false; protected $allowedFields=['admin_id','student_id','action','before_values','after_values','created_at']; }
