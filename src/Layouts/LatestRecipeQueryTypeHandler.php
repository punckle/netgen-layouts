<?php

namespace App\Layouts;

use App\Repository\RecipeRepository;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterType\TextType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('netgen_layouts.query_type_handler', ['type' => 'latest_recipes'])]
class LatestRecipeQueryTypeHandler implements QueryTypeHandlerInterface
{
    public function __construct(private RecipeRepository $recipeRepository)
    {
    }

    public function buildParameters(ParameterBuilderInterface $builder): void
    {
        $builder->add('term', TextType::class);
    }

    public function getValues(Query $query, int $offset = 0, ?int $limit = null): iterable
    {
        return $this->recipeRepository->createQueryBuilderOrderedByNewest()
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getCount(Query $query): int
    {
        return $this->recipeRepository->createQueryBuilderOrderedByNewest()
            ->select('COUNT(recipe.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function isContextual(Query $query): bool
    {
        return false;
    }
}