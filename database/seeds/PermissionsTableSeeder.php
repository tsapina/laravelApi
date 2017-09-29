<?php

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('permissions')->insert(['name' => 'Update']);
        DB::table('permissions')->insert(['name' => 'Create']);
        DB::table('permissions')->insert(['name' => 'Delete']);
        DB::table('permissions')->insert(['name' => 'Read']);

    }
}
