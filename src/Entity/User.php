<?php

namespace App\Entity;

use App\Entity\Post;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Doctrine\Odm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter as FilterSearchFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Metadata as metadata;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post as MetadataPost;
use Doctrine\Common\Collections\Collection;

#[
    ApiResource(
        // collectionOperations: ['get', 'post'],
        // itemOperations: ['get', 'put', 'patch']
        operations: [
            new GetCollection(
                provider: CollectionProvider::class,
                // uriTemplate: '/api/{id}', 
            ),
            new MetadataPost(
                provider: CollectionProvider::class,
                // uriTemplate: '/api/{id}',
                security: 'is_granted("ROLE_ADMIN")',
            ),
            // new PostCollection(),
            // new Post(),
            new Get(
                provider: ItemProvider::class,
                // metadata: [
                //     'security' => 'is_granted("IS_MESSAGE_VIEW", object)',
                // ],
            ),
            new Put(),
            new Patch(),
            new Delete(),
            // new Get(uriTemplate: '/api/{id}'),
            // new Get(uriTemplate: '/api/{id}', controller: GetWeather::class),
        ],
        paginationEnabled: true,
        paginationItemsPerPage: 5
    ),

    ApiFilter(
        // SearchFilter::class,
        FilterSearchFilter::class,
        properties: [
            'id' => SearchFilter::STRATEGY_EXACT,
            'title' => SearchFilter::STRATEGY_PARTIAL,
            'description' => SearchFilter::STRATEGY_PARTIAL,
        ]
        // properties: ['title' => 'partial', 'description' => 'partial']
    )


]

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    const ROLE_ADMIN = 'ROLE_ADMIN';

    // This is the Id of user
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    // This is the email of user
    #[ORM\Column(type: "string", length: 180, unique: true)]
    private ?string $email = null;

    // This is the roles of user
    #[ORM\Column(type: "json")]
    private array $roles = [];

    // This is the password of user
    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: "string")]
    private ?string $password;


    // This is the posts of user
    // This will show the relationship between User and Posts
    /**
     * @var Post[] Available Posts from this user
     * 
     */
    // #[ApiSubresource]
    #[ORM\OneToMany(
        targetEntity: Post::class,
        mappedBy: 'user',
        // cascade: ["persist", "remove"]
    )]
    private Collection $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }


    //Getters/Setters
    public function getId(): ?int
    {
        return $this->id;
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }


    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }


    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }


    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }


    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }


    // isAdmin() method
    public function isAdmin(): bool
    {
        return in_array(self::ROLE_ADMIN, $this->getRoles());
    }





    //getter for Post
    /**
     * @return Collection<int, Post>
     */
    public function getPosts()
    {
        return $this->posts;
    }

    public function addPost(Post $p): self
    {
        if (!$this->posts->contain($p)) {
            $this->posts[] = $p;
            $p->setUser($this);
        }
        return $this;
    }

    public function removePost(Post $p): self
    {
        if ($this->posts->removeElement($p)) {
            // set the owning side to null (unless already changed)
            if ($p->getUser() === $this) {
                $p->setUser(null);
            }
            // $p->removeUser($this);
        }

        return $this;
    }
}
