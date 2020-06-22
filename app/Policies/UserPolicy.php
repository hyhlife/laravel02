<?php

namespace App\Policies;

use App\Models\User;
use Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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
        if(Auth::check() && Auth::user()->can('manage_users')){
            return true;
        } else {
            return false;
        }
    }

    public function view(): bool
    {
        if(Auth::check() && Auth::user()->can('manage_users')){
            return true;
        } else {
            return false;
        }
    }

    public function create(): bool
    {
        if(Auth::check() && Auth::user()->can('manage_users_add')){
            return true;
        } else {
            return false;
        }
    }


    public function update(User $currentUser, User $user)
    {
        if(Auth::check() && Auth::user()->can('manage_users_edit')){
            return true;
        } else {
            return $currentUser->id === $user->id;
        }
    }


    public function edit(): bool
    {
        if(Auth::check() && Auth::user()->can('manage_users_edit')){
            return true;
        } else {
            return false;
        }
    }


    public function delete(User $currentUser,User $user): bool
    {
        if($currentUser->can('manage_users_delete') && !$user->hasRole('超级管理员')){
            return true;
        } else {
            return false;
        }
    }

}
