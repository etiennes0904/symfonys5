<?php declare(strict_types=1);

namespace App\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Resource\CreateComment;
use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Content;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

final readonly class CreateCommentProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    /** @param CreateComment $data */
    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = [],
    ): Comment {
        // Rechercher l'auteur par son identifiant (par exemple, email ou UUID)
        $author = $this->em->getRepository(User::class)->findOneBy(['email' => $data->author]);
        if (!$author) {
            throw new UserNotFoundException('Author not found');
        }

        // Rechercher le contenu par son identifiant
        $content = $this->em->getRepository(Content::class)->find($data->content);
        if (!$content) {
            throw new \InvalidArgumentException('Content not found');
        }

        // Créer le commentaire
        $comment = new Comment();
        $comment->comment = $data->comment;
        $comment->author = $author;
        $comment->content = $content;

        $this->em->persist($comment);
        $this->em->flush();

        return $comment;
    }
}