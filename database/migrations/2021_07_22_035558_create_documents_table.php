<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->string('id')->unique();
            $table->string('name');
            $table->string('type')->default('document');
            $table->string('folder_id');
            $table->string('content')->nullable();
            $table->integer('timestamp');
            $table->bigInteger('owner_id');
            $table->string('share')->nullable();
            $table->string('company_id')->nullable();
            $table->foreign('folder_id')
                    ->references('id')->on('folders')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
    }
}
