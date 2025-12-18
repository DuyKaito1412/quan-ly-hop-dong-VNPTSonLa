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
        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->string('type'); // EXPIRY, PAYMENT, DELIVERY, etc.
            $table->string('title');
            $table->date('due_date');
            $table->boolean('done')->default(false);
            $table->date('done_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['contract_id', 'type']);
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milestones');
    }
};
