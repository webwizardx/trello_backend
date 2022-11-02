<?php

namespace App\Http\Resources\Workspace;

use App\Http\Resources\Board\BoardResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class WorkspaceResource extends JsonResource
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
            'user_id' => $this->when($this->whenLoaded('user') instanceof MissingValue, $this->user_id),
            'user' => new UserResource($this->whenLoaded('user')),
            'boards' => BoardResource::collection($this->whenLoaded('boards'))
        ];
    }
}
