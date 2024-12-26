<?php

namespace Database\Seeders\firebase;

use App\Models\FirebaseTopic;
use Illuminate\Database\Seeder;

class FirebaseTopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        return FirebaseTopic::create([
            'name' => FirebaseTopic::DEFAULT_TOPIC
        ]);
    }
}
