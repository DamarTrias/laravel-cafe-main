<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;

class RecipeController extends Controller
{
    public function index()
    {
        $categories = Category::with(['products.ingredients'])->get();
        return view('admin.recipes.index', compact('categories'));
    }
}
