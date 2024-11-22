<?php declare(strict_types=1);

namespace App\Api\Action;

use App\Entity\Content;
use App\Entity\Tag;
use App\Service\ImageDownloader;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\String\Slugger\SluggerInterface;
use function count;
use const FILTER_VALIDATE_URL;

#[AsController]
class ImportCsvAction
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        #[Autowire(param: 'kernel.project_dir')]
        private string $uploadDir,
        private SluggerInterface $slugger,
        private Security $security,
        private ImageDownloader $imageDownloader,
    ) {
    }

    public function __invoke(Request $request): array
    {
        $file = $request->files->get('file');
        if (!$file || 'csv' !== $file->getClientOriginalExtension()) {
            throw new BadRequestHttpException('Please upload a valid CSV file.');
        }

        // Lire et traiter le fichier CSV
        $csvData = array_map('str_getcsv', file($file->getPathname()));
        $headers = array_shift($csvData); // Récupérer les en-têtes

        $requiredColumns = ['title', 'metaTitle', 'metaDescription', 'content', 'tags'];
        if (array_diff($requiredColumns, $headers)) {
            throw new BadRequestHttpException('The CSV file is missing required columns.');
        }

        $importedContents = [];
        foreach ($csvData as $line) {
            $data = array_combine($headers, $line);
            // Validation minimale des données
            if (empty($data['title']) || empty($data['content'])) {
                throw new BadRequestHttpException('Title and content are required.');
            }

            $content = new Content();
            $content->setTitle($data['title']);
            $content->setMetaTitle($data['metaTitle']);
            $content->setMetaDescription($data['metaDescription']);
            $content->setContent($data['content']);

            $date = new DateTime();
            $content->setSlug($this->slugger->slug($data['title'])->lower()->toString() . '-' . $date->format('Y-m-d-H-i-s'));

            $content->setAuthor($this->security->getUser());

            // Gestion des tags
            $tagNames = explode('|', $data['tags']);
            foreach ($tagNames as $tagName) {
                $tag = $this->getOrCreateTag(trim($tagName));
                $content->addTag($tag);
            }

            // Téléchargement de l'image
            if (!empty($data['cover']) && filter_var($data['cover'], FILTER_VALIDATE_URL)) {
                try {
                    $upload = $this->imageDownloader->downloadImage($data['cover']);
                    $content->setCover($upload->getPath());
                } catch (Exception $e) {
                    throw new BadRequestHttpException('Failed to download image: ' . $e->getMessage());
                }
            }

            $this->entityManager->persist($content);
            $importedContents[] = $content;
        }

        $this->entityManager->flush();

        return [
            'status' => 'success',
            'imported' => count($importedContents),
        ];
    }

    private function getOrCreateTag(string $tagName): Tag
    {
        $tag = $this->entityManager->getRepository(Tag::class)->findOneBy(['name' => $tagName]);

        if (!$tag) {
            $tag = new Tag();
            $tag->setName($tagName);
            $this->entityManager->persist($tag);
        }

        return $tag;
    }
}