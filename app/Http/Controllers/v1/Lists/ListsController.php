<?php

namespace App\Http\Controllers\v1\Lists;

use App\Http\Controllers\Controller;
use App\Http\Middleware\ResponseMetadata;
use App\Http\Requests\Lists\StoreListsRequest;
use App\Http\Requests\Lists\UpdateListsRequest;
use App\Http\Resources\Lists\ListsResource;
use App\Models\Lists;
use Illuminate\Http\Request;

class ListsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, int $id)
    {
        $user = $request->user();
        $perPage = $request->query('perPage') ?? 15;
        $page = $request->query('page') ?? null;
        $includes = $request->query('includes') ?? [];
        $includes = is_array($includes) ? $includes : [$includes];

        $query = $user->boards()->findOrFail($id);
        $query = $query->lists();
        $query = $query->with($includes);

        if ($page) {
            $query = $query->paginate($perPage);
        } else {
            $query = $query->get();
        }

        return ListsResource::collection($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreListsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreListsRequest $request)
    {
        $validated = $request->validated();
        $lists = Lists::create($validated);

        return new ListsResource($lists);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Lists  $lists
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $id)
    {
        $lists = Lists::findOrFail($id);
        $lists->board()
            ->whereRelation('users', 'user_id', $request->user()->id)
            ->firstOrFail();

        $includes = $request->query('includes') ?? [];
        $includes = is_array($includes) ? $includes : [$includes];

        $lists->load($includes);

        return new ListsResource($lists);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateListsRequest  $request
     * @param  \App\Models\Lists  $lists
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateListsRequest $request, int $id)
    {
        $lists = Lists::findOrFail($id);
        $lists->board()
            ->whereRelation('users', 'user_id', $request->user()->id)
            ->firstOrFail();
        $validated = $request->validated();

        $lists->title = $validated['title'] ?? $lists->title;
        $lists->board_id = $validated['board_id'] ?? $lists->board_id;
        $lists->save();

        return new ListsResource($lists);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Lists  $lists
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $id)
    {
        $lists = Lists::findOrFail($id);
        $lists->board()
            ->whereRelation('users', 'user_id', $request->user()->id)
            ->firstOrFail();

        $lists->delete();

        return response()->json([ResponseMetadata::MESSAGE => 'List deleted successfully', 'data' => new ListsResource($lists)]);
    }
}
