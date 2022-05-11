<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TestimonialRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

#[ORM\Entity(repositoryClass: TestimonialRepository::class)]
#[ApiResource(
    security: "is_granted('ROLE_ADMIN')",
    normalizationContext: [
        'groups' => ['testimonial:read']
    ],
    denormalizationContext: [
        'groups' => ['testimonial:write']
    ],
    collectionOperations: [
        'post',
        'get' => [
            'security' => "is_granted('PUBLIC_ACCESS')"
        ]
    ],
    itemOperations: [
        'get' => [
            'security' => "is_granted('PUBLIC_ACCESS')"
        ],
        'put', 'patch', 'delete'
    ]
)]

#[ApiFilter(OrderFilter::class)]
#[ApiFilter(SearchFilter::class, properties: ['author' => 'partial', 'title' => 'partial', 'content' => 'partial'])]

class Testimonial
{

    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['testimonial:read'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['testimonial:read', 'testimonial:write'])]
    #[Assert\NotBlank()]
    private $title;

    #[ORM\Column(type: 'text')]
    #[Groups(['testimonial:read', 'testimonial:write'])]
    #[Assert\NotBlank()]
    private $content;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['testimonial:read', 'testimonial:write'])]
    #[Assert\NotBlank()]
    private $author;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }
}
