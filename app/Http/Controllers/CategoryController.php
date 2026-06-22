<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('transactions')
            ->orderBy('name')
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'icon' => $c->icon,
                'transaction_count' => $c->transactions_count,
                'created_at' => $c->created_at->toIso8601String(),
            ]);

        return Inertia::render('Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function store(StoreCategoryRequest $request)
    {
        Category::create($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category created.');
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category deleted.');
    }
}
