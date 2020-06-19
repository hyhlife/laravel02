<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use App\Models\Topic;

class TotalTopics extends Value{
	public $name = '总话题量';

	public function calculate(NovaRequest $request){
		return $this->result(Topic::count());
	}

	public function cacheFor()
    {
       	
    }

    public function uriKey()
    {
        return 'total-topics';
    }
}