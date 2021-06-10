<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = Message::class;

    /**
     * Define the model's default state.
     * @return array
     */
    public function definition()
    {
       
        do{
            $users = User::inRandomOrder()->limit(1)->get();
            $user = $users->first();
         }while(count($user->contacts) == 0);

        return [
            'from' => $user->id,
            'to' => $user->contacts()->first()->id,
            'message' => $this->faker->sentence,
        ];
    }
}
