<?php

namespace App\Http\Resources\Lists;

use App\Http\Resources\Board\BoardResource;
use App\Http\Resources\Todo\TodoResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class ListsResource extends JsonResource
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
            'board_id' => $this->when($this->whenLoaded('board') instanceof MissingValue, $this->board_id),
            'board' => new BoardResource($this->whenLoaded('board')),
            'todos' => TodoResource::collection($this->whenLoaded('todos'))
        ];
    }
}
