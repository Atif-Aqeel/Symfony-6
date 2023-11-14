<?php

namespace App\Entity;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\PostsRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
// #[ApiResource]

class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $text;

    #[ORM\Column(type: "datetime")]
    private $created_at;


    #[ORM\ManyToOne(targetEntity: 'App\Entity\User', inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: true)]
    // #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    // private User $commentUser;
    private $user;


    #[ORM\ManyToOne(targetEntity: 'App\Entity\Post', inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    // #[ORM\JoinColumn(name: 'post_id', referencedColumnName: 'id', nullable: false)]
    // private Post $posts;
    private $post;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }


    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }


    // /**
    //  * @return Collection<int, Post>
    //  */
    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }


    // public function addPost(Post $post): static
    // {
    //     if (!$this->posts->contains($post)) {
    //         $this->posts->add($post);
    //         $post->addComment($this);
    //     }
    //     return $this;
    // }

    // public function removePost(Post $post): static
    // {
    //     if ($this->posts->removeElement($post)) {
    //         $post->removeComment($this);
    //     }
    //     return $this;
    // }
}
