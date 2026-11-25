<?php

namespace App\Database\Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;
use RuntimeException;
use Throwable;

/** Fictional records for the local academic demonstration database. */
final class DummyStudentSeeder extends Seeder
{
    public function run()
    {
        $connection = $this->db->query('SELECT DATABASE() AS database_name, @@port AS server_port')->getRowArray();
        $host = strtolower((string) ($this->db->hostname ?? ''));
        if (ENVIRONMENT !== 'development' || ($connection['database_name'] ?? '') !== 'student_records_db'
            || (int) ($connection['server_port'] ?? 0) !== 33307 || !in_array($host, ['127.0.0.1', 'localhost', '::1'], true)) {
            throw new RuntimeException('Seeder requires the local development student_records_db on port 33307.');
        }

        $fixtures = [
            ['202610001', 'Adrian Reyes', 'BA', true], ['202610002', 'Sofia Mendoza', 'BA', true],
            ['202610003', 'Gabriel Santos', 'CS', true], ['202510001', 'Isabella Cruz', 'AGD', true],
            ['202510002', 'Nathan Villanueva', 'WMA', true], ['202510003', 'Chloe Garcia', 'CPE', false],
            ['202410001', 'Ethan Navarro', 'BA', true], ['202410002', 'Alyssa Ramos', 'AGD', true],
            ['202410003', 'Lucas Bautista', 'WMA', true], ['202410004', 'Bianca Flores', 'CS', false],
            ['202310001', 'Daniel Aquino', 'BA', true], ['202310002', 'Samantha Torres', 'AGD', true],
            ['202310003', 'Miguel Castillo', 'CPE', true], ['202310004', 'Andrea Lim', 'WMA', false],
            ['202310005', 'Julian Rivera', 'BA', true],
        ];
        $programs = [
            'BA' => ['Information Technology', 'BSITBA', 'Business Analytics', 'Information Technology'],
            'AGD' => ['Information Technology', 'BSITAGD', 'Animation and Game Development', 'Information Technology'],
            'WMA' => ['Information Technology', 'BSITWMA', 'Web and Mobile Applications', 'Information Technology'],
            'CS' => ['Computer Science', 'BSCS', null, 'Computer Science'],
            'CPE' => ['Computer Engineering', 'BSCPE', null, 'Computer Engineering'],
        ];

        $inserted = 0; $skipped = 0;
        $this->db->transException(true);
        $this->db->transBegin();
        try {
            foreach ($fixtures as [$studentId, $name, $programKey, $enrolled]) {
                $email = 'student-' . $studentId . '@example.invalid';
                $existing = $this->db->table('students')->groupStart()->where('student_id', $studentId)
                    ->orWhere('institutional_email', $email)->groupEnd()->countAllResults();
                if ($existing > 0) {
                    $skipped++;
                    continue;
                }
                [$program, $code, $specialization, $department] = $programs[$programKey];
                $now = gmdate('Y-m-d H:i:s');
                if (!$this->db->table('students')->insert([
                    'student_id' => $studentId, 'full_name' => $name, 'institutional_email' => $email,
                    'program' => $program, 'program_code' => $code, 'specialization' => $specialization,
                    'department' => $department, 'enrollment_status' => $enrolled ? 'enrolled' : 'not_enrolled',
                    'created_at' => $now, 'updated_at' => $now,
                ])) {
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
        CLI::write("Fictional students inserted: {$inserted}; skipped: {$skipped}.");
    }
}
