<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Option>
 */
class SettingsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'qr_side' => $this->faker->numberBetween(0, 2),
            'qr_top_margin' => $this->faker->randomFloat(max: 10),
            'qr_side_margin' => $this->faker->randomFloat(max: 10),
            'qr_scale' => $this->faker->randomFloat(max: 4),
            'img_side' => $this->faker->randomBetween(0, 2),
            'img_top_margin' => $this->faker->randomFloat(max: 10),
            'img_side_margin' => $this->faker->randomFloat(max: 10),
            'img_scale' => $this->faker->randomFloat(max: 4),
            'img_filename' => $this->faker->word() . ".png",
        ];
    }
}
