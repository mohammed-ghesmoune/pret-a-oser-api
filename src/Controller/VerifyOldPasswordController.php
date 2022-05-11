<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsController]
class VerifyOldPasswordController extends AbstractController
{

  public function __construct(
    private UserPasswordHasherInterface $passwordHasher,
  ) {
  }
  public function __invoke()
  {
    /** @var User $user */
    $user = $this->getUser();
    return $this->passwordHasher->isPasswordValid($user, $user->getPlainPassword());
  }
}
