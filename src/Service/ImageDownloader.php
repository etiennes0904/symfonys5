<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Upload;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use const FILTER_VALIDATE_URL;
use const PATHINFO_EXTENSION;
use const PHP_URL_PATH;

class ImageDownloader
{
    private string $uploadDir;

    public function __construct(
        #[Autowire(param: 'kernel.project_dir')]
        private string $projectDir,
        private EntityManagerInterface $em,
    ) {
        // Définit le répertoire de téléchargement
        $this->uploadDir = $this->projectDir . '/public/medias';
    }

    public function downloadImage(string $url): Upload
    {
        // Vérifie si l'URL est valide
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid URL: ' . $url);
        }

        // Récupère le contenu de l'image
        $imageContent = @file_get_contents($url);
        if (false === $imageContent) {
            throw new RuntimeException('Failed to download image from: ' . $url);
        }

        // Détermine l'extension de l'image
        $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
        if (!$extension) {
            $extension = 'jpg';
        }

        // Génère un nom unique pour l'image
        $fileName = uniqid('img_', true) . '.' . $extension;

        // Chemin pour sauvegarder l'image
        $filePath = $this->uploadDir . '/' . $fileName;

        // Sauvegarde l'image
        try {
            file_put_contents($filePath, $imageContent);
        } catch (FileException $e) {
            throw new RuntimeException('Failed to save image to: ' . $filePath);
        }

        $upload = new Upload();
        $upload->setPath("/medias/{$fileName}");
        $this->em->persist($upload);
        $this->em->flush();

        return $upload;
    }
}