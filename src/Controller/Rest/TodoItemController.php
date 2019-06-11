<?php


namespace App\Controller\Rest;

use App\Entity\TodoItem;
use App\Entity\TodoList;
use App\Form\TodoItemType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;

/**
 * @Rest\RouteResource("Item")
 */
class TodoItemController extends AbstractFOSRestController implements ClassResourceInterface
{
    /**
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default", "items"})
     *
     * @param int $listId
     *
     * @return View
     */
    public function cgetAction(int $listId): View
    {
        $repository = $this->getTodoListRepository();
        $listItems  = $repository->findListJoinItems([$listId]);

        if (!$listItems) {
            throw new ResourceNotFoundException('Not found');
        }

        return $this->view($listItems, Response::HTTP_OK);
    }


    /**
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default"})
     *
     * @param int     $listId
     * @param Request $request
     *
     * @return View
     */
    public function postAction(int $listId, Request $request): View
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
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default", "list"})
     *
     * @param int $listId
     * @param int $itemId
     *
     * @return View
     */
    public function getAction(int $listId, int $itemId): View
    {
        $repository = $this->getTodoItemRepository();
        $item       = $repository->findItemJoinList($itemId, $listId);

        if (!$item) {
            throw new ResourceNotFoundException('Not found');
        }

        return $this->view($item, Response::HTTP_OK);
    }

    /**
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default"})
     *
     * @param int $listId
     * @param int $itemId
     *
     * @return View
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteAction(int $listId, int $itemId): View
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