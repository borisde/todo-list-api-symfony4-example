<?php


namespace App\Controller\Rest;

use App\Entity\TodoItem;
use App\Repository\TodoItemRepository;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @Rest\RouteResource("Search", pluralize=false)
 * @SWG\Tag(name="Search")
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
     * @SWG\Parameter(
     *     name="query",
     *     type="string",*
     *     in="query",
     *     description="Search string"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Json array with collection of Items",
     *     @SWG\Schema(
     *           type="array",
     *           @Model(type=TodoItem::class, groups={"Default", "list"})
     *     )
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Bad Request",
     *     @SWG\Schema(ref="#definitions/ErrorBadRequest")
     * )
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

        return $this->view($result, Response::HTTP_OK);
    }
}

