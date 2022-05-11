<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Repository\PrestationRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;


#[ORM\Entity(repositoryClass: PrestationRepository::class)]
#[ApiResource(
    security: "is_granted('ROLE_ADMIN')",
    normalizationContext: ['groups' => ['prestation:read']],
    denormalizationContext: ['groups' => ['prestation:write']],
    collectionOperations: [
        'post',
        'get' => [
            'security' => "is_granted('PUBLIC_ACCESS')"
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['prestation:read', 'prestation:item:get']],
            'security' => "is_granted('PUBLIC_ACCESS')"

        ],
        'put', 'patch', 'delete'
    ]
)]

#[ApiFilter(SearchFilter::class, properties: ['category.title' => 'partial'])]
#[ApiFilter(OrderFilter::class)]

class Prestation
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['prestation:read', 'category:read'])]
    #[ApiFilter(SearchFilter::class, strategy: 'exact')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['prestation:read', 'prestation:write', 'image:read', 'category:read'])]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    #[Assert\NotBlank()]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    #[Gedmo\Slug(fields: ['title'])]
    #[Groups(['prestation:read', 'category:read'])]
    private $slug;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['prestation:write', 'prestation:read'])]
    #[Assert\NotBlank()]
    private $exerpt;

    #[ORM\Column(type: 'text')]
    #[Groups(['prestation:item:get', 'prestation:write', 'prestation:read'])]
    #[Assert\NotBlank()]
    private $content;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['prestation:read', 'prestation:write'])]
    private $price;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Groups(['prestation:read', 'prestation:write'])]
    // #[Assert\Regex('\d{,2}:\d{2}')]
    private $duration;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'prestations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['prestation:read', 'prestation:write'])]
    #[Assert\NotBlank()]
    private $category;

    #[ORM\OneToMany(mappedBy: 'prestation', targetEntity: Image::class)]
    #[Groups(['prestation:read', 'prestation:write'])]
    #[ApiProperty(iri: 'http://schema.org/image')]
    private $images;



    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(?string $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setPrestation($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getPrestation() === $this) {
                $image->setPrestation(null);
            }
        }

        return $this;
    }

    public function getExerpt(): ?string
    {
        return $this->exerpt;
    }

    public function setExerpt(string $exerpt): self
    {
        $this->exerpt = $exerpt;

        return $this;
    }
}
