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
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @Rest\RouteResource("List")
 * @SWG\Tag(name="Lists")
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
     * @SWG\Response(
     *     response=200,
     *     description="Json array with a collection of Lists",
     *     @SWG\Schema(
     *           type="array",
     *           @Model(type=TodoList::class, groups={"Default", "items_count"})
     *     )
     * )
     *
     * @return View
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     */
    public function cgetAction(): View
    {
        $lists = $this->todoListRepository->findListJoinItems();

        return $this->view($lists, Response::HTTP_OK);
    }

    /**
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default", "items_count"})
     *
     * @SWG\Parameter(
     *     name="title",
     *     in="body",
     *     @SWG\Schema(ref=@Model(type=TodoListType::class))
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Json object with created List",
     *     @Model(type=TodoList::class, groups={"Default", "items_count"})
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Bad Request",
     *     @SWG\Schema(ref="#definitions/ErrorBadRequest")
     * )
     *
     * @param Request $request
     *
     * @return View
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     */
    public function postAction(Request $request): View
    {
        $list = new TodoList();
        $form = $this->createForm(TodoListType::class, $list);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->view($form, Response::HTTP_BAD_REQUEST);
        }

        $this->todoListRepository->save($list);

        $location = $request->getPathInfo().'/'.$list->getId();

        return $this->view($list, Response::HTTP_CREATED, ['Location' => $location]);
    }

    /**
     * @Rest\Get(requirements={"listId" = "\d+"})
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default", "items_count"})
     *
     * @SWG\Parameter(
     *     name="listId",
     *     type="integer",*
     *     in="path",
     *     description="List id"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Json with List object",
     *     @Model(type=TodoList::class, groups={"Default", "items_count"})
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not found",
     *     @SWG\Schema(ref="#definitions/ErrorNotFound")
     * )
     *
     * @param int $listId
     *
     * @return View
     *
     */
    public function getAction(int $listId): View
    {
        $list = $this->todoListRepository->findOneBy(['id' => $listId]);

        if (!$list) {
            throw new ResourceNotFoundException('Not found');
        }

        return $this->view($list, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete(requirements={"listId" = "\d+"})
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default"})
     *
     * @SWG\Parameter(
     *     name="listId",
     *     type="integer",*
     *     in="path",
     *     description="List id"
     * )
     * @SWG\Response(
     *     response=204,
     *     description="",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not found",
     *     @SWG\Schema(ref="#definitions/ErrorNotFound")
     * )
     *
     * @param int $listId
     *
     * @return View
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
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

    /**
     * @Rest\Put(requirements={"listId" = "\d+"})
     *
     * @SWG\Parameter(
     *     name="listId",
     *     type="integer",*
     *     in="path",
     *     description="List id"
     * )
     * @SWG\Parameter(
     *     name="title",
     *     in="body",
     *     @SWG\Schema(ref=@Model(type=TodoListType::class))
     * )
     * @SWG\Response(
     *     response=200,
     *     description="",
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Bad Request",
     *     @SWG\Schema(ref="#definitions/ErrorBadRequest")
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not found",
     *     @SWG\Schema(ref="#definitions/ErrorNotFound")
     * )
     *
     * @param int     $listId
     * @param Request $request
     *
     * @return View
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function putAction(int $listId, Request $request): View
    {
        $list = $this->todoListRepository->findOneBy(['id' => $listId]);

        if (!$list) {
            throw new ResourceNotFoundException('Not found');
        }

        $form = $this->createForm(TodoListType::class, $list);

        $form->submit($request->request->all(), false);
        if (!$form->isValid()) {
            return $this->view($form, Response::HTTP_BAD_REQUEST);
        }

        $this->todoListRepository->save($list);

        // 200 HTTP OK response.
        return $this->view(null, Response::HTTP_OK);
    }
}

