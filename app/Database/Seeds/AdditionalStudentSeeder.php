<?php

namespace App\Database\Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;
use RuntimeException;
use Throwable;

/** Additional fictional students for the local academic demonstration. */
final class AdditionalStudentSeeder extends Seeder
{
    public function run()
    {
        $target = $this->db->query('SELECT DATABASE() AS database_name, @@port AS server_port')->getRowArray();
        $host = strtolower((string) ($this->db->hostname ?? ''));
        if (ENVIRONMENT !== 'development' || ($target['database_name'] ?? '') !== 'student_records_db'
            || (int) ($target['server_port'] ?? 0) !== 33307 || !in_array($host, ['127.0.0.1', 'localhost', '::1'], true)) {
            throw new RuntimeException('Seeder requires the local development student_records_db on port 33307.');
        }
        $fixtures = [
            ['202610004','Marcus Delgado','CYBER',true], ['202610005','Hannah Velasco','SE',true],
            ['202610006','Elijah Dizon','CE',true], ['202610007','Nicole Salazar','ME',true],
            ['202610008','Liam Fernandez','EE',true], ['202510004','Jasmine Morales','BA',true],
            ['202510005','Aaron Pascual','DS',true], ['202510006','Camille Santiago','ECE',true],
            ['202510007','Joshua Mercado','CPE',false], ['202510008','Gabrielle Tan','CE',true],
            ['202410005','Matthew Soriano','AGD',true], ['202410006','Angela Villareal','WMA',true],
            ['202410007','Sebastian Ong','ME',true], ['202410008','Patricia Domingo','EE',false],
            ['202410009','Vincent Chua','ECE',true], ['202310006','Danielle Lopez','SE',true],
            ['202310007','Christian Go','CE',true], ['202310008','Samantha Co','CPE',true],
            ['202310009','Rafael Manalo','ME',false], ['202310010','Mikaela David','CYBER',true],
        ];
        $programs = [
            'CYBER'=>['Information Technology','BSITCYBER','Cybersecurity','Information Technology'],
            'BA'=>['Information Technology','BSITBA','Business Analytics','Information Technology'],
            'AGD'=>['Information Technology','BSITAGD','Animation and Game Development','Information Technology'],
            'WMA'=>['Information Technology','BSITWMA','Web and Mobile Applications','Information Technology'],
            'SE'=>['Computer Science','BSCSSE','Software Engineering','Computer Science'],
            'DS'=>['Computer Science','BSCSDS','Data Science','Computer Science'],
            'CPE'=>['Computer Engineering','BSCPE',null,'Computer Engineering'],
            'CE'=>['Civil Engineering','BSCE',null,'Civil Engineering'],
            'ME'=>['Mechanical Engineering','BSME',null,'Mechanical Engineering'],
            'EE'=>['Electrical Engineering','BSEE',null,'Electrical Engineering'],
            'ECE'=>['Electronics Engineering','BSECE',null,'Electronics Engineering'],
        ];
        $inserted=0; $skipped=0;
        $this->db->transException(true);
        $this->db->transBegin();
        try {
            foreach ($fixtures as [$studentId,$name,$programKey,$enrolled]) {
                $email='student-'.$studentId.'@example.invalid';
                $exists=$this->db->table('students')->groupStart()->where('student_id',$studentId)->orWhere('institutional_email',$email)->groupEnd()->countAllResults();
                if ($exists > 0) { $skipped++; continue; }
                [$program,$code,$specialization,$department]=$programs[$programKey];
                $now=gmdate('Y-m-d H:i:s');
                if (!$this->db->table('students')->insert(['student_id'=>$studentId,'full_name'=>$name,'institutional_email'=>$email,
                    'program'=>$program,'program_code'=>$code,'specialization'=>$specialization,'department'=>$department,
                    'enrollment_status'=>$enrolled?'enrolled':'not_enrolled','created_at'=>$now,'updated_at'=>$now])) {
                    throw new RuntimeException('A fictional student record could not be inserted.');
                }
                $inserted++;
            }
            if (!$this->db->transStatus() || !$this->db->transCommit()) throw new RuntimeException('The seeder transaction could not be committed.');
        } catch (Throwable $exception) {
            $this->db->transRollback();
            throw $exception;
        } finally {
            $this->db->transException(false);
        }
        CLI::write("Additional fictional students inserted: {$inserted}; skipped: {$skipped}.");
    }
}
