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
            $table->string('id', );
            $table->string('name', );
            $table->string('type', );
            $table->string('folder_id', );
            $table->string('content', );
            $table->integer('timestamp');
            $table->string('owner_id', );
            $table->string('share', );
            $table->string('company_id', );
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
