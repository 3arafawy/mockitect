<?php

declare(strict_types=1);

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
        Schema::create('mocks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('match_rules');
            $table->json('response_config');
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('scenario_id')->nullable()->constrained('scenarios')->nullOnDelete();
            $table->timestamps();

            $table->index('is_active');
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mocks');
    }
};
