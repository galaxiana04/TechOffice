<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobticketPart>
 */
class JobticketPartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'unit_id' => $this->faker->numberBetween(1, 20),           // contoh unit_id
            'proyek_type_id' => $this->faker->numberBetween(1, 20),    // contoh proyek_type_id
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
