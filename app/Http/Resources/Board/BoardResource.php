<?php

namespace App\Http\Resources\Board;

use App\Http\Resources\User\UserResource;
use App\Http\Resources\Workspace\WorkspaceResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class BoardResource extends JsonResource
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
            'workspace_id' => $this->when($this->whenLoaded('workspace') instanceof MissingValue, $this->workspace_id),
            'users' => UserResource::collection($this->whenLoaded('users')),
            'workspace' => new WorkspaceResource($this->whenLoaded('workspace'))
        ];
    }
}
