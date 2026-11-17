<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateAdminsTable extends Migration { public function up() { $this->forge->addField(['id'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],'email'=>['type'=>'VARCHAR','constraint'=>150,'unique'=>true],'full_name'=>['type'=>'VARCHAR','constraint'=>150],'password_hash'=>['type'=>'VARCHAR','constraint'=>255],'status'=>['type'=>'ENUM','constraint'=>['active','inactive'],'default'=>'active'],'last_login_at'=>['type'=>'DATETIME','null'=>true],'created_at'=>['type'=>'DATETIME','null'=>true],'updated_at'=>['type'=>'DATETIME','null'=>true]]);$this->forge->addKey('id',true);$this->forge->createTable('admins'); } public function down(){ $this->forge->dropTable('admins'); }}
