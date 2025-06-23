<?php

namespace App\Services;
use App\Models\Author;
use App\Repositories\Interfaces\AuthorRepository;
use Illuminate\Database\Eloquent\Collection;

class AuthorService
{
    private $authorRepository;
    /**
   1  * Create a new class instance.
     */
    public function __construct(AuthorRepository $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    public function all() : Collection
    {
        return $this->authorRepository->all();
    }

    public function show(string $id) : Author
    {
        return $this->authorRepository->find($id);
    }

    public function store(array $data) : Author
    {
        return $this->authorRepository->create($data);
    }

    public function update(string $id, array $data) : Author
    {
        return $this->authorRepository->update($data, $id);
    }

    public function delete(string $id) : bool
    {
        return $this->authorRepository->delete($id);
    }
}
