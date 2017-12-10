<?php


use Phinx\Seed\AbstractSeed;

class CategorySeeder extends AbstractSeed
{
    public function run()
    {
        $data = [];
        $faker = \Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 5; $i++) {
            $data[] = [
                'name'         => $faker->word,
                'slug'         => $faker->slug
            ];
        }
        $this->table('categories')->insert($data)->save();
    }
}
