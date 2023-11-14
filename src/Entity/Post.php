<?php

namespace App\Entity;

use App\Entity\User;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
// use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
// use ApiPlatform\Doctrine\Odm\Filter\SearchFilter;
use App\Repository\PostsRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post as MetadataPost;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Schema\Index;
use Symfony\Component\Serializer\Annotation\Groups;
// use Elastic\Elasticsearch\ClientBuilder;

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
        paginationItemsPerPage: 5,
    ),

    ApiFilter(
        SearchFilter::class,
        properties: [
            // 'name' => SearchFilter::STRATEGY_PARTIAL,
            'id' => SearchFilter::STRATEGY_EXACT,
            'title' => SearchFilter::STRATEGY_PARTIAL,
            'description' => SearchFilter::STRATEGY_PARTIAL,
        ]
    ),

]

// #[SearchFilter(index="posts")]
// it shows this is a doctrine entity
#[ORM\Entity(repositoryClass: PostsRepository::class)]

class Post
{
    // this is the primary key
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    // private ?int $id = null;
    private $id;

    // this is the title of the post
    #[ORM\Column(type: "string", length: 255)]
    // private ?string $title = null;
    private $title;

    // this is the description of the post
    #[ORM\Column(length: 255)]
    // #[@Assert\NotNull]
    private ?string $description = null;


    // this is the user of the post // This will show the relationship between User and Posts
    // /**
    //  * @var User|null The User of this Post
    //  * 
    //  */
    #[ORM\ManyToOne(
        targetEntity: User::class,
        inversedBy: 'posts'
    )]
    #[ORM\JoinColumn(nullable: true)]
    // private ?User $user = null;
    private $user;


    #[ORM\OneToMany(
        targetEntity: Comment::class,
        mappedBy: 'post'
    )]
    private $comments;


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

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }


    // User of the post
    // /**
    //  * @return User
    //  */
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $users): self
    {
        $this->user = $users;
        return $this;
    }


    // Owner of the post
    // public function getOwner(): ?User
    // {
    //     return $this->owner;
    // }

    // public function setOwner(?User $owner): static
    // {
    //     $this->owner = $owner;

    //     return $this;
    // }


    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }
        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }
        return $this;
    }

    // ElasticSearch Integration

    // public function searchTexts() #: array
    // {
    //     $client = ClientBuilder::create()
    //         ->setHosts(['http://localhost:9200'])
    //         ->build();

    //     $result = $client->info();
    //     var_dump($result);

    //     return [
    //         'title',
    //         'content'
    //     ];
    // }
}
