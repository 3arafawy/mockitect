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
        Schema::create('request_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mock_id')->nullable()->constrained('mocks')->nullOnDelete();
            $table->string('method');
            $table->string('path');
            $table->json('headers')->nullable();
            $table->json('query_params')->nullable();
            $table->text('body')->nullable();
            $table->integer('response_status');
            $table->json('response_headers')->nullable();
            $table->text('response_body')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->boolean('was_matched')->default(false);
            $table->timestamps();

            $table->index('created_at');
            $table->index(['method', 'path']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_logs');
    }
};
