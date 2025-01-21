<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Créer un nouvel administrateur',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'Email de l\'administrateur')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'Mot de passe de l\'administrateur')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Nom de l\'administrateur')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $email = $input->getOption('email') ?? $io->ask('Email de l\'administrateur');
        $password = $input->getOption('password') ?? $io->askHidden('Mot de passe de l\'administrateur');
        $name = $input->getOption('name') ?? $io->ask('Nom de l\'administrateur');

        $user = new User();
        $user->setEmail($email);
        $user->setName($name);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setAdmin(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Administrateur créé avec succès.');

        return Command::SUCCESS;
    }
} 