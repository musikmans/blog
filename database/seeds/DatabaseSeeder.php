<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user_admin = [];
        $faker = Faker::create();
        //generate list of user
    	foreach (range(1,10) as $index) {
            // we radomly decide is the user is an admin here
            // if the user is an admin, we add the index to an array of admin user
            $value = rand(0, 1);
            if ($value===1) {
                array_push($user_admin, $index);
            }
	        DB::table('users')->insert([
	            'name' => $faker->name,
	            'email' => $faker->email,
                'password' => bcrypt('secret'),
                'isAdmin' => $value,
	        ]);
        }
        // generate posts
        foreach (range(1,20) as $index) {
            $date = $faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = 'America/Vancouver');
            $k = array_rand($user_admin);
            $userId = $user_admin[$k];
	        DB::table('posts')->insert([
                'user_id' => $userId,
	            'title' => $faker->catchPhrase,
                'content' => $faker->realText($maxNbChars = 400, $indexSize = 2),
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            
            // generate comments
            foreach (range(1,10) as $commentsIndex) {
                $commentdate = $faker->dateTimeBetween($startDate = $date, $endDate = 'now', $timezone = 'America/Vancouver');
                $post_id =  $index;
                $user_id = rand(1,10);
                DB::table('comments')->insert([
                    'user_id' => $user_id,
                    'post_id' => $post_id,
                    'body' => $faker->realText($maxNbChars = 400, $indexSize = 2),
                    'created_at' => $commentdate,
                    'updated_at' => $commentdate,
                ]);
            }
        }

        // generate likes
        foreach (range(1,100) as $index) {
            $user_id = rand(1,10);
            $post_id = rand(1,20);
            $values = array('-1','1');
            $key = array_rand($values);
            $like_it_or_not = intval($values[$key]);
	        DB::table('likes')->insert([
                'user_id' => $user_id,
	            'post_id' => $post_id,
                'score' => $like_it_or_not,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
	        ]);
        }

        // generate hashtags
        foreach (range(1,100) as $index) {
	        DB::table('hashtags')->insert([
                'hashtag' => $faker->word,
	        ]);
        }

        foreach (range(1,500) as $index) {
            $post_id = rand(1,20);
            $hashtag_id = rand(1,100);
	        DB::table('posts_hashtags')->insert([
                'post_id' => $post_id,
                'hashtag_id' => $hashtag_id,
	        ]);
        }
    }
}
