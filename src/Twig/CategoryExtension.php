<?php

namespace App\Twig;

use App\Repository\CategorieRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CategoryExtension extends AbstractExtension
{
    public function __construct(private CategorieRepository $categoryRepository)
    {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('all_categories', [$this, 'getCategories']),
        ];
    }

    public function getCategories(): array
    {
        return $this->categoryRepository->findAll();
    }
}