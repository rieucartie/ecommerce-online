<?php
namespace App\Data;

use App\Entity\Category;

class SearchData
{
    /**
     * @var int
     */
    public int $page = 1;
    /**
     * @var string
     */
    public ?string $q = '';
    /**
     * @var Category[]
     */
    public $categories = [];
    /**
     * @var null|integer
     */
    public ?int $max;
    /**
     * @var null|integer
     */
    public ?int $min;
    /**
     * @var boolean
     */
    public bool $promo = false;
}
