<?php

namespace App\Entity;

use App\Entity\User;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\PostsRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Doctrine\Odm\Filter\SearchFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post as MetadataPost;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

use function PHPSTORM_META\type;

// by using this, it displays on Api Platform as Swagger UI
#[

    ApiResource(
        operations: [
            new GetCollection(
                provider: CollectionProvider::class,
            ),
            new MetadataPost(
                provider: CollectionProvider::class,
                security: 'is_granted("ROLE_ADMIN")',
            ),
            new Get(
                provider: ItemProvider::class,
            ),
            new Put(
                security: 'is_granted("ROLE_USER") and object.getOwner() == user ',
                securityMessage: 'A Post can only be Updated by owner',
            ),
            new Patch(),
            new Delete(),
            // new Get(uriTemplate: '/api/{id}'),
            // new Get(uriTemplate: '/api/{id}', controller: GetWeather::class),
        ],
        paginationEnabled: true,
        paginationItemsPerPage: 5
    )

]

// it shows this is a doctrine entity
#[ORM\Entity(repositoryClass: PostsRepository::class)]
class Post
{
    // this is the primary key
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // this is the title of the post
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    // this is the description of the post
    #[ORM\Column(length: 255)]
    // #[@Assert\NotNull]
    private ?string $description = null;


    // this is the user of the post
    // This will show the relationship between User and Posts
    /**
     * @var User|null The User of this Post
     * 
     */
    #[ORM\ManyToOne(
        targetEntity: User::class,
        inversedBy: 'posts'
    )]
    // #[ORM\JoinColumn(nullable: false)]
    private ?User $user;


    // this is the owner of the post
    #[ORM\ManyToOne(
        targetEntity: User::class,
        inversedBy: 'posts'
    )]
    #[
        Groups(['post.read', 'post.write']),
    ]
    private ?User $owner = null;


    #[ORM\OneToMany(mappedBy: 'Post', targetEntity: Comment::class)]
    private Collection $comments;

    public function __construct()
    {
        // $this->user = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }



    // Getters/Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    // public function setId(int $id): static
    // {
    //     $this->id = $id;

    //     return $this;
    // }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }


    // User of the post
    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $users): static
    {
        // if (!$this->user->contains($users)) {
        // $this->user[] = $users;
        // $this->user->add($users);
        // }
        $this->user = $users;
        return $this;
    }


    // Owner of the post
    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }
}
