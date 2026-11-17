<?php
namespace App\Services;
use App\Models\VerificationLogModel; use Throwable;
final class VerificationLogger { public function record(string $result,mixed $identifier):void{try{$key=(string)env('studentRecords.apiKey','');$hash=is_string($identifier)&&$identifier!==''&&$key!==''?hash_hmac('sha256',trim($identifier),$key):null;(new VerificationLogModel())->insert(['student_identifier_hash'=>$hash,'verification_result'=>$result,'response_category'=>$result,'requested_at'=>gmdate('Y-m-d H:i:s')]);}catch(Throwable $exception){log_message('error','Verification monitoring write failed ({exceptionClass}).',['exceptionClass'=>$exception::class]);}}}
