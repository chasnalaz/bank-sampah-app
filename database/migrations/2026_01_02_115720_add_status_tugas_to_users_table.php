<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Kita beri default 'offline' atau 'izin'
            $table->enum('status_tugas', ['siap', 'izin'])->default('izin')->after('role');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status_tugas');
        });
    }
};
