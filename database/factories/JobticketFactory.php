<?php

namespace Database\Factories;

use App\Models\JobticketIdentity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Jobticket>
 */
class JobticketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'documentname' => $this->faker->words(2, true),
            'rev' => 1,
            'inputer_id' => 1,
            'approver_id' => 2,
            'publicstatus' => 'draft',
            'jobticket_identity_id' => JobticketIdentity::factory(),
        ];
    }
}
