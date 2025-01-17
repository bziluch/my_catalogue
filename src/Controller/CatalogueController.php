<?php

namespace App\Controller;

use App\Entity\Catalogue;
use App\Form\CatalogueType;
use App\Helper\ContextHolder;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CatalogueController extends AbstractAppController
{
    #[Route('/catalogue/list', name: 'catalogue_list')]
    public function index(ContextHolder $contextHolder): Response
    {
        return parent::index($contextHolder);
    }

    #[Route('/catalogue/new', name: 'catalogue_new')]
    #[Route('/catalogue/edit/{id}', name: 'catalogue_edit')]
    public function form(ContextHolder $contextHolder, ?int $id = null): Response
    {
        return parent::form($contextHolder, $id);
    }

    protected function getIndexList(EntityRepository $entityRepository, ContextHolder $contextHolder): array|Collection
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

    protected function getRedirect(ContextHolder $contextHolder): ?RedirectResponse
    {
        return $this->redirectToRoute('catalogue_list');
    }
}
