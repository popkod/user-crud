<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PopcodeUserCrudCallFirstUserSeeder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!class_exists('PopcodeUserCrudFirstUserSeeder')) {
            return;
        }

        $firstUserSeeder = new PopcodeUserCrudFirstUserSeeder();
        $firstUserSeeder->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
