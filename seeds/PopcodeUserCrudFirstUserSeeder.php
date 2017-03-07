<?php

use Illuminate\Database\Seeder;

class PopcodeUserCrudFirstUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tableName = Config::get('popcode-usercrud.table', 'users');
        $defaultAdmin = Config::get('popcode-usercrud.default_admin', false);

        if (!$defaultAdmin) {
            return;
        }

        $defaultAdmin['password'] = Hash::make($defaultAdmin['password']);
        $defaultAdmin['created_at'] = Carbon\Carbon::now();

        $userId = DB::table($tableName)->insertGetId($defaultAdmin);


        $userMeta = Config::get('popcode-usercrud.user_meta', false);
        $metaTableName = Config::get('popcode-usercrud.meta_table', 'user_metas');
        $defaultAdminMetas = Config::get('popcode-usercrud.default_admin_metas', false);

        if (!$userMeta || !$defaultAdminMetas) {
            return;
        }

        array_walk($defaultAdminMetas, function(&$meta) use ($userId) {
            $meta['user_id'] = $userId;
            $meta['created_at'] = Carbon\Carbon::now();
        });

        DB::table($metaTableName)->insert($defaultAdminMetas);
    }
}
