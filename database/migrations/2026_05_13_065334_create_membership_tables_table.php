<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembershipTablesTable extends Migration
{
    public function up()
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('tier', ['none', 'silver', 'gold', 'platinum'])->default('none');
            $table->integer('completed_orders')->default(0);
            $table->decimal('cashback_rate', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('tier_achieved_at')->nullable();
            $table->timestamp('platinum_expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('total_points')->default(0);
            $table->integer('used_points')->default(0);
            $table->integer('available_points')->default(0);
            $table->timestamps();
        });

        Schema::create('loyalty_point_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('points');
            $table->enum('type', ['earned', 'used', 'expired'])->default('earned');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('cashback_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 15, 2);
            $table->decimal('cashback_rate', 5, 2);
            $table->string('membership_tier');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cashback_logs');
        Schema::dropIfExists('loyalty_point_logs');
        Schema::dropIfExists('loyalty_points');
        Schema::dropIfExists('memberships');
    }
}