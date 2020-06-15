<?php

namespace App\Models;

class Reply extends Model
{
    protected $fillable = ['topic_id', 'user_id', 'content'];

    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeRecent($query)
	{
	    return $query->orderBy('created_at', 'desc');
	}
}
