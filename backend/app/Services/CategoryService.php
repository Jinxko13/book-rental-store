<?php

namespace App\Services;
use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    private $categoryRepository;
    /**
     * Create a new class instance.
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function all() : Collection
    {
        $categories = $this->categoryRepository->withCount('books')->all();
        return $categories;
    }

    public function show(string $id) : Category
    {
        return $this->categoryRepository->find($id);
    }

    public function store(array $data) : Category
    {
        return $this->categoryRepository->create($data);
    }

    public function update(string $id, array $data) : Category
    {
        return $this->categoryRepository->update($data, $id);
    }

    public function delete(string $id) : bool
    {
        return $this->categoryRepository->delete($id);
    }
}
