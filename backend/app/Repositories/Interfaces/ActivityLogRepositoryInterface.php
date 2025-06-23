<?php

namespace App\Repositories\Interfaces;

interface ActivityLogRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
}