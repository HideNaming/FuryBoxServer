<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Box;
use App\Models\Loot;
  

class Feed extends Model
{
    use HasFactory;

    protected $table = 'feed';
    /**
     * The attributes that are mass assignable.
     *	
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    protected $appends = ['time', 'box', 'loot'];

    public function getBoxAttribute()
    {
        $box = Box::find($this->box_id);
        return [
            "name" => $box->name,
            "slug" => $box->slug,
            "image" => $box->image
        ];
    }

    public function getTimeAttribute()
    {
        return \Carbon\Carbon::createFromTimeStamp(strtotime($this->created_at))->diffForHumans();
    }

    public function getLootAttribute()
    {
        $loot = Loot::find($this->loot_id);
        return [
            "name" => $loot->name,
            "image" => $loot->image,
            "price" => $loot->price
        ];
    }
}