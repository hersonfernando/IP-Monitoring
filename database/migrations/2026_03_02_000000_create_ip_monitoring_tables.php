<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('monitored_ips', function (Blueprint $table): void {
            $table->id();
            $table->string('label');
            $table->ipAddress('ip_address')->unique();
            $table->unsignedSmallInteger('port')->default(80);
            $table->boolean('is_active')->default(true);
            $table->string('last_status')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->unsignedInteger('last_response_ms')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('ip_check_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('monitored_ip_id')->constrained('monitored_ips')->cascadeOnDelete();
            $table->enum('status', ['online', 'offline']);
            $table->unsignedInteger('response_ms')->nullable();
            $table->string('message')->nullable();
            $table->timestamp('checked_at');
            $table->timestamps();
            $table->index(['monitored_ip_id', 'checked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ip_check_logs');
        Schema::dropIfExists('monitored_ips');
    }
};