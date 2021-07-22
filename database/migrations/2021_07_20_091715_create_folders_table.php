<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('folders', function (Blueprint $table) {
            $table->string('id')->unique();
            $table->string('name');
            $table->string('type')->default('folder');
            $table->tinyInteger('is_public')->default(true);
            $table->bigInteger('owner_id');
            $table->integer('timestamp');
            $table->string('company_id')->nullable();
            $table->string('share')->nullable();
            $table->string('content')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('folders');
    }
}
