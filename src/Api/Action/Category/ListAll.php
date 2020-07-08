<?php


namespace App\Api\Action\Category;


use App\Api\RequestManager;
use App\Api\Serializer;
use App\Repository\CategoryRepository;
use App\Api\EndpointUri;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ListAll extends RequestManager
{
    /** @var CategoryRepository */
    private $categoryRepository;

    /** @var Serializer */
    private $serializer;

    public function __construct(CategoryRepository $categoryRepository, Serializer $serializer)
    {
        $this->categoryRepository = $categoryRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Route(EndpointUri::URI_CATEGORY_LIST, methods={"GET"})
     * @return JsonResponse
     */
    public function __invoke():JsonResponse
    {
        $categoryCollection = $this->categoryRepository->findAll();

        $jsonContent = (null !== $categoryCollection)
            ? $this->serializer->serializeCategoryCollection($categoryCollection) : null;

        $jsonContent = (null == $jsonContent) ? $this->getJsonForEmptyData(200) : $jsonContent;

        return $this->getJsonResponse($jsonContent, 200);
    }
}