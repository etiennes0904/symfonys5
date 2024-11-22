<?php declare(strict_types=1);

namespace App\Api\Action;

use App\Entity\Upload;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
class UploadAction
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        #[Autowire(param: 'kernel.project_dir')]
        private string $projectDir,
    ) {
    }

    public function __invoke(Request $request)
    {
        $file = $request->files->get('file');

        if (!$file instanceof UploadedFile) {
            throw new BadRequestHttpException('Invalid file upload.');
        }

        $path = uniqid() . '.' . $file->getClientOriginalExtension();

        $file->move($this->projectDir . '/public/medias', $path);

        $upload = new Upload();
        $upload->setPath("/medias/{$path}");

        $this->entityManager->persist($upload);
        $this->entityManager->flush();

        return $upload;
    }
}
