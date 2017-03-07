<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRoleToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = Config::get('popcode-usercrud.table', 'users');

        if (!Schema::hasColumn($tableName, 'role')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->integer('role', false, true)->default(1);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableName = Config::get('popcode-usercrud.table', 'users');

        if (Schema::hasColumn($tableName, 'role')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
}
