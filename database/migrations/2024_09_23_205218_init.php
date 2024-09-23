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
        Schema::create('search', function (Blueprint $table) {
            $table->id();
            $table->string('query');
            $table->integer('total_count');
            $table->timestamps();
        });
        Schema::create('github_project', function (Blueprint $table) {
            $table->id();
            $table->integer('search_id');
            $table->string('name');
            $table->string('author');
            $table->string('project_id');
            $table->integer('stargazers');
            $table->integer('watchers');
            $table->string('html_url');
            $table->timestamps();
            $table->foreign('search_id')->references('id')->on('search');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('github_project');
        Schema::dropIfExists('search');
    }
};
