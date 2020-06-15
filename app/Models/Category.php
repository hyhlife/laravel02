<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $fillable = [
        'name', 'description','order'
    ];

    public function topic()
    {
        return $this->hasMany(Topic::class)->orderBy('created_at','desc');
    }
}
