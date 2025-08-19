<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pays', function (Blueprint $table) {
            $table->renameColumn('is_open', 'enable');
        });
    }

    public function down()
    {
        Schema::table('pays', function (Blueprint $table) {
            $table->renameColumn('enable', 'is_open');
        });
    }
};