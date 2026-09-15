<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number');
            $table->foreignId('user_id')
                ->constrained('users');
            $table->foreignId('category_id')
                ->constrained('ticket_categories');
            $table->foreignId('asset_id')
                ->nullable()
                ->constrained('assets')
                ->nullOnDelete();
            $table->enum('type', [
                'incident',
                'service_request'
            ]);
            $table->string('title');
            $table->text('description');
            $table->enum('priority', [
                'low',
                'medium',
                'high',
                'critical'
            ]);
            $table->enum('status', [
                'open',
                'assigned',
                'in_progress',
                'waiting',
                'resolved',
                'closed'
            ])->default('open');
            $table->foreignId('sla_policy_id')
                ->nullable()
                ->constrained('sla_policies')
                ->nullOnDelete();
            $table->dateTime('response_deadline')->nullable();
            $table->dateTime('resolution_deadline')->nullable();
            $table->dateTime('first_responded_at')
                ->nullable();
            $table->dateTime('resolved_at')
                ->nullable();
            $table->dateTime('closed_at')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
