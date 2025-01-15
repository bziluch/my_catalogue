<?php

namespace App\Controller;

use App\Entity\AbstractEntity;
use App\Entity\Catalogue;
use App\Entity\Item;
use App\Helper\ContextHolder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class ItemController extends AbstractAppController
{

    #[Route('/catalogue/{catalogueId}/new-item', name: 'item_new')]
    #[Route('/item/edit/{id}', name: 'item_edit')]
    public function form(
        ContextHolder $contextHolder,
        ?int $id = null,
        ?int $catalogueId = null
    ): Response {
        $contextHolder->add('catalogueId', $catalogueId);
        return parent::form($contextHolder, $id);
    }

    protected function getEntityClass(): string
    {
        return Item::class;
    }

    protected function getFormTypeClass(): string
    {
        // TODO: Implement getFormTypeClass() method.
        return ''; // temporary
    }

    protected function getFormView(): string
    {
        // TODO: Implement getFormView() method.
        return ''; // temporary
    }

    protected function getIndexView(): string
    {
        // TODO: Implement getIndexView() method.
        return ''; // temporary
    }

    /**
     * @param Item $entity
     */
    protected function postGetEntity(AbstractEntity $entity, ContextHolder $contextHolder): void
    {
        if ($entity->getId() !== null) {
            $catalogue = $entity->getCatalogue();
        } else {
            $catalogue = $this->entityManager->getRepository(Catalogue::class)->find($contextHolder->get('catalogueId'));
            $entity->setCatalogue($catalogue);
        }

        if (!$catalogue instanceof Catalogue || $catalogue->getUser() !== $this->getUser()) {
            throw new NotFoundHttpException();
        }
    }
}