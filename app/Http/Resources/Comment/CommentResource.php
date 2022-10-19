<?php

namespace App\Http\Resources\Comment;

use App\Http\Resources\Todo\TodoResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class CommentResource extends JsonResource
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
            'message' => $this->message,
            'todo_id' => $this->when($this->whenLoaded('todo') instanceof MissingValue, $this->todo_id),
            'user_id' => $this->when($this->whenLoaded('user') instanceof MissingValue, $this->user_id),
            'todo' => new TodoResource($this->whenLoaded('todo')),
            'user' => new UserResource($this->whenLoaded('user'))
        ];
    }
}
