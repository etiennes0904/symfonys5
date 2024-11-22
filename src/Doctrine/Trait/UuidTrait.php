<?php declare(strict_types=1);

namespace App\Doctrine\Trait;

use ApiPlatform\Metadata\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
trait UuidTrait
{ 
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ApiProperty(identifier: true)]
    public ?Uuid $uuid = null;

    public function defineUuid(): void
    {
        $this->uuid ??= Uuid::v4();
    }
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(?string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }
}