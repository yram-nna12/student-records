<?php
namespace App\Models; use CodeIgniter\Model;
class VerificationAssistanceRequestModel extends Model { protected $table='verification_assistance_requests';protected $primaryKey='id';protected $returnType='array';protected $useTimestamps=true;protected $allowedFields=['student_id','full_name','contact_email','program','explanation','document_name','document_stored_name','document_mime_type','document_size','document_sha256','status','review_notes','reviewed_by','reviewed_at'];}
