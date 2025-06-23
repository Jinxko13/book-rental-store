<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'=>'required',
            'author_id'=>'required|exists:authors,id',
            'category_id'=>'required|array',
            'category_id.*'=>'exists:categories,id',
            'quantity'=>'required|numeric',
            'price'=>'required|numeric',
            'description'=>'required',
            'image' => 'nullable|array',
            'image.*' => 'file|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ];
    }
}
