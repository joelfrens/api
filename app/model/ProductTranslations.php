<?php

namespace App\model;
use App\model\Product;

use Illuminate\Database\Eloquent\Model;

class ProductTranslations extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['product_name', 'product_description', 'product_id', 'language_id'];

}
