<?php

namespace App\Http\Controllers\v1\Board;

use App\Http\Controllers\Controller;
use App\Http\Middleware\ResponseMetadata;
use App\Http\Requests\Board\StoreBoardRequest;
use App\Http\Requests\Board\UpdateBoardRequest;
use App\Http\Resources\Board\BoardResource;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $perPage = $request->query('perPage') ?? 15;
        $page = $request->query('page') ?? null;
        $includes = $request->query('includes') ?? [];
        $includes = is_array($includes) ? $includes : [$includes];

        $query = $user->boards();
        $query = $query->with($includes);

        if ($page) {
            $query = $query->paginate($perPage);
        } else {
            $query = $query->get();
        }

        return BoardResource::collection($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBoardRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBoardRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $board = Board::create($validated);
            $board->users()->attach($request->user()->id);

            DB::commit();

            return new BoardResource($board, 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $id)
    {
        $board = Board::where(['id' => $id])->whereRelation('users', 'user_id', $request->user()->id)->firstOrFail();;

        $includes = $request->query('includes') ?? [];
        $includes = is_array($includes) ? $includes : [$includes];

        $board->load($includes);

        return new BoardResource($board);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBoardRequest  $request
     * @param  \App\Models\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBoardRequest $request, int $id)
    {
        $board = Board::where(['id' => $id])->whereRelation('users', 'user_id', $request->user()->id)->firstOrFail();
        $validated = $request->validated();

        $board->title = $validated['title'] ?? $board->title;
        $board->workspace_id = $validated['workspace_id'] ?? $board->workspace_id;
        $board->save();

        return new BoardResource($board);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Board  $board
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $id)
    {
        try {
            DB::beginTransaction();

            $board = Board::where(['id' => $id])->whereRelation('users', 'user_id', $request->user()->id)->firstOrFail();
            $board->users()->detach();
            $board->delete();

            DB::commit();

            return response()->json([ResponseMetadata::MESSAGE => 'Board deleted successfully', 'data' => new BoardResource($board)]);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
