<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddAdminManagerRoles extends Migration
{
    public function up()
    {
        // PostgreSQL: ubah kolom role menjadi string biasa (sudah varchar)
        // Cukup update constraint / enum jika pakai check constraint
        // Untuk PostgreSQL tidak ada native ENUM alter, kita pakai check constraint

        DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('superadmin','admin','manager','kasir','kitchen','customer'))");
    }

    public function down()
    {
        DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('superadmin','kasir','kitchen','customer'))");
    }
}