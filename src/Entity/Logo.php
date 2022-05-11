<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\LogoRepository;
use App\Controller\EmptyController;
use Gedmo\Mapping\Annotation as Gedmo;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\HttpFoundation\File\File;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;


#[ORM\Entity(repositoryClass: LogoRepository::class)]
#[ApiResource(
    security: "is_granted('ROLE_ADMIN')",
    // security: "is_granted('PUBLIC_ACCESS')",

    normalizationContext: ["groups" => ["logo:read"]],
    denormalizationContext: ["groups" => ["logo:write"]],
    collectionOperations: [
        "get" => [
            "security" => "is_granted('PUBLIC_ACCESS')"
        ],
        "post" => [
            'input_formats' => [
                'multipart' => ['multipart/form-data'],
            ],
            'validation_groups' => ['Default', 'logo_create'],
        ]
    ],
    itemOperations: [
        'get' => [
            'security' => "is_granted('PUBLIC_ACCESS')"
        ],
        'delete',

        'post' => [
            'method' => 'POST',
            'input_formats' => [
                'multipart' => ['multipart/form-data'],
            ],
            'controller' => EmptyController::class,
            'openapi_context' => [
                'summary' => ' Update a Logo resource ',
                'description' => ' Update a Logo resource ',
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'title' => [
                                        'type' => 'string'
                                    ],
                                    'url' => [
                                        'type' => 'string',
                                        'format' => 'url',
                                        'nullable' => true
                                    ],
                                    'imageFile' => [
                                        'description' => "The Image UploadedFile object.",
                                        'type' => 'string',
                                        'format' => 'binary',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

        ],

    ]

)]
#[ApiFilter(OrderFilter::class)]
/**
 * @Vich\Uploadable
 */
class Logo
{
    use TimestampableEntity;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['logo:read'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    #[Groups(['logo:read', 'logo:write'])]
    #[Assert\NotBlank()]

    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    #[Gedmo\Slug(fields: ['title'])]
    private $slug;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['logo:read', 'logo:write'])]
    #[Assert\Url()]
    private $url;

    /** 
     * The image file nanme.
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $imageName;

    /** 
     * The image url relative to public directory.
     * 
     * @var string|null
     */
    #[Groups(['logo:read'])]
    #[ApiProperty(iri: 'http://schema.org/contentUrl')]
    private $imageUrl;

    /**
     * The Image UploadedFile object.
     * 
     * @Vich\UploadableField(mapping="logo_image", fileNameProperty="imageName")
     * 
     * @var File|null
     */
    #[Groups(['logo:write'])]
    #[Assert\NotNull(groups: ['logo_create'])]
    #[Assert\Image()]
    private $imageFile;



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

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /** 
     * @param File|UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;
        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime();
        }
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }
}
