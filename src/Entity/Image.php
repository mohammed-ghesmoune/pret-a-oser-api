<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Controller\EmptyController;
use App\Repository\ImageRepository;
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


#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[ApiResource(
    security: "is_granted('ROLE_ADMIN')",
    // security: "is_granted('PUBLIC_ACCESS')",
    iri: 'http://schema.org/MediaObject',
    normalizationContext: ['groups' => ['image:read']],
    denormalizationContext: ['groups' => ['image:write']],

    collectionOperations: [
        'get' => [
            'security' => "is_granted('PUBLIC_ACCESS')"
        ],
        'post' => [
            'input_formats' => [
                'multipart' => ['multipart/form-data'],
            ],
            'validation_groups' => ['Default', 'image_create'],

        ],
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
                'summary' => ' Update Image resource ',
                'description' => ' Update Image resource ',
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'title' => [
                                        'type' => 'string'
                                    ],
                                    'prestation' => [
                                        'type' => 'string',
                                        'format' => 'iri-reference'
                                    ],
                                    'imageFile' => [
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

#[ApiFilter(SearchFilter::class, properties: ['title' => 'partial', 'prestation.title' => 'partial'])]
#[ApiFilter(OrderFilter::class, properties: ['prestation.title' => 'DESC'])]
#[ApiFilter(OrderFilter::class)]

/**
 * @Vich\Uploadable
 */
class Image
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['image:read', 'prestation:read'])]
    private $id;

    /** 
     * The image file nanme.
     * 
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private  $imageName;
    /** 
     * The image url relative to public directory.
     * 
     * @var string|null
     */
    #[Groups(['image:read', 'prestation:read'])]
    #[ApiProperty(iri: 'http://schema.org/contentUrl')]
    private $imageUrl;
    /**
     * The Image UploadedFile object.
     * 
     * @Vich\UploadableField(mapping="prestation_image", fileNameProperty="imageName")
     * 
     * @var File|null
     */
    #[Groups(['image:write'])]
    #[Assert\NotNull(groups: ['image_create'])]
    #[Assert\Image()]
    // #[ApiProperty(iri: 'http://schema.org/image',)]
    private $imageFile;

    #[ORM\ManyToOne(targetEntity: Prestation::class, inversedBy: 'images')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Groups(['image:read', 'image:write'])]
    #[ApiFilter(SearchFilter::class, strategy: 'exact')]
    private $prestation;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['image:read', 'image:write', 'prestation:read'])]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    private $title;



    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrestation(): ?Prestation
    {
        return $this->prestation;
    }

    public function setPrestation(?Prestation $prestation): self
    {
        $this->prestation = $prestation;

        return $this;
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
}
