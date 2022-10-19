<?php

namespace App\Http\Controllers\v1\Workspace;

use App\Http\Controllers\Controller;
use App\Http\Middleware\ResponseMetadata;
use App\Http\Requests\Workspace\StoreWorkspaceRequest;
use App\Http\Requests\Workspace\UpdateWorkspaceRequest;
use App\Http\Resources\Workspace\WorkspaceResource;
use App\Models\Workspace;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->query('perPage') ?? 15;
        $page = $request->query('page') ?? null;
        $includes = $request->query('includes') ?? [];
        $includes = is_array($includes) ? $includes : [$includes];

        $query = Workspace::where('user_id', $request->user()->id);
        $query = $query->with($includes);

        if ($page) {
            $query = $query->paginate($perPage);
        } else {
            $query = $query->get();
        }

        return WorkspaceResource::collection($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreWorkspaceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWorkspaceRequest $request)
    {
        $validated = $request->validated();
        $workspace = Workspace::create($validated);

        return new WorkspaceResource($workspace);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Workspace  $workspace
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $id)
    {
        $workspace = Workspace::where(['id' => $id, 'user_id' => $request->user()->id])->firstOrFail();

        $includes = $request->query('includes') ?? [];
        $includes = is_array($includes) ? $includes : [$includes];

        $workspace->load($includes);

        return new WorkspaceResource($workspace);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateWorkspaceRequest  $request
     * @param  \App\Models\Workspace  $workspace
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateWorkspaceRequest $request, int $id)
    {
        $workspace = Workspace::where(['id' => $id, 'user_id' => $request->user()->id])->firstOrFail();
        $validated = $request->validated();

        $workspace->title = $validated['title'] ?? $workspace->title;
        $workspace->user_id = $validated['user_id'] ?? $workspace->user_id;
        $workspace->save();

        return new WorkspaceResource($workspace);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Workspace  $workspace
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $id)
    {
        $workspace = Workspace::where(['id' => $id, 'user_id' => $request->user()->id])->firstOrFail();
        $workspace->delete();

        return response()->json([ResponseMetadata::MESSAGE => 'Workspace deleted successfully', 'data' => new WorkspaceResource($workspace)]);
    }
}
