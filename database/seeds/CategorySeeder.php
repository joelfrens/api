<?php

use Illuminate\Database\Seeder;
use App\model\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category = new Category();
        $category->category_name = "Accessories";
        $category->category_description = "Accessories";
        $category->save();
    }
}
