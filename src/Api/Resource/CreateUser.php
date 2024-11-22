<?php declare(strict_types=1);

namespace App\Api\Resource;

class CreateUser
{
    public ?string $email = null;
    public ?string $password = null;
}
