<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organization_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('intake_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('channel');
            $table->string('outcome')->nullable();
            $table->text('note')->nullable();
            $table->timestampTz('attempted_at');
            $table->timestampTz('next_follow_up_at')->nullable();

            $table->timestamps();

            $table->index(['organization_id', 'intake_id']);
            $table->index(['organization_id', 'attempted_at']);
            $table->index(['organization_id', 'next_follow_up_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
    }
};
