<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use App\Models\User;

class TotalUsers extends Value{
	public $name = '总用户量';

	public function calculate(NovaRequest $request){
		return $this->result(User::count());
	}

	public function cacheFor()
    {
        
    }

    public function uriKey()
    {
        return 'total-users';
    }
}