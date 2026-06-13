<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('polls_polls')) {
            Schema::create('polls_polls', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('status')->default('open');
                $table->boolean('multiple_choice')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('polls_poll_options')) {
            Schema::create('polls_poll_options', function (Blueprint $table) {
                $table->id();
                $table->foreignId('poll_id')->constrained('polls_polls')->cascadeOnDelete();
                $table->string('label');
                $table->unsignedInteger('votes_count')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('polls_poll_votes')) {
            Schema::create('polls_poll_votes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('poll_id')->constrained('polls_polls')->cascadeOnDelete();
                $table->foreignId('poll_option_id')->constrained('polls_poll_options')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['poll_option_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('polls_poll_votes');
        Schema::dropIfExists('polls_poll_options');
        Schema::dropIfExists('polls_polls');
    }
};
