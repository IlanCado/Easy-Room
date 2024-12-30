<?php

// database/factories/EquipmentFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EquipmentFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
        ];
    }
}

