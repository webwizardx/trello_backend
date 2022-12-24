<?php

namespace App\Http\Controllers\v1\Todo;

use App\Http\Controllers\Controller;
use App\Http\Middleware\ResponseMetadata;
use App\Http\Requests\Todo\StoreTodoRequest;
use App\Http\Requests\Todo\UpdateTodoRequest;
use App\Http\Resources\Todo\TodoResource;
use App\Models\Lists;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, int $id)
    {
        $perPage = $request->query('perPage') ?? 15;
        $page = $request->query('page') ?? null;
        $includes = $request->query('includes') ?? [];
        $includes = is_array($includes) ? $includes : [$includes];

        $query = Lists::findOrFail($id);
        $query->board()->whereRelation('users', 'user_id', $request->user()->id)->firstOrFail();
        $query = $query->todos();
        $query = $query->with($includes);

        if ($page) {
            $query = $query->paginate($perPage);
        } else {
            $query = $query->get();
        }

        return TodoResource::collection($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTodoRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTodoRequest $request)
    {
        try {
            DB::beginTransaction();

            $userId = $request->user()->id;
            $validated = $request->validated();
            $todo = Todo::create($validated);
            $todo->users()->attach($userId);

            DB::commit();

            return new TodoResource($todo);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $id)
    {
        $todo = Todo::findOrFail($id);
        $todo->list()->first()->board()->whereRelation('users', 'user_id', $request->user()->id)->firstOrFail();

        $includes = $request->query('includes') ?? [];
        $includes = is_array($includes) ? $includes : [$includes];

        $todo->load($includes);

        return new TodoResource($todo);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTodoRequest  $request
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTodoRequest $request, int $id)

    {
        $todo = Todo::findOrFail($id);
        $todo->list()->first()->board()->whereRelation('users', 'user_id', $request->user()->id)->firstOrFail();
        $validated = $request->validated();

        $todo->title = $validated['title'] ?? $todo->title;
        $todo->description = $validated['description'] ?? $todo->description;
        $todo->list_id = $validated['list_id'] ?? $todo->list_id;
        $todo->save();

        return new TodoResource($todo);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $id)
    {
        try {
            DB::beginTransaction();

            $todo = Todo::findOrFail($id);
            $todo->list()->first()->board()->whereRelation('users', 'user_id', $request->user()->id)->firstOrFail();

            $todo->users()->detach();
            $todo->delete();

            DB::commit();

            return response()->json([ResponseMetadata::MESSAGE => 'Todo deleted successfully', 'data' => new TodoResource($todo)]);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
