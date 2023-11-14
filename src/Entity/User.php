<?php

namespace App\Entity;

use App\Entity\Post;
use App\Entity\Comment;
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
    const ROLE_USER = 'ROLE_USER';
    const DEFAULT_ROLES = [self::ROLE_USER];



    // This is the Id of user
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;
    // private ?int $id = null;

    // This is the email of user
    #[ORM\Column(type: "string", length: 180, unique: true)]
    // private ?string $email = null;
    private $email;

    // This is the roles of user
    #[ORM\Column(type: "json", length: 200, nullable: true)]
    // private array $roles = [];
    private $roles;

    // This is the password of user
    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: "string", length: 255)]
    // private ?string $password;
    private $password;



    // This is the posts of user // This will show the relationship between User and Posts
    // /**
    //  * @var Post[] Available Posts from this user
    //  * 
    //  */
    // #[ApiSubresource]
    #[ORM\OneToMany(
        targetEntity: Post::class,
        mappedBy: 'user',
    )]
    // private Collection $posts;
    private $posts;


    #[ORM\OneToMany(
        targetEntity: Comment::class,
        mappedBy: 'user'
    )]
    private $comments;



    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        // $this->roles = self::DEFAULT_ROLES;
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

    public function setEmail(string $email): self
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
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }



    //getter for Post
    /**
     * @return Collection|Post[]
     */
    public function getPost(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }
        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }
        return $this;
    }



    /**
     * @return Collection|Comment[]
     */
    public function getComment(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }
        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
            return $this;
        }
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

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
        return $this;
    }

    // isAdmin() method
    public function isAdmin(): bool
    {
        return in_array(self::ROLE_ADMIN, $this->getRoles());
    }



    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }


    /**
     * Removes sensitive data from the user.
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     * 
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }








    // 
}
