<?php


use Phinx\Seed\AbstractSeed;

class PostSeeder extends AbstractSeed
{
    public function run()
    {
        $faker = \Faker\Factory::create('fr_FR');
        $data = [];
        for ($i = 0; $i < 40; $i++) {
            $date = $faker->unixTime('now');
            $data[] = [
                'title'         => $faker->catchPhrase,
                'slug'          => $faker->slug,
                'category_id'   => rand(1, 5),
                'author_id'     => 1,
                'header'        => $faker->text(200),
                'content'       => $faker->text(3000),
                'created_at'    => date('Y-m-d H:i:s', $date),
                'updated_at'    => date('Y-m-d H:i:s', $date),
                'published'     => 1
            ];
        }
        $this->table('posts')->insert($data)->save();
    }
}
