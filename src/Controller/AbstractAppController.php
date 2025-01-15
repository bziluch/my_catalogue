<?php

namespace App\Controller;

use App\Entity\AbstractEntity;
use App\Helper\ContextHolder;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AbstractAppController extends AbstractController
{
    abstract protected function getEntityClass(): string;
    abstract protected function getFormTypeClass(): string;
    abstract protected function getFormView(): string;
    abstract protected function getIndexView(): string;

    protected function getDetailsView(): string {
        return '';
    }

    protected function getRedirectRoute(): ?string {
        return null;
    }

    protected function getIndexList(EntityRepository $entityRepository): array|Collection
    {
        return $entityRepository->findAll();
    }

    public function __construct(
        protected readonly EntityManagerInterface $entityManager,
        protected readonly RequestStack $requestStack
    ) {
    }

    public function index() : Response
    {
        $repository = $this->entityManager->getRepository($this->getEntityClass());
        $entities = $this->getIndexList($repository);

        return $this->render($this->getIndexView(), [
            'entities' => $entities
        ]);
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
            if ($this->getRedirectRoute()) {
                return $this->redirectToRoute($this->getRedirectRoute());
            }
        }
        return $this->render($this->getFormView(), [
            'entity' => $entity,
            'form' => $form->createView()
        ]);
    }

    public function view(int $id) : Response
    {
        $entity = $this->entityManager->getRepository($this->getEntityClass())->find($id);
        if (!$entity) {
            throw new NotFoundHttpException();
        }

        return $this->render($this->getDetailsView(), [
            'entity' => $entity
        ]);
    }

    protected function postGetEntity(AbstractEntity $entity, ContextHolder $contextHolder) : void
    {
    }

}