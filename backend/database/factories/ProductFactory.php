<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * A fixed, on-brand catalog for demo seeding — see the `industrial()`
     * state below and its use in DatabaseSeeder.
     *
     * @var array<int, array{name: string, description: string}>
     */
    public static array $catalog = [
        ['name' => 'Wizbang Widget', 'description' => "Does something. Nobody's quite sure what, but it does it well."],
        ['name' => 'Boltzilla', 'description' => 'A fastener of unusual size, for jobs of unusual size.'],
        ['name' => 'Ratchetron 5000', 'description' => 'Ratchets. Repeatedly. Relentlessly.'],
        ['name' => 'Grumblegear Gearbox', 'description' => 'Shifts smoothly. Complains loudly.'],
        ['name' => 'Rustbucket Rivet Gun', 'description' => 'Holds the whole operation together, one bang at a time.'],
        ['name' => 'Sparkplug Supreme', 'description' => 'Ignition with attitude.'],
        ['name' => 'Torqueasaurus', 'description' => 'Extinction-level twisting force in a hand-held package.'],
        ['name' => 'Megaflange Mounting Bracket', 'description' => 'Flanged for your protection.'],
        ['name' => 'Snaggletooth Sprocket', 'description' => 'Every tooth a little different. Somehow it still works.'],
        ['name' => 'Zapmaster Zener Diode', 'description' => 'Regulates voltage. Judges your wiring.'],
        ['name' => 'Grease Weasel Lubricant', 'description' => 'Slippery when applied. Sold in industrial quantities.'],
        ['name' => 'Anvil Annihilator', 'description' => "For when a regular anvil just won't do."],
        ['name' => 'Flywheel Frenzy', 'description' => 'Stores energy. Releases chaos.'],
        ['name' => 'Bazooka Bearing', 'description' => 'Rolls smoother than it sounds.'],
        ['name' => 'Thundergasket', 'description' => 'Seals the deal, and everything else.'],
    ];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->words(3, true)),
            'sku' => strtoupper(fake()->unique()->bothify('??-####')),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 5, 500),
        ];
    }

    /**
     * Cycle through the catalog above in order (by row index). SKU and price
     * stay randomized. Falls back to the default Faker name/description for
     * any rows beyond the catalog's length.
     */
    public function industrial(): static
    {
        return $this->sequence(fn ($sequence) => self::$catalog[$sequence->index] ?? []);
    }
}
