<?php


namespace App\Api\Action\Category;


use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Api\EndpointUri;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ListAll
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
        $categoriesList = $this->categoryRepository->findAll();

        $data = null;

        if (null !== $categoriesList) {
            $data = $this->serializeCategoryList($categoriesList);
        }

        $response = new JsonResponse($data, 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    private function serializeCategory(Category $category)
    {
        return array(
            'id' => $category->getId(),
            'name' => $category->getName(),
            'description' => $category->getDescription(),
            'created_at' => $category->getCreatedAt()->format('Y-m-d H:i:s'),
        );
    }

    private function serializeCategoryList (array $categoriesList):array
    {
        $data = array('categories' => array());

        foreach ($categoriesList as $category) {
            $data['categories'][] = $this->serializeCategory($category);
        }

        return $data;
    }
}