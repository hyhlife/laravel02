<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Auth;

class LinkPolicy
{
    use HandlesAuthorization;

    public function viewAny(): bool
    {
        if(Auth::check() && Auth::user()->can('manage_links')){
            return true;
        } else {
            return false;
        }
    }

    public function view(): bool
    {
        if(Auth::check() && Auth::user()->can('manage_links')){
            return true;
        } else {
            return false;
        }
    }

    public function create(): bool
    {
        if(Auth::check() && Auth::user()->can('manage_links_add')){
            return true;
        } else {
            return false;
        }
    }

    public function update()
    {
        if(Auth::check() && Auth::user()->can('manage_links_edit')){
            return true;
        } else {
            return false;
        }
    }


    public function edit(): bool
    {
        if(Auth::check() && Auth::user()->can('manage_contents')){
            return true;
        } else {
            return false;
        }
    }


    public function delete(): bool
    {
        if(Auth::check() && Auth::user()->can('manage_links_delete')){
            return true;
        } else {
            return false;
        }
    }
}
