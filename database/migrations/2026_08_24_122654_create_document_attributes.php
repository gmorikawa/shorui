<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->string('key', 50)->primary();
            $table->string('label')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('document_type_attributes', function (Blueprint $table) {
            $table->uuid('type_id');
            $table->string('attribute_key', 50);

            $table->primary(['type_id', 'attribute_key']);
            $table->foreign('type_id')->references('id')->on('document_types')->onDelete('cascade');
            $table->foreign('attribute_key')->references('key')->on('attributes')->onDelete('cascade');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->json('attributes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('attributes');
        });
        Schema::dropIfExists('document_type_attributes');
        Schema::dropIfExists('attributes');
    }
};
