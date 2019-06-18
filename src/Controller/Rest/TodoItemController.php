<?php


namespace App\Controller\Rest;

use App\Entity\TodoItem;
use App\Form\TodoItemType;
use App\Repository\TodoItemRepository;
use App\Repository\TodoListRepository;
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
     * @var TodoListRepository
     */
    private $todoListRepository;

    /**
     * @var TodoItemRepository
     */
    private $todoItemRepository;

    /**
     * TodoItemController constructor.
     *
     * @param TodoListRepository $todoListRepository
     * @param TodoItemRepository $todoItemRepository
     */
    public function __construct(TodoListRepository $todoListRepository, TodoItemRepository $todoItemRepository)
    {
        $this->todoListRepository = $todoListRepository;
        $this->todoItemRepository = $todoItemRepository;
    }

    /**
     * @Rest\Get(requirements={"listId" = "\d+"})
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default", "items"})
     *
     * @param int $listId
     *
     * @return View
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function cgetAction(int $listId): View
    {
        $listItems = $this->todoListRepository->findListJoinItems($listId);

        if (!$listItems) {
            throw new ResourceNotFoundException('Not found');
        }

        return $this->view($listItems, Response::HTTP_OK);
    }

    /**
     * @Rest\Post(requirements={"listId" = "\d+"})
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default"})
     *
     * @param int     $listId
     * @param Request $request
     *
     * @return View
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postAction(int $listId, Request $request): View
    {
        $list = $this->todoListRepository->findOneBy(['id' => $listId]);

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

        $this->todoItemRepository->create($item);

        $location = $request->getPathInfo().'/'.$item->getId();

        return $this->view($item, Response::HTTP_CREATED, ['Location' => $location]);
    }

    /**
     * @Rest\Get(requirements={"listId" = "\d+", "itemId" = "\d+"})
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default", "list"})
     *
     * @param int $listId
     * @param int $itemId
     *
     * @return View
     */
    public function getAction(int $listId, int $itemId): View
    {
        $item = $this->todoItemRepository->findOneBy(['id' => $itemId, 'list' => $listId]);

        if (!$item) {
            throw new ResourceNotFoundException('Not found');
        }

        return $this->view($item, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete(requirements={"listId" = "\d+", "itemId" = "\d+"})
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
        $item = $this->todoItemRepository->findOneBy(
            [
                'id'   => $itemId,
                'list' => $listId,
            ]
        );

        if (!$item) {
            throw new ResourceNotFoundException('Not found');
        }

        $this->todoItemRepository->delete($item);

        // 204 HTTP NO CONTENT response. The object is deleted.
        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}

