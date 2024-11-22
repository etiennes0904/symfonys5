<?php declare(strict_types=1);

namespace App\Api\Resource;

class EditUser
{
    public ?string $firstName = null;
    public ?string $lastName = null;
    public ?string $email = null;
    public ?string $password = null;
}
