<?php

namespace App\Serializer\Normalizer;

use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class TimestampableEntityNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
  use NormalizerAwareTrait;

  private const ALREADY_CALLED = 'TimestampableEntity_NORMALIZER_ALREADY_CALLED';

  public function normalize($object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
  {

    $context[self::ALREADY_CALLED] = true;
    $data = $this->normalizer->normalize($object, $format, $context);
    $data['createdAt'] = $object->getCreatedAt()->format('c');
    $data['updatedAt'] = $object->getUpdatedAt()->format('c');
    return $data;
  }

  public function supportsNormalization($data, ?string $format = null, array $context = []): bool
  {
    if (isset($context[self::ALREADY_CALLED]) || !\is_object($data)) {
      return false;
    }
    return  \in_array(TimestampableEntity::class, \class_uses($data));
  }
}
