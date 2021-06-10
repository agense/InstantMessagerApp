<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = User::class;

    /**
     * Configure the model factory.
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            $users = User::where('id', '!=', $user->id)->inRandomOrder()->limit(1)->get();

            if ($users->isNotEmpty()) {
                $user->contacts()->syncWithoutDetaching([$users->first()->id]);
                $users->first()->contacts()->syncWithoutDetaching([$user->id]);
             }
        });
    }
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'phone' => $this->faker->phoneNumber,
            'remember_token' => Str::random(10),
        ];
    }
}
