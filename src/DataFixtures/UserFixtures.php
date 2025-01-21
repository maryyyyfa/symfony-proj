<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
  private UserPasswordHasherInterface $hasher;

  public function __construct(UserPasswordHasherInterface $hasher)
  {
    $this->hasher = $hasher;
  }

  public function load(ObjectManager $manager): void
  {
    // Create admin user
    $admin = new User();
    $admin->setEmail('admin@example.com');
    $admin->setName('Admin User');
    $admin->setRoles(['ROLE_ADMIN']);
    $admin->setPassword($this->hasher->hashPassword($admin, 'password123'));

    $manager->persist($admin);

    // Create a regular user
    $user = new User();
    $user->setEmail('user@example.com');
    $user->setName('Regular User');
    $user->setRoles(['ROLE_USER']);
    $user->setPassword($this->hasher->hashPassword($user, 'user123'));

    $manager->persist($user);

    $manager->flush();
  }
}
