<?php

namespace App\Policies;

use App\Models\User;
use Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    // public function viewAny(): bool
    // {
    //     if(Auth::check() && Auth::user()->can('manage_users')){
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

    public function view(User $currentUser, User $user): bool
    {
        if(Auth::check() && Auth::user()->can('manage_users')){
            return true;
        } else {
           return $currentUser->id === $user->id;
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
            if($user->hasRole('超级管理员')){
                if($currentUser->hasRole('超级管理员')){
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        } else {
            return $currentUser->id === $user->id;
        }
    }


    public function edit(User $currentUser, User $user): bool
    {
        if(Auth::check() && Auth::user()->can('manage_users_edit')){
            if($user->hasRole('超级管理员')){
                if($currentUser->hasRole('超级管理员')){
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        } else {
            return $currentUser->id === $user->id;
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
