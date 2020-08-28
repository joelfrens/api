<?php

use Illuminate\Database\Seeder;
use App\model\Language;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $language = new Language();
        $language->language_name = "English";
        $language->language_code = "en-gb";
        $language->save();

        $language = new Language();
        $language->language_name = "French";
        $language->language_code = "fr-ch";
        $language->save();
    }
}
