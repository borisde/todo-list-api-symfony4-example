<?php


namespace App\Controller;

use App\Form\TodoListType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\TodoList;
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
     */
    public function getListsAction(): View
    {
        $repository = $this->getDoctrine()->getRepository(TodoList::class);
        $lists      = $repository->findall();

        return $this->view($lists, Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/lists")
     */
    public function postListAction(Request $request): View
    {
        $list = new TodoList();
        $form = $this->createForm(TodoListType::class, $list);

        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($list);
            $em->flush();

            return $this->view(['status' => 'ok'], Response::HTTP_CREATED);
        }

        return $this->view($form, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Get("/lists/{listId}")
     */
    public function getListAction(int $listId): View
    {
        $repository = $this->getDoctrine()->getRepository(TodoList::class);
        $list       = $repository->find($listId);
        if (!$list) {
            return $this->view(['status' => 'error', 'message' => 'Not found'], Response::HTTP_NOT_FOUND);

        }

        return $this->view($list, Response::HTTP_OK);
    }
}

