<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\VerifyOldPasswordController;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


#[ORM\Entity(repositoryClass: UserRepository::class)]

#[UniqueEntity(fields: ['email'])]
#[UniqueEntity(fields: ['username'])]

#[ApiResource(
    normalizationContext: ['groups' => ['user:read'], 'swagger_definition_name' => 'read'],
    denormalizationContext: ['groups' => ['user:write'], 'swagger_definition_name' => 'write'],
    collectionOperations: [
        'get' => [
            "security" => "is_granted('IS_AUTHENTICATED_FULLY') or is_granted('ROLE_ADMIN')",
        ],
        'post' => [
            "security" => "is_granted('PUBLIC_ACCESS')",
            "validation_groups" => ['Default', 'create']
        ],
    ],
    itemOperations: [
        'get' => [
            "security" => "(is_granted('IS_AUTHENTICATED_FULLY') and object == user) or is_granted('ROLE_ADMIN')",
        ],
        'put' => [
            "security" => "(is_granted('IS_AUTHENTICATED_FULLY') and object == user) or is_granted('ROLE_ADMIN')",
        ],
        'patch' =>  [
            "security" => "(is_granted('IS_AUTHENTICATED_FULLY') and object == user) or is_granted('ROLE_ADMIN')",
        ],
        'delete' => [
            "security" => "is_granted('ROLE_ADMIN')"
        ],
        'verifyOldPassword' => [
            "security" => "(is_granted('IS_AUTHENTICATED_FULLY') and object == user) or is_granted('ROLE_ADMIN')",
            'method' => 'PUT',
            'path' => '/users/{id}/verify-old-password',
            'write' => false,
            'validate' => false,
            'controller' => VerifyOldPasswordController::class,
            'openapi_context' => [
                'summary' => 'Verify if password valid',
                'descroption' => 'Check if the password is valid before modify it',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'password' => [
                                        'type' => 'string',
                                        'example' => '1234'
                                    ]
                                ]
                            ]
                        ]

                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => '"true" if the provided password is valid , "false" otherwise',
                        'content' => [
                            'application/json' => [
                                'type' => 'boolean'
                            ],
                            'application/ld+json' => [
                                'type' => 'boolean'
                            ]
                        ]
                    ],

                ]
            ]
        ]
    ]

)]

#[ApiFilter(OrderFilter::class)]
#[ApiFilter(SearchFilter::class, properties: ['username' => 'partial', 'email' => 'partial'])]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["user:read"])]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\NotBlank()]
    #[Assert\Email()]
    private $email;

    #[ORM\Column(type: 'json')]
    #[Groups(['admin:write', 'user:read'])]
    private $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: 'string')]
    private $password;

    #[Groups(['user:write'])]
    #[SerializedName("password")]
    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Length(min: 4)]
    private $plainPassword;

    #[ORM\Column(type: 'string', length: 255, nullable: false, unique: true)]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 3)]
    private $username;


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
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
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

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
}
