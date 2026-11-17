<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateVerificationLogsTable extends Migration { public function up(){ $this->forge->addField(['id'=>['type'=>'BIGINT','unsigned'=>true,'auto_increment'=>true],'student_identifier_hash'=>['type'=>'CHAR','constraint'=>64,'null'=>true],'verification_result'=>['type'=>'VARCHAR','constraint'=>30],'response_category'=>['type'=>'VARCHAR','constraint'=>40],'requested_at'=>['type'=>'DATETIME']]);$this->forge->addKey('id',true);$this->forge->addKey(['verification_result','requested_at']);$this->forge->addKey('requested_at');$this->forge->createTable('verification_logs'); } public function down(){ $this->forge->dropTable('verification_logs'); }}
