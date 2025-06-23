<?php

namespace App\Http\Controllers;

use App\Services\DiscountService;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    private $discountService;

    public function __construct(DiscountService $discountService)
    {
        $this->discountService = $discountService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return response()->json($this->discountService->all());
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required',
                'description' => 'required',
                'discount_percent' => 'required',
                'valid_from' => 'required',
                'valid_to' => 'required',
            ]);
            return response()->json($this->discountService->create($data));
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /*;
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            return response()->json($this->discountService->show($id));
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $data = $request->validate([
                'name' => 'required',
                'description' => 'required',
                'discount_percent' => 'required',
                'valid_from' => 'required',
                'valid_to' => 'required',
            ]);

            return response()->json($this->discountService->update($data, $id));
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => $this->discountService->delete($id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function applyPromotions(Request $request, string $code)
    {
        try {
            $promotion = $this->discountService->findByName($code);
            if (!$promotion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã giảm giá không hợp lệ.'
                ], 400);
            }
            return response()->json([
                'success' => true,
                'promotion' => $promotion
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
