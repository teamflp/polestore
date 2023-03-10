<?php

namespace App\Classe;

use App\Entity\Category;

class Search
{
    /**
     * @var string|null
     */
    public ?string $string = '';

    /**
     * @var Category[]|null
     */
    public ?array $categories = [];

    /**
     * @var string|null
     */
    public ?string $productName = '';

    /**
     * @var string|null
     */
    public ?string $categoryName = '';
}

