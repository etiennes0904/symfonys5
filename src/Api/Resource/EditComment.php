<?php

namespace App\Api\Resource;

class EditComment
{
    public ?string $comment = null;
    public ?string $author = null; // Assuming author is passed as a string (e.g., UUID or email)
    public ?string $content = null; // Assuming content is passed as a string (e.g., UUID)
}