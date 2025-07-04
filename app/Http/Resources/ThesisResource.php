<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ThesisResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->whenLoaded('author', fn() => $this->author->name),
            'university' => $this->whenLoaded('university', fn() => $this->university->name),
            'specialization' => $this->whenLoaded('specialization', fn() => $this->specialization->name),
            'degree' => $this->whenLoaded('degree', fn() => $this->degree->name),
            'year' => $this->year,
            'created_at' => $this->created_at?->format('Y-m-d'),
        ];
    }
}