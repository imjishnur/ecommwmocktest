<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductService;

class ProductController extends Controller
{
    protected $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

       public function index()
    {
        $products = $this->service->getActive(); 
       
        return view('frontend.products.index', compact('products'));
    }
}
