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
        Schema::create('evaluation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained()->onDelete('cascade');
            $table->foreignId('criterion_id')->constrained()->onDelete('cascade');
            $table->foreignId('criterion_level_id')->nullable()->constrained('criterion_levels')->nullOnDelete();
            $table->decimal('score', 8, 2)->nullable(); // copia del value en el momento de guardar (redundante pero útil para auditoría)
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['evaluation_id', 'criterion_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_items');
    }
};
