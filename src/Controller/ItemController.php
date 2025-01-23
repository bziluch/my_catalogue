<?php

namespace App\Controller;

use App\Entity\AbstractEntity;
use App\Entity\Catalogue;
use App\Entity\Item;
use App\Event\ItemUpdateCatalogueEvent;
use App\Form\Filters\ItemFilterType;
use App\Form\ItemCatalogueType;
use App\Form\ItemType;
use App\Helper\ContextHolder;
use App\Repository\CatalogueRepository;
use App\Service\CatalogueService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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


    #[Route('/item/update-catalogue/{id}', name: 'item_update_catalogue')]
    public function catalogueForm(
        CatalogueService $catalogueService,
        CatalogueRepository $catalogueRepository,
        EventDispatcherInterface $eventDispatcher,
        int $id,
    ): Response
    {
        $entity = $this->getRepository()->find($id);
        if (!$entity) {
            throw new NotFoundHttpException();
        }
        $oldCatalogueId = $entity->getCatalogue()->getId();

        $form = $this->createForm(ItemCatalogueType::class, $entity);
        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            /*
             * TODO: refactor - create event changeCatalogue, and listener for it
             */
            if ($oldCatalogueId !== $entity->getCatalogue()->getId())
            {
                $eventDispatcher->dispatch(new ItemUpdateCatalogueEvent($entity, $catalogueRepository->find($oldCatalogueId)));
            }

            return $this->redirectToRoute('item_list', ['catalogueId' => $oldCatalogueId]);
        }

        return $this->render($this->getFormView(), [
            'form' => $form->createView(),
        ]);
    }

    protected function getEntityClass(): string
    {
        return Item::class;
    }

    protected function updateIndexQuery(QueryBuilder $queryBuilder, ContextHolder $contextHolder): void
    {
        $queryBuilder
            ->andWhere('e.catalogue = :catalogue')
            ->setParameter('catalogue', $contextHolder->get('catalogue'));
    }

    protected function getFormTypeClass(): string
    {
        return ItemType::class;
    }

    protected function getFilterFormType(): string
    {
        return ItemFilterType::class;
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