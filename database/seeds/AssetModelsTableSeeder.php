<?php

use Illuminate\Database\Seeder;

class AssetModelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      \App\AssetModel::factory()->count(4)->create();
    }
}
