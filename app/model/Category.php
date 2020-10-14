<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;
use App\model\Category;

class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['category_name', 'category_description'];
}
