<?php

namespace Database\Factories;

use App\Models\SamlProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SamlProvider> */
class SamlProviderFactory extends Factory
{
    protected $model = SamlProvider::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'sso_provider_id' => \App\Models\SsoProvider::factory(),
            'entity_id' => fake()->unique()->word(),
            'metadata_xml' => '<EntityDescriptor />',
            'metadata_url' => null,
            'attribute_mapping' => null,
            'created_at' => null,
            'updated_at' => null,
            'name_id_format' => null,
        ];
    }
}
