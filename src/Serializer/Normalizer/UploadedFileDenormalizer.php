<?php
// api/src/Serializer/MediaObjectNormalizer.php

namespace App\Serializer\Normalizer;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;


final class UploadedFileDenormalizer implements DenormalizerInterface
{



  /**
   * {@inheritdoc}
   */
  public function denormalize($data, string $type, string $format = null, array $context = []): ?UploadedFile
  {
    // handle the case when the image input is left blank (empty string)
    if ($data === '') {
      $data = null;
    }
    return $data;
  }

  /**
   * {@inheritdoc}
   */
  public function supportsDenormalization($data, $type, $format = null): bool
  {

    return $data instanceof UploadedFile || $type === File::class;
  }
}
