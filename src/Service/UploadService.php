<?php

namespace App\Service;

use App\Entity\Image;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadService
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/public/uploads/photos')] private readonly string $photosDir,
        private readonly SluggerInterface $slugger,
    ) {
    }

    public function upload(UploadedFile $uploadedFile): Image
    {
        $image = (new Image())
            ->setOriginalName($uploadedFile->getClientOriginalName())
            ->setExt($uploadedFile->guessExtension())
            ->setName($this->slugger->slug($uploadedFile->getClientOriginalName()))
            ->setUploadDate(new \DateTime());
        //TODO: fix difference between filename in entity, and real filename

        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

        $uploadedFile->move($this->photosDir, $newFilename);
        return $image;
    }
}