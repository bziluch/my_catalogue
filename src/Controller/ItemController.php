<?php

namespace App\Controller;

use App\Entity\AbstractEntity;
use App\Entity\Catalogue;
use App\Entity\Item;
use App\Form\ItemType;
use App\Helper\ContextHolder;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class ItemController extends AbstractAppController
{

    #[Route('/catalogue/view/{catalogueId}', name: 'item_list')]
    public function index(ContextHolder $contextHolder, int $catalogueId = 0): Response
    {
        $contextHolder->add('catalogue', $this->entityManager->getRepository(Catalogue::class)->find($catalogueId));

        return parent::index($contextHolder);
    }

    #[Route('/catalogue/{catalogueId}/new-item', name: 'item_new')]
    #[Route('/item/edit/{id}', name: 'item_edit')]
    public function form(
        ContextHolder $contextHolder,
        ?int $id = null,
        int $catalogueId = 0
    ): Response {
        $contextHolder->add('catalogue', $this->entityManager->getRepository(Catalogue::class)->find($catalogueId));
        return parent::form($contextHolder, $id);
    }

    protected function getEntityClass(): string
    {
        return Item::class;
    }

    protected function getIndexList(EntityRepository $entityRepository, ContextHolder $contextHolder): array|Collection
    {
        return $this->getRepository()->findBy(['catalogue' => $contextHolder->get('catalogue')]);
    }

    protected function getFormTypeClass(): string
    {
        return ItemType::class;
    }

    protected function getFormView(): string
    {
        return 'item/form.html.twig';
    }

    protected function getIndexView(): string
    {
        return 'item/index.html.twig';
    }

    protected function getRedirect(ContextHolder $contextHolder): ?RedirectResponse
    {
        return $this->redirectToRoute('item_list', ['catalogueId' => $contextHolder->get('catalogue')->getId()]);
    }

    /**
     * @param Item $entity
     */
    protected function postGetEntity(AbstractEntity $entity, ContextHolder $contextHolder): void
    {
        if ($entity->getId() !== null) {
            $contextHolder->add('catalogue', $entity->getCatalogue());

        } else {
            $catalogue = $contextHolder->get('catalogue');

            if (!$catalogue instanceof Catalogue || $catalogue->getUser() !== $this->getUser()) {
                throw new NotFoundHttpException();
            }

            $entity->setCatalogue($catalogue);
        }
    }

    protected function indexAdditionalParams(ContextHolder $contextHolder): array
    {
        return ['catalogue' => $contextHolder->get('catalogue')];
    }
}