<?php

namespace App\Controller;

use App\Entity\Catalogue;
use App\Form\CatalogueType;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CatalogueController extends AbstractAppController
{
    #[Route('/catalogue/list', name: 'catalogue_list')]
    public function index(): Response
    {
        return parent::index();
    }

    #[Route('/catalogue/new', name: 'catalogue_new')]
    #[Route('/catalogue/edit/{id}', name: 'catalogue_edit')]
    public function form(?int $id = null): Response
    {
        return parent::form($id);
    }

    protected function getIndexList(EntityRepository $entityRepository): array|Collection
    {
        return $entityRepository->findBy(['user' => $this->getUser()], ['id' => 'DESC']);
    }

    protected function getEntityClass(): string
    {
        return Catalogue::class;
    }

    protected function getFormTypeClass(): string
    {
        return CatalogueType::class;
    }

    protected function getFormView(): string
    {
        return 'catalogue/form.html.twig';
    }

    protected function getIndexView(): string
    {
        return 'catalogue/index.html.twig';
    }

    protected function getRedirectRoute(): ?string
    {
        return 'catalogue_list';
    }
}
