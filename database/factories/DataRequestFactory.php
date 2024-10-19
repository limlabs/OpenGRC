<?php

namespace Database\Factories;

use App\Models\Audit;
use App\Models\AuditItem;
use App\Models\DataRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DataRequestFactory extends Factory
{
    protected $model = DataRequest::class;

    public function definition(): array
    {
        return [

        ];
    }
}
