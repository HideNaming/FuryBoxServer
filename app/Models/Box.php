<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Loot;



class Box extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *	
     * @var array
     */
    protected $fillable = [
        'name', 'slug', 'image', 'sale', 'price', 'views', 'opens', 'category_id'
    ];

    protected $appends = ['category', 'loot'];

    public function getCategoryAttribute()
    {
        return Category::find($this->category_id);
    }

    public function getLootAttribute()
    {
        return Loot::where('box_id', $this->id)->orderBy('price', 'desc')->get();
    }
}
