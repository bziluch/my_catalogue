<?php

namespace App\Controller;

use App\Entity\AbstractEntity;
use App\Form\Filters\AbstractFilterType;
use App\Helper\ContextHolder;
use App\Model\Enum\FilterTypeEnum;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AbstractAppController extends AbstractController
{
    abstract protected function getEntityClass(): string;
    abstract protected function getFormTypeClass(): string;
    abstract protected function getFormView(): string;
    abstract protected function getIndexView(): string;

    protected function getFilterFormType(): ?string
    {
        return null;
    }

    protected function getRedirect(ContextHolder $contextHolder): ?RedirectResponse {
        return null;
    }

    protected function getIndexList(EntityRepository $entityRepository, ContextHolder $contextHolder): array|Collection
    {
        return $entityRepository->findAll();
    }

    protected function updateIndexQuery(QueryBuilder $queryBuilder, ContextHolder $contextHolder): void
    {
    }

    protected function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository($this->getEntityClass());
    }

    public function __construct(
        protected readonly EntityManagerInterface $entityManager,
        protected readonly RequestStack $requestStack
    ) {
    }

    public function index(ContextHolder $contextHolder) : Response
    {
        $query = $this->getRepository()->createQueryBuilder('e');

        if ($this->getFilterFormType())
        {
            $filterForm = $this->createForm($this->getFilterFormType());
            $filterForm->handleRequest($this->requestStack->getCurrentRequest());

            if ($filterForm->isSubmitted() && $filterForm->isValid())
            {
                /** @var array<string, FilterTypeEnum> $filterTypes */
                $filterTypes = (AbstractFilterType::class)($this->getFilterFormType())::defineFilterTypes();

                foreach ($filterTypes as $filterName => $filterTypeValue)
                {
                    $expr = match ($filterTypeValue) {
                        FilterTypeEnum::Like => ':'.$filterName.' LIKE "%:'.$filterName.'%"',
                        FilterTypeEnum::Exact => ':'.$filterName.' = ":'.$filterName.'"'
                    };

                    if (null !== ($value = $filterForm->get($filterName)->getData()))

                    $query->andWhere($expr)->setParameter($filterName, $value);
                }

            }

        }

        $this->updateIndexQuery($query, $contextHolder);

        /*
         * TODO: replace entities with results from $query
         */
        $entities = $this->getIndexList($this->getRepository(), $contextHolder);

        return $this->render(
            $this->getIndexView(),
            array_merge(['entities' => $entities], $this->indexAdditionalParams($contextHolder))
        );
    }

    public function form(ContextHolder $contextHolder, ?int $id = null) : Response
    {
        if ($id) {
            $entity = $this->entityManager->getRepository($this->getEntityClass())->find($id);
            if (!$entity) {
                throw new NotFoundHttpException();
            }
        } else {
            $entity = new ($this->getEntityClass())();
        }

        $this->postGetEntity($entity, $contextHolder);

        $form = $this->createForm($this->getFormTypeClass(), $entity);
        $form->handleRequest($this->requestStack->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid())
        {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
            if (null !== ($redirect = $this->getRedirect($contextHolder))) {
                return $redirect;
            }
        }
        return $this->render($this->getFormView(), [
            'entity' => $entity,
            'form' => $form->createView()
        ]);
    }

    protected function postGetEntity(AbstractEntity $entity, ContextHolder $contextHolder) : void
    {
    }

    protected function indexAdditionalParams(ContextHolder $contextHolder): array
    {
        return [];
    }

}