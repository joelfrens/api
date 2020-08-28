<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);
        $this->call(LanguageSeeder::class);
        $this->call(CategorySeeder::class);
        Factory(\App\model\Product::class, 20)->create();
        Factory(\App\model\ProductTranslations::class, 20)->create();
    }
}
