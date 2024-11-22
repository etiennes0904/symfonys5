<?php declare(strict_types=1);

namespace App\Doctrine\Trait;

use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait TimestampableTrait
{
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, insertable: false, updatable: false, options: ['default' => 'CURRENT_TIMESTAMP'], generated: 'INSERT')]
    public ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, insertable: false, updatable: false, columnDefinition: 'DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP', generated: 'ALWAYS')]
    public ?DateTime $updatedAt = null;
}
