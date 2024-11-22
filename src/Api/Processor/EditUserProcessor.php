<?php declare(strict_types=1);

namespace App\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Resource\EditUser;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class EditUserProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher,
    ) {
    }

    /** @param EditUser $data */
    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = [],
    ): User {
        // Rechercher l'utilisateur par son identifiant
        $user = $this->em->getRepository(User::class)->find($uriVariables['id']);
        if (!$user) {
            throw new InvalidArgumentException('User not found');
        }

        // Mettre à jour les informations de l'utilisateur
        $user->firstName = $data->firstName ?? $user->firstName;
        $user->lastName = $data->lastName ?? $user->lastName;
        $user->email = $data->email ?? $user->email;

        if ($data->password) {
            $user->setPassword($this->hasher->hashPassword($user, $data->password));
        }

        $this->em->flush();

        return $user;
    }
}
