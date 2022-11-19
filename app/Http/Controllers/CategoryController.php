<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        // ? Ambos retornan lo mismo, la diferencia que el de abajo al ser una relación, podemos utilizar el método paginate()
        // dd($category->projects->load('category')); // Con load evitamos el problema N + 1
        // dd($category->projects()->with('category')->get());
        return view('projects.index', [
            'category' => $category,
            'projects' => $category->projects()->with('category')->latest()->paginate()
        ]);
    }
}
