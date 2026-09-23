<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddComplimentarySubscriptionStatus extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE schools MODIFY subscription_status ENUM('trialing','active','past_due','suspended','canceled','complimentary') NULL DEFAULT 'trialing'");
    }

    public function down()
    {
        $this->db->query("UPDATE schools SET subscription_status = 'trialing' WHERE subscription_status = 'complimentary'");
        $this->db->query("ALTER TABLE schools MODIFY subscription_status ENUM('trialing','active','past_due','suspended','canceled') NULL DEFAULT 'trialing'");
    }
}
