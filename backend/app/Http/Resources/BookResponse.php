<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
          'id'=>$this->id,
          'title'=>$this->title,
          'author'=>[
              'id'=>$this->author->id,
              'name'=>$this->author->name,
          ],
          'image'=>array_map(function ($image) {
              return [
                  'id'=>$image->id,
                  'url'=>$image->image_url,
              ];
          },$this->BookImage->all()),
            'category'=>array_map(function ($category) {
                return [
                    'id'=>$category->id,
                    'name'=>$category->name,
                ];
            },$this->categories->all()),
            'quantity'=>$this->quantity,
            'price'=>$this->price,
            'description'=>$this->description,
        ];
    }
}
