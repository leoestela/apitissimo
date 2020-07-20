<?php


namespace App\Api\Action\Category;


use App\Api\RequestManager;
use App\Repository\CategoryRepository;
use App\Api\EndpointUri;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ListAll extends RequestManager
{
    /** @var CategoryRepository */
    private $categoryRepository;


    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route(EndpointUri::URI_CATEGORY_LIST, methods={"GET"})
     * @return JsonResponse
     */
    public function __invoke():JsonResponse
    {
        $categoryCollection = $this->categoryRepository->findAll();

        $jsonContent = [];

        if(null != $categoryCollection)
        {
            $jsonContent = $this->serializeCategoryCollection($categoryCollection);
        }

        return $this->formatResponseToJson($jsonContent, JsonResponse::HTTP_OK);
    }

    private function serializeCategoryCollection (array $categoryCollection): array
    {
        $data = array('categories' => array());

        foreach ($categoryCollection as $category) {
            $data['categories'][] = $category->serialize();
        }

        return $data;
    }
}