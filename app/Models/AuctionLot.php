<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Loot;
use App\Models\Box;



class AuctionLot extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *	
     * @var array
     */
    protected $fillable = [
        'id', 'loot_id', 'price', 'stap', 'start', 'rate', 'updated', 'finished', 'finish'
    ];

    protected $appends = ['loot', 'benefit', 'box'];

    public function getLootAttribute()
    {
        return Loot::find($this->loot_id);
    }
    public function getBoxAttribute()
    {
        $box = Box::where('id', $this->loot->box_id)->select('name', 'price', 'slug', 'image')->first();
        return $box;
    }
    public function getBenefitAttribute()
    {
        return $this->loot->price / $this->price;
    }
}
