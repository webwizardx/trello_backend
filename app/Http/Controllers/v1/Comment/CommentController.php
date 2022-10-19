<?php

namespace App\Http\Controllers\v1\Comment;

use App\Http\Controllers\Controller;
use App\Http\Middleware\ResponseMetadata;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Models\Comment;
use App\Models\Todo;
use Illuminate\Http\Request;

class CommentController extends Controller
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

        $query = Todo::findOrFail($id);
        $lists = $query->list()->firstOrFail();
        $lists->board()->whereRelation('users', 'user_id', $request->user()->id)->firstOrFail();
        $query = $query->comments();
        $query = $query->with($includes);

        if ($page) {
            $query = $query->paginate($perPage);
        } else {
            $query = $query->get();
        }

        return CommentResource::collection($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCommentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCommentRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;
        $comment = Comment::create($validated);

        return new CommentResource($comment);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $id)
    {
        $comment = Comment::findOrFail($id);
        $todo = $comment->todo()->firstOrFail();
        $lists = $todo->list()->firstOrFail();
        $lists->board()
            ->whereRelation('users', 'user_id', $request->user()->id)
            ->firstOrFail();

        $includes = $request->query('includes') ?? [];
        $includes = is_array($includes) ? $includes : [$includes];

        $comment->load($includes);

        return new CommentResource($comment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCommentRequest  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCommentRequest $request, int $id)
    {
        $comment = Comment::findOrFail($id);
        $todo = $comment->todo()->firstOrFail();
        $lists = $todo->list()->firstOrFail();
        $lists->board()
            ->whereRelation('users', 'user_id', $request->user()->id)
            ->firstOrFail();
        $validated = $request->validated();

        $comment->message = $validated['message'] ?? $comment->message;
        $comment->todo_id = $validated['todo_id'] ?? $comment->todo_id;
        $comment->save();

        return new CommentResource($comment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $id)
    {
        $comment = Comment::findOrFail($id);
        $todo = $comment->todo()->firstOrFail();
        $lists = $todo->list()->firstOrFail();
        $lists->board()
            ->whereRelation('users', 'user_id', $request->user()->id)
            ->firstOrFail();

        $comment->delete();

        return response()->json([ResponseMetadata::MESSAGE => 'Comment deleted successfully', 'data' => new CommentResource($comment)]);
    }
}
