<?php
namespace App\Controllers;

use App\Models\AdminModel;
use Config\Database;
use DateTimeImmutable;
use DateTimeZone;

final class AdminDashboardController extends BaseController
{
    public function index(): string
    {
        $db = Database::connect();
        $requestCounts = array_fill_keys(['pending','under_review','resolved','rejected'], 0);
        foreach ($db->table('verification_assistance_requests')->select('status, COUNT(*) AS total')->groupBy('status')->get()->getResultArray() as $row) {
            if (array_key_exists($row['status'], $requestCounts)) $requestCounts[$row['status']] = (int) $row['total'];
        }
        $outcomes = [];
        foreach ($db->table('verification_logs')->select('verification_result, COUNT(*) AS total')->groupBy('verification_result')->get()->getResultArray() as $row) $outcomes[$row['verification_result']] = (int) $row['total'];
        $days = (int) $this->request->getGet('days');
        if (!in_array($days, [7,30,90], true)) $days = 30;
        $manila = new DateTimeZone('Asia/Manila'); $utc = new DateTimeZone('UTC');
        $today = (new DateTimeImmutable('now', $manila))->setTime(0,0);
        $first = $today->modify('-'.($days-1).' days'); $last = $today->modify('+1 day');
        $trend = [];
        for ($date = $first; $date < $last; $date = $date->modify('+1 day')) $trend[$date->format('Y-m-d')] = ['total'=>0, 'verified'=>0];
        $logs = $db->table('verification_logs')->select('requested_at, verification_result')
            ->where('requested_at >=', $first->setTimezone($utc)->format('Y-m-d H:i:s'))
            ->where('requested_at <', $last->setTimezone($utc)->format('Y-m-d H:i:s'))->get()->getResultArray();
        foreach ($logs as $log) {
            $key = (new DateTimeImmutable($log['requested_at'], $utc))->setTimezone($manila)->format('Y-m-d');
            if (isset($trend[$key])) { $trend[$key]['total']++; if ($log['verification_result'] === 'verified') $trend[$key]['verified']++; }
        }
        return view('admin/dashboard', [
            'title'=>'Dashboard', 'adminUser'=>(new AdminModel())->select('full_name')->find((int) session()->get('admin_id')),
            'total'=>$db->table('students')->countAllResults(), 'enrolled'=>$db->table('students')->where('enrollment_status','enrolled')->countAllResults(),
            'successful'=>$outcomes['verified'] ?? 0, 'unsuccessful'=>array_sum($outcomes)-($outcomes['verified'] ?? 0),
            'requests'=>array_sum($requestCounts), 'requestCounts'=>$requestCounts, 'outcomeCounts'=>$outcomes, 'trend'=>$trend, 'days'=>$days,
            'programDistribution'=>$db->table('students')->select('program, COUNT(*) AS total')->groupBy('program')->orderBy('total','DESC')->orderBy('program','ASC')->get()->getResultArray(),
            'recentVerifications'=>$db->table('verification_logs')->select('requested_at, verification_result')->orderBy('requested_at','DESC')->orderBy('id','DESC')->limit(5)->get()->getResultArray(),
            'recentRequests'=>$db->table('verification_assistance_requests')->select('id, full_name, status, created_at')->orderBy('created_at','DESC')->orderBy('id','DESC')->limit(5)->get()->getResultArray(),
        ]);
    }
}
