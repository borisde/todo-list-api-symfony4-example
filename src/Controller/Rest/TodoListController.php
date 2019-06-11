<?php

namespace App\Controller\Rest;

use App\Entity\TodoItem;
use App\Form\TodoItemType;
use App\Form\TodoListType;
use App\Entity\TodoList;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;

/**
 * @Route("/api", name="api_")
 */
class TodoListController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/lists")
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default", "items_count"})
     *
     * @return View
     */
    public function getAllListsAction(): View
    {
        $repository = $this->getTodoListRepository();
        $lists      = $repository->findListJoinItems();

        return $this->view($lists, Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/lists")
     *
     * @param Request $request
     *
     * @return View
     */
    public function postListsAction(Request $request): View
    {
        $list = new TodoList();
        $form = $this->createForm(TodoListType::class, $list);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->view($form, Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($list);
        $em->flush();

        return $this->view(['code' => Response::HTTP_CREATED, 'message' => 'Created'], Response::HTTP_CREATED);
    }

    /**
     * @Rest\Get("/lists/{listId}")
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default", "items_count"})
     *
     * @param int $listId
     *
     * @return View
     */
    public function getListAction(int $listId): View
    {
        $repository = $this->getTodoListRepository();
        $list       = $repository->findListJoinItems([$listId]);

        if (!$list) {
            throw new ResourceNotFoundException('Not found');
        }

        return $this->view($list, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete("/lists/{listId}")
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default"})
     *
     * @param int $listId
     *
     * @return View
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function removeListAction(int $listId): View
    {
        $repository = $this->getTodoListRepository();
        $list       = $repository->findOneBy(['id' => $listId]);

        if (!$list) {
            throw new ResourceNotFoundException('Not found');
        }

        $repository->delete($list);

        // 204 HTTP NO CONTENT response. The object is deleted.
        return $this->view('Deleted', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Rest\Get("/lists/{listId}/items")
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default", "items"})
     *
     * @param int $listId
     *
     * @return View
     */
    public function getListAllItemsAction(int $listId): View
    {
        $repository = $this->getTodoListRepository();
        $listItems  = $repository->findListJoinItems([$listId]);

        if (!$listItems) {
            throw new ResourceNotFoundException('Not found');
        }

        return $this->view($listItems, Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/lists/{listId}/items")
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default"})
     */
    public function postListItemsAction(int $listId, Request $request): View
    {
        $repository = $this->getTodoListRepository();
        $list       = $repository->findOneBy(['id' => $listId]);

        if (!$list) {
            throw new ResourceNotFoundException('Not found');
        }

        $item = new TodoItem();
        $item->setList($list);

        $form = $this->createForm(TodoItemType::class, $item);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->view($form, Response::HTTP_BAD_REQUEST);
        }

        $this->getTodoItemRepository()->create($item);

        return $this->view(['code' => Response::HTTP_CREATED, 'message' => 'Created'], Response::HTTP_CREATED);
    }

    /**
     * @Rest\Get("/lists/{listId}/items/{itemId}")
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default", "list"})
     *
     * @param int $listId
     * @param int $itemId
     *
     * @return View
     */
    public function getListItemAction(int $listId, int $itemId): View
    {
        $repository = $this->getTodoItemRepository();
        $item       = $repository->findItemJoinList($itemId, $listId);

        if (!$item) {
            throw new ResourceNotFoundException('Not found');
        }

        return $this->view($item, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete("/lists/{listId}/items/{itemId}")
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default"})
     *
     * @param int $listId
     * @param int $itemId
     *
     * @return View
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function removeListItemAction(int $listId, int $itemId): View
    {
        $repository = $this->getTodoItemRepository();
        $item       = $repository->findOneBy(
            [
                'id'   => $itemId,
                'list' => $listId,
            ]
        );

        if (!$item) {
            throw new ResourceNotFoundException('Not found');
        }

        $repository->delete($item);

        // 204 HTTP NO CONTENT response. The object is deleted.
        return $this->view('Deleted', Response::HTTP_NO_CONTENT);
    }

    /**
     * @return object
     */
    protected function getTodoListRepository(): object
    {
        return $this->getDoctrine()->getRepository(TodoList::class);
    }

    /**
     * @return object
     */
    protected function getTodoItemRepository(): object
    {
        return $this->getDoctrine()->getRepository(TodoItem::class);
    }
}

