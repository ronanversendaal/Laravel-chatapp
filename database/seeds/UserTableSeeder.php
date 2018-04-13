<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class)->create(
            [
                'name' => 'Chatbot',
                'email' => 'chatbot@chatapp.test',
                'admin' => '0',
            ]
        );

        factory(App\User::class)->create(
            [
                'name' => 'Ronan Versendaal',
                'email' => 'ronan@chatapp.test',
                'admin' => '1',
                'password' => bcrypt('admin')
            ]
        );
    }
}
