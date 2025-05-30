<?php

namespace App\Policies;

use App\Models\DataRequestResponse;
use App\Models\User;
use Illuminate\Support\Str;

class DataRequestResponsePolicy
{
    protected string $model = DataRequestResponse::class;

    public function view(User $user): bool
    {
        return $user->can('Read '.Str::plural(class_basename($this->model)));
    }

    public function create(User $user): bool
    {
        return $user->can('Create '.Str::plural(class_basename($this->model)));
    }

    public function update(User $user, DataRequestResponse $dataRequestResponse): bool
    {
        return $dataRequestResponse->requestee_id === $user->id;
    }

    public function delete(User $user): bool
    {
        return $user->can('Delete '.Str::plural(class_basename($this->model)));
    }
}
