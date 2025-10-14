<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('quiz_responses', function (Blueprint $table) {
            $table->enum('education_level', ['D4', 'S1', 'Pascasarjana'])
                  ->nullable()
                  ->after('department_id')
                  ->comment('Education level: D4, S1, or Pascasarjana (S2/S3)');
        });
    }

    public function down()
    {
        Schema::table('quiz_responses', function (Blueprint $table) {
            $table->dropColumn('education_level');
        });
    }
};