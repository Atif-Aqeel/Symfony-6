<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Post;

class PostsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        for ($i = 0; $i < 15; ++$i) {
            $post = new Post();
            $post->setTitle('Post ' . $i);
            $post->setDescription('Description ' . $i);
            $manager->persist($post);
        }

        $manager->flush();


        // $post = new Posts();
        // $post->setTitle('Christian Bale');
        // $post->setDescription('Description here');
        // $manager->persist($post);

        // $post2 = new Posts();
        // $post2->setTitle('Heath Ledger');
        // $post2->setDescription('Description here');
        // $manager->persist($post2);

        // $manager->flush();

        // $this->addReference('actor', $post);
        // $this->addReference('actor2', $post2);
    }
}
