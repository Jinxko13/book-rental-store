<?php

namespace App\Repositories\Eloquent;

use App\Models\Author;
use App\Repositories\Interfaces\AuthorRepository;

class AuthorRepositoryEloquent implements AuthorRepository
{
    protected $author;

    public function __construct(Author $author)
    {
        $this->author = $author;
    }

    public function all()
    {
        return $this->author->all();
    }

    public function find($id)
    {
        return $this->author->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->author->create($data);
    }

    public function update($id, array $data)
    {
        $author = $this->find($id);
        $author->update($data);
        return $author;
    }

    public function delete($id)
    {
        return $this->author->destroy($id);
    }
}
