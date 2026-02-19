<?php

namespace Database\Factories;

use App\Models\JobticketDocumentKind;  // Pastikan import ini
use App\Models\JobticketIdentity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobticketIdentity>
 */
class JobticketIdentityFactory extends Factory
{
    protected $model = JobticketIdentity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'jobticket_part_id' => $this->faker->numberBetween(1, 10),
            'jobticket_documentkind_id' => JobticketDocumentKind::factory(), // pake huruf besar K di Kind
            'documentnumber' => 'DOC-' . $this->faker->unique()->numerify('###'),
            'newprogressreportids' => json_encode([$this->faker->numberBetween(1, 100)]),
        ];
    }
}
