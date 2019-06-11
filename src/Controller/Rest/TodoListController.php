<?php


namespace App\Controller\Rest;

use App\Entity\TodoList;
use App\Form\TodoListType;
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
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default", "items_count"})
     *
     * @return View
     */
    public function cgetAction(): View
    {
        $repository = $this->getTodoListRepository();
        $lists      = $repository->findListJoinItems();

        return $this->view($lists, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     *
     * @return View
     */
    public function postAction(Request $request): View
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
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default", "items_count"})
     *
     * @param int $listId
     *
     * @return View
     */
    public function getAction(int $listId): View
    {
        $repository = $this->getTodoListRepository();
        $list       = $repository->findListJoinItems([$listId]);

        if (!$list) {
            throw new ResourceNotFoundException('Not found');
        }

        return $this->view($list, Response::HTTP_OK);
    }

    /**
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
     * @return object
     */
    protected function getTodoListRepository(): object
    {
        return $this->getDoctrine()->getRepository(TodoList::class);
    }
}

