<?php


namespace App\Controller\Rest;

use App\Repository\TodoItemRepository;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;

/**
 * @Rest\RouteResource("Search", pluralize=false)
 */
class SearchController extends AbstractFOSRestController
{
    /**
     * @var TodoItemRepository
     */
    private $todoItemRepository;

    /**
     * SearchController constructor.
     *
     * @param TodoItemRepository $todoItemRepository
     */
    public function __construct(TodoItemRepository $todoItemRepository)
    {
        $this->todoItemRepository = $todoItemRepository;
    }

    /**
     * @Rest\QueryParam(name="query", requirements="[A-Za-z0-9\s]+", strict=true)
     * @Rest\View(populateDefaultVars=false, serializerGroups={"Default", "list"})
     *
     * @param string $query
     *
     * @return View
     */
    public function getItemsAction(string $query): View
    {
        if (empty($query)) {
            $result = [];
        } else {
            $result = $this->todoItemRepository->searchItems($query);
        }

        $cnt = count($result);

        return $this->view(['total_count' => $cnt, 'items' => $result], Response::HTTP_OK);
    }
}

