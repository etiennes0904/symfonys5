<?php declare(strict_types=1);

namespace App\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Content;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CreateContentProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em, // autowire comme pour votre commande
    ) {
    }

    /** @param Content $data */
    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = [],
    ): Content {
        // Les données envoyées dans le POST sont stockées dans $data
        // Vous pouvez reprendre la logique de votre commande pour 
        // créer votre contenu

        // Assurez-vous que l'auteur est défini
        if (!$data->author instanceof User) {
            throw new \InvalidArgumentException('Author must be a valid User entity.');
        }

        // Votre logique pour créer le contenu
        $content = new Content();
        $content->title = $data->title;
        $content->content = $data->content;
        $content->cover = $data->cover;
        $content->author = $data->author;
        $content->tags = $data->tags;

        $this->em->persist($content);
        $this->em->flush();

        return $content;
    }
}