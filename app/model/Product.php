<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;
use App\model\Category;
use App\model\ProductTranslations;

class Product extends Model
{   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['product_price', 'category_id'];
    
    /**
     * Product has one category
     */
    public function category()
    {
        return $this->belongsTo('App\model\Category');
    }

    /**
     * Product Translations
     */
    public function translations()
    {
        return $this->hasOne('App\model\ProductTranslations');
    }
}
