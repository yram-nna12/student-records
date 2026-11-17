<?php
namespace App\Controllers;
class AdminDashboardController extends BaseController { public function index():string{$db=\Config\Database::connect();return view('admin/dashboard',['title'=>'Dashboard','total'=>$db->table('students')->countAllResults(),'enrolled'=>$db->table('students')->where('enrollment_status','enrolled')->countAllResults(),'notEnrolled'=>$db->table('students')->where('enrollment_status','not_enrolled')->countAllResults()]);}}
