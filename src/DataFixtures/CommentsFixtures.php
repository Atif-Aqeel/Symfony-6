<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentsFixtures extends Fixture
{
    // Load Comment Fixtures
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i <= 10; $i++) {

            $comment = new Comment();
            $comment->setText('Comment ' . $i);
            $comment->setCreatedAt($faker->dateTime('now'));

            $post = $this->getReference("post");
            $comment->setPost($post);

            $user = $this->getReference("user");
            // $user = $this->getUser();
            $comment->setCommentUser($user);

            $manager->persist($comment);
        }
        $manager->flush();
    }
}
