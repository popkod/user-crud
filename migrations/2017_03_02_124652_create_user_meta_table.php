<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = Config::get('popcode-usercrud.meta_table', 'user_metas');

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id', false, true)->nullable();
                $table->string('key')->default('');
                $table->text('value')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on(Config::get('popcode-usercrud.table', 'users'));
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
        Schema::dropIfExists(Config::get('popcode-usercrud.meta_table', 'user_metas'));
    }
}
