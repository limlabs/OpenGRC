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
        Schema::create('controls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('standard_id'); // Foreign key to the Standard model
            $table->string('title', 1024);
            $table->string('code');
            $table->longText('description');
            $table->longText('discussion')->nullable();
            $table->string('type')->default(\App\Enums\ControlType::OTHER);
            $table->string('category')->default(\App\Enums\ControlCategory::OTHER);
            $table->string('enforcement')->default(\App\Enums\ControlEnforcementCategory::UNKNOWN);
            $table->string('effectiveness')->default(\App\Enums\Effectiveness::UNKNOWN);
            $table->string('applicability')->default(\App\Enums\Applicability::UNKNOWN);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('standard_id')->references('id')->on('standards')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('controls');
    }
};
