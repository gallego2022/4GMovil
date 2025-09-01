<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Usuario>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre_usuario' => fake()->name(),
            'correo_electronico' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'contrasena' => static::$password ??= Hash::make('password'),
            'telefono' => fake()->phoneNumber(),
            'estado' => fake()->boolean(),
            'rol' => fake()->randomElement(['admin', 'user']),
            'fecha_registro' => now(),
            'created_at' => now(),
            'updated_at' => now(),
            
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
