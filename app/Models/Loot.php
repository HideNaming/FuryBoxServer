<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

  

class Loot extends Model
{
    use HasFactory;

    protected $table = 'loot';
    /**
     * The attributes that are mass assignable.
     *	
     * @var array
     */
    protected $fillable = [
        'name', 'image', 'price', 'detail', 'stock', 'box_id'
    ];


    public function box()
    {
        return $this->hasMany('App\Loot', 'boxes_last_gift_id_foreign');
    }
}