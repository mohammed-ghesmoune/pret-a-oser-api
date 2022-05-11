<?php
// api/src/Serializer/MediaObjectNormalizer.php

namespace App\Serializer\Normalizer;

use App\Entity\Image;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;


final class ImageDenormalizer implements DenormalizerAwareInterface, ContextAwareDenormalizerInterface
{

  use DenormalizerAwareTrait;
  private const ALREADY_CALLED = 'PRESTATION_DENORMALIZER_ALREADY_CALLED';

  /**
   * {@inheritdoc}
   */
  public function denormalize(mixed $data, string $type, string $format = null, array $context = []): mixed
  {
    $context[self::ALREADY_CALLED] = true;
    $data['prestation'] = null;
    return $this->denormalizer->denormalize($data, $type, $format, $context);
  }

  /**
   * {@inheritdoc}
   */
  public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
  {
    if (isset($context[self::ALREADY_CALLED])) {
      return false;
    }
    return $type === Image::class && isset($data['prestation']) && $data['prestation'] === '';
  }
}
