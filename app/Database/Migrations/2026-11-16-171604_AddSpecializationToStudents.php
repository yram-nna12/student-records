<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSpecializationToStudents extends Migration
{
    public function up()
    {
        $this->forge->addColumn('students', [
            'specialization' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'program',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(
            'students',
            'specialization'
        );
    }
}
