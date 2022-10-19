<?php

namespace App\Http\Resources\Todo;

use App\Http\Resources\Lists\ListsResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class TodoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'list_id' => $this->when($this->whenLoaded('list') instanceof MissingValue, $this->list_id),
            'list' => new ListsResource($this->whenLoaded('list'))
        ];
    }
}
