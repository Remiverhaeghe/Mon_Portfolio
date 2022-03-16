<?php

namespace App\Classe;

use App\Entity\Category;
use PhpParser\Node\Expr\Cast\String_;

class Search {
    
    /**
     * @var string
     */
    public $string = ''; 

    /**
     * @var Category[]
     */
    public $categories = []; 

}