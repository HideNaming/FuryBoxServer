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
        return Box::find($this->loot->box_id);
    }
    public function getBenefitAttribute()
    {
        return $this->loot->price / $this->price;
    }
}
