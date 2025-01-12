<?php

namespace App\Controller;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AbstractAppController extends AbstractController
{
    abstract protected function getEntityClass(): string;
    abstract protected function getFormTypeClass(): string;
    abstract protected function getFormView(): string;
    abstract protected function getIndexView(): string;

    protected function getRedirectRoute(): ?string {
        return null;
    }

    protected function getIndexList(): array|Collection
    {
        return $this->entityManager->getRepository($this->getEntityClass())->findAll();
    }

    public function __construct(
        protected readonly EntityManagerInterface $entityManager
    ) {
    }

    public function index() : Response
    {
        $entities = $this->getIndexList();
        return $this->render($this->getIndexView(), [
            'entities' => $entities
        ]);
    }

    public function form(Request $request, ?int $id = null) : Response
    {
        if ($id) {
            $entity = $this->entityManager->getRepository($this->getEntityClass())->find($id);
            if (!$entity) {
                throw new NotFoundHttpException();
            }
        } else {
            $entity = new ($this->getEntityClass())();
        }
        $form = $this->createForm($this->getFormTypeClass(), $entity);
        $form->handleRequest($request);
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

}