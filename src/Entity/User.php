<?php declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Api\Processor\CreateUserProcessor;
use App\Api\Processor\DeleteUserProcessor;
use App\Api\Processor\EditUserProcessor;
use App\Api\Resource\CreateUser;
use App\Api\Resource\EditUser;
use App\Doctrine\Trait\TimestampableTrait;
use App\Doctrine\Trait\UuidTrait;
use App\Enum\TableEnum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource()]
#[Get]
#[GetCollection]
#[ORM\Table(name: TableEnum::USER)]
#[Post(input: CreateUser::class, processor: CreateUserProcessor::class, security: 'is_granted("ROLE_ADMIN")')]
#[Put(input: EditUser::class, processor: EditUserProcessor::class, security: 'is_granted("ROLE_ADMIN") and object == user')]
#[Delete(processor: DeleteUserProcessor::class, security: 'is_granted("ROLE_ADMIN")')]
#[ApiFilter(SearchFilter::class, properties: ['firstName' => 'partial', 'lastName' => 'partial', 'email' => 'exact'])]
#[ApiFilter(BooleanFilter::class, properties: ['enabled'])]
#[ApiFilter(NumericFilter::class, properties: ['age'])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'firstName', 'lastName'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use UuidTrait;
    use TimestampableTrait;

    #[ORM\Column]
    #[Assert\NotBlank]
    public ?string $firstName = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    public ?string $lastName = null;

    #[ORM\Column(unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    public ?string $password = null;

    #[ORM\Column]
    public array $roles = [];

    #[ORM\Column(type: 'boolean')]
    public bool $enabled = true;

    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $age = null;

    public function __construct()
    {
        $this->defineUuid();
    }

    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
