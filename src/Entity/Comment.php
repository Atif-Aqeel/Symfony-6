<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ApiResource]

class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $text = null;

    #[ORM\Column]
    private ?User $commentUser;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $created_at = null;



    #[ORM\ManyToOne(targetEntity: 'App\Entity\Post', inversedBy: 'comments')]
    #[ORM\JoinColumn(name: 'post_id', referencedColumnName: 'id', nullable: false)]
    private ?Post $post;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getCommentUser(): ?int
    {
        return $this->commentUser;
    }

    public function setCommentUser(User $commentUser): static
    {
        $this->commentUser = $commentUser;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTime $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }



    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $posts): static
    {
        $this->post = $posts;

        return $this;
    }
}
