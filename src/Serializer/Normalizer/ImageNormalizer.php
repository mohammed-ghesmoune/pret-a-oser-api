<?php

namespace App\Serializer\Normalizer;

use App\Entity\Logo;
use App\Entity\Page;
use App\Entity\Image;
use Vich\UploaderBundle\Storage\StorageInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

final class ImageNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
  use NormalizerAwareTrait;

  private const ALREADY_CALLED = 'IMAGE_NORMALIZER_ALREADY_CALLED';

  public function __construct(private StorageInterface $storage)
  {
  }

  public function normalize($object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
  {
    $context[self::ALREADY_CALLED] = true;
    $imageUrl = \str_starts_with($object->getImageName(), 'http') ? $object->getImageName() : $this->storage->resolveUri($object, 'imageFile');
    $object->setImageUrl($imageUrl);


    return $this->normalizer->normalize($object, $format, $context);
  }

  public function supportsNormalization($data, ?string $format = null, array $context = []): bool
  {
    if (isset($context[self::ALREADY_CALLED])) {
      return false;
    }
    return  $data instanceof Image || $data instanceof Page || $data instanceof Logo;
  }
}
