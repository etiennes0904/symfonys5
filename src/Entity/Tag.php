<?php declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\Trait\UuidTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'tag')]
class Tag
{
    use UuidTrait;

    #[ORM\Column(type: 'string', unique: true)]
    #[Assert\NotBlank]
    private ?string $name = null;

    public function __construct()
    {
        $this->defineUuid();
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
