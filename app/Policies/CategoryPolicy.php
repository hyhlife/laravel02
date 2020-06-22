<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;
use Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function viewAny(): bool
    {
        if(Auth::check() && Auth::user()->can('manage_categories')){
            return true;
        } else {
            return false;
        }
    }


    public function view(): bool
    {
        if(Auth::check() && Auth::user()->can('manage_categories')){
            return true;
        } else {
            return false;
        }
    }

    public function create(): bool
    {
        if(Auth::check() && Auth::user()->can('manage_categories_add')){
            return true;
        } else {
            return false;
        }
    }

    public function update()
    {
        if(Auth::check() && Auth::user()->can('manage_categories_edit')){
            return true;
        } else {
            return false;
        }
    }

    public function delete()
    {
        if(Auth::check() && Auth::user()->can('manage_categories_delete')){
            return true;
        } else {
            return false;
        }
    }
}
