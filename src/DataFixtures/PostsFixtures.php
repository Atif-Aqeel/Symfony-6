<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PostsFixtures extends Fixture
{
    // Load Posts
    public function load(ObjectManager $manager): void
    {

        for ($i = 0; $i < 15; $i++) {
            $post = new Post();
            // $post->setTitle($this->faker->sentence());
            $post->setTitle('Post ' . $i);
            $post->setDescription('Description ' . $i);

            $user = $this->getReference("user");
            $post->setOwner($user);

            $manager->persist($post);
        }

        $manager->flush();
    }
}
