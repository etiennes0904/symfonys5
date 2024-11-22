<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Doctrine\Trait\UuidTrait;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Api\Action\UploadAction;
use App\Enum\TableEnum;


#[ORM\Entity]
#[ORM\Table(name: TableEnum::UPLOAD)]
#[ApiResource()]
#[Get]
#[Post(controller: UploadAction::class, deserialize: false)]
class Upload
{
    use UuidTrait;



    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private ?string $path = null;

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function __construct()
    {
        $this->defineUuid();
    }
}