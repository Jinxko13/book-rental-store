<?php

namespace App\Http\Controllers;

use App\Services\AuthorService;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    protected $authorService;

    public function __construct(AuthorService $authorService)
    {
        $this->authorService = $authorService;
    }

    public function index()
    {
        return response()->json([
            'data'=>$this->authorService->all()
        ],200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required'
        ]);
        return response()->json([
            'message' => 'Author created successfully.',
            'data'  => $this->authorService->store($data)
        ], 201);
    }

    public function show($id)
    {
        return response()->json($this->authorService->show($id));
    }

    public function update(Request $request, $id)
    {
        $data = $request->only([
            'name', 'date_of_birth', 'nationality', 'gender', 'category_id', 'bio'
        ]);
        return response()->json([
            'message'   => 'cap nhap thanh cong.',
            'data'      => $this->authorService->update($id, $data)
        ], 201);
    }

    public function destroy($id)
    {
        return response()->json([
            'message'   => 'xoa thanh cong.',
            'data'      => $this->authorService->destroy($id)
        ], 201);
    }
}
