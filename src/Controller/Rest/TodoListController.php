<?php


namespace App\Controller\Rest;

use App\Entity\TodoList;
use App\Form\TodoListType;
use App\Repository\TodoListRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;

/**
 * @Rest\RouteResource("List")
 */
class TodoListController extends AbstractFOSRestController implements ClassResourceInterface
{
    /**
     * @var TodoListRepository
     */
    private $todoListRepository;

    /**
     * TodoListController constructor.
     *
     * @param TodoListRepository $todoListRepository
     */
    public function __construct(TodoListRepository $todoListRepository)
    {
        $this->todoListRepository = $todoListRepository;
    }

    /**
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default", "items_count"})
     *
     * @return View
     */
    public function cgetAction(): View
    {
        $lists = $this->todoListRepository->findListJoinItems();

        return $this->view($lists, Response::HTTP_OK);
    }

    /**
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default", "items_count"})
     *
     * @param Request $request
     *
     * @return View
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postAction(Request $request): View
    {
        $list = new TodoList();
        $form = $this->createForm(TodoListType::class, $list);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->view($form, Response::HTTP_BAD_REQUEST);
        }

        $this->todoListRepository->create($list);

        $location = $request->getPathInfo().'/'.$list->getId();

        return $this->view($list, Response::HTTP_CREATED, ['Location' => $location]);
    }

    /**
     * @Rest\Get(requirements={"listId" = "\d+"})
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default", "items_count"})
     *
     * @param int $listId
     *
     * @return View
     */
    public function getAction(int $listId): View
    {
        $list = $this->todoListRepository->findListJoinItems([$listId]);

        if (!$list) {
            throw new ResourceNotFoundException('Not found');
        }

        return $this->view($list, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete(requirements={"listId" = "\d+"})
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default"})
     *
     * @param int $listId
     *
     * @return View
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteAction(int $listId): View
    {
        $list = $this->todoListRepository->findOneBy(['id' => $listId]);

        if (!$list) {
            throw new ResourceNotFoundException('Not found');
        }

        $this->todoListRepository->delete($list);

        // 204 HTTP NO CONTENT response. The object is deleted.
        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}

