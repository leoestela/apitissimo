<?php


namespace App\Api;


use App\Entity\BudgetRequest;
use App\Entity\Category;
use App\Entity\User;

class Serializer
{
    public function serializeCategory(Category $category): array
    {
        return array(
            'id' => $category->getId(),
            'name' => $category->getName(),
            'description' => $category->getDescription(),
            'created_at' => $category->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $category->getCreatedAt()->format('Y-m-d H:i:s')
        );
    }

    public function serializeCategoryCollection (array $categoryCollection): array
    {
        $data = array('categories' => array());

        foreach ($categoryCollection as $category) {
            $data['categories'][] = $this->serializeCategory($category);
        }

        return $data;
    }

    public function serializeUser(User $user): array
    {
        return array(
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'phone' => $user->getPhone(),
            'address' => $user->getAddress(),
            'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $user->getCreatedAt()->format('Y-m-d H:i:s')
        );
    }

    public function serializeBudgetRequest(BudgetRequest $budgetRequest)
    {
        return array(
            'id' => $budgetRequest->getId(),
            'title' => $budgetRequest->getTitle(),
            'description' => $budgetRequest->getDescription(),
            'category' => (null != $budgetRequest->getCategory())
                ? $this->serializeCategory($budgetRequest->getCategory()) : null,
            'user' => $this->serializeUser($budgetRequest->getUser()),
            'status' => $budgetRequest->getStatus(),
            'created_at' => $budgetRequest->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $budgetRequest->getUpdatedAt()->format('Y-m-d H:i:s')
        );
    }

    public function serializeBudgetRequestCollection (array $budgetRequestCollection): array
    {
        $data = array('budget_requests' => array());

        foreach ($budgetRequestCollection as $budgetRequest) {
            $data['budget_requests'][] = $this->serializeBudgetRequest($budgetRequest);
        }

        return $data;
    }
}