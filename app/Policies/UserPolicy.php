<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Str;

class UserPolicy
{
    protected String $model = User::class;

    public function viewAny(User $user): bool
    {
        return $user->can('Manage Users');
    }

    public function view(User $user): bool
    {
        return $user->can('Manage Users');
    }

    public function create(User $user): bool
    {
        return $user->can('Manage Users');
    }

    public function update(User $user): bool
    {
        return $user->can('Manage Users');
    }

    public function delete(User $user): bool
    {
        return $user->can('Manage Users');
    }

}
