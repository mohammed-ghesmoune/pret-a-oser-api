<?php

namespace App\DataPersister;

use App\Entity\User;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserDataPersister implements ContextAwareDataPersisterInterface
{


  public function __construct(
    private ContextAwareDataPersisterInterface $decoratedDataPersister,
    private UserPasswordHasherInterface $passwordHasher
  ) {
  }
  public function supports($data, array $context = []): bool
  {
    return $data instanceof User;
  }
  /**
   *
   * @param User $data
   * @return object|void
   */
  public function persist($data, array $context = [])
  {

    if ($data->getPlainPassword()) {
      $data->setPassword(
        $this->passwordHasher->hashPassword($data, $data->getPlainPassword())
      );
      $data->eraseCredentials();
    }
    return  $this->decoratedDataPersister->persist($data, $context);
  }
  public function remove($data, array $context = [])
  {
    return $this->decoratedDataPersister->remove($data, $context);
  }
}
