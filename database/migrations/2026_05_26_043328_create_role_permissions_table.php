<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolePermissionsTable extends Migration
{
    public function up()
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role'); // superadmin, admin, manager, kasir, kitchen, customer
            $table->string('permission'); // e.g. 'view_orders', 'manage_users', 'export_report'
            $table->string('module')->nullable(); // e.g. 'orders', 'users', 'reports'
            $table->boolean('is_allowed')->default(true);
            $table->timestamps();

            $table->unique(['role','permission']);
            $table->index('role');
        });

        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_laporan')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('judul');
            $table->string('tipe'); // sales, orders, wallet, membership, custom
            $table->string('periode_start')->nullable();
            $table->string('periode_end')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable(); // pdf, excel
            $table->text('catatan')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->string('admin_note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('laporans');
        Schema::dropIfExists('role_permissions');
    }
}