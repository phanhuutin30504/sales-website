<?php

namespace App\Models;

use App\Support\Eloquent\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory,Sluggable;
    protected $table = 'notifications';
    protected $guarded = [];

    public function users()
{
    return $this->belongsToMany(User::class);
}

public function getShortDescriptionAttribute()
{
    return substr($this->desc, 0, 100);
}

}
