<?php


namespace App\Controller\Rest;

use App\Form\TodoListType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\TodoList;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * @Route("/api", name="api_")
 */
class TodoListController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/lists")
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default"})
     */
    public function getListsAction(): View
    {
        $repository = $this->getDoctrine()->getRepository(TodoList::class);
        $lists      = $repository->findAll();
        $c = count($lists);

        return $this->view($lists, Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/lists")
     */
    public function postListsAction(Request $request): View
    {
        $list = new TodoList();
        $form = $this->createForm(TodoListType::class, $list);

        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($list);
            $em->flush();

            return $this->view(['code'=> Response::HTTP_CREATED, 'message' => 'Created'], Response::HTTP_CREATED);
        }

        return $this->view($form, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Get("/lists/{listId}")
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default", "items"})
     */
    public function getListAction(int $listId): View
    {
        $repository = $this->getDoctrine()->getRepository(TodoList::class);
        $list       = $repository->find($listId);

        if (!$list) {
            throw new ResourceNotFoundException('Not found');
        }

        return $this->view($list, Response::HTTP_OK);
    }

}

