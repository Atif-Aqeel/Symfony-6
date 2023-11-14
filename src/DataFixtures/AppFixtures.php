<?php

namespace App\DataFixtures;


use App\Entity\User;
use App\Entity\Post;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private $passwordEncoder;
    private $faker;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {

        $this->loadUser($manager);
        $this->loadPost($manager);
        $this->loadComments($manager);
    }

    public function loadUser(ObjectManager $manager)
    {
        // $roles = [User::ROLE_ADMIN, User::ROLE_USER];

        for ($i = 0; $i < 10; $i++) {

            $user = new User();

            $user->setEmail($this->faker->email);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordEncoder->hashPassword($user, 'sercet123'));

            $this->addReference("user_$i", $user);

            $manager->persist($user);
        }

        $manager->flush();
    }


    // Load Posts
    public function loadPost(ObjectManager $manager)
    {

        for ($i = 0; $i < 10; $i++) {

            $post = new Post();

            $post->setTitle($this->faker->sentence());
            $post->setDescription($this->faker->realText(50));

            // $user = $this->getReference("user");
            $user = $this->getReference("user_" . rand(0, 9));
            $this->addReference("post_$i", $post);

            $post->setUser($user);

            $manager->persist($post);
        }

        $manager->flush();
    }


    // Comment Fixtures
    public function loadComments(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {

            $comment = new Comment();

            $comment->setText($this->faker->realText());
            $comment->setCreatedAt(new \DateTime());

            // $user = $this->getReference("user");
            $user = $this->getReference("user_" . rand(0, 9));
            $post = $this->getReference("post_$i");

            $comment->setUser($user);
            $comment->setPost($post);

            $manager->persist($comment);
        }
        $manager->flush();
    }
}
