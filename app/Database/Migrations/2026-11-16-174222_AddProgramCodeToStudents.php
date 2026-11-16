<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProgramCodeToStudents extends Migration
{
    public function up()
    {
        $this->forge->addColumn('students', [
            'program_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'program',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(
            'students',
            'program_code'
        );
    }
}
