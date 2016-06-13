<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Illuminate\Http\Request;

use App\Models\Thread as Thread;
use App\Models\Comment as Comment;
use App\Models\Rating as Rating;
use App\User;

// Get or create a thread
$app->get('api/v1/thread/{id}', ['middleware' => 'auth', function (Request $request, $id) {
    $user = $request->user();
    $threadExists = Thread::find($id);

    if (!$threadExists) {
        $thread = new Thread;
        $thread->user_id = $user->id;
        $thread->save();
    } else {
        $thread = Thread::with(['comments' => function($query)
        {
            $query->with(['user' => function($query)
            {
                $query->select('id', 'name');
            }])
            ->with(['rating' => function($query)
            {
                $query->where('user_id', $user->id);
            }])
            ->where('latest_revision', true)
            ->where('moderated', '<>', true);
        }])
        ->where('id', $id)
        ->first();
    }
    return response()->json($thread, 200);
}]);

// Create a comment, or update a comment and archive the old version
$app->post('api/v1/comment', ['middleware' => 'auth', function (Request $request) {
    $user = $request->user();

    $this->validate($request, [
        'thread_id' => 'required',
        'body' => 'required'
    ]);

    $comment_id = $request->input('comment_id');
    $comment = new Comment;

    if (!empty($comment_id)) {
        $oldComment = Comment::find($comment_id)->first();
        if (empty($oldComment->revision_id)) {
            $oldComment->revision_id = $oldComment->id;
            $oldComment->latest_revision = false;
        }
        $comment->revision_id = $oldComment->revision_id;
    }

    $comment->user_id = $user->id;
    $comment->thread_id = $request->input('thread_id');
    $parent_id = $request->input('parent_id');
    $comment->parent_id = !empty($parent_id) ? $parent_id : null;
    $comment->body = $request->input('body');
    $comment->save();
    return response()->json($comment, 200);
}]);

// Moderate a comment (It's a GET request because there's no POST data needed)
$app->get('api/v1/comment/{commentId}/moderate', ['middleware' => 'auth', function (Request $request, $commentId) {
    $user = $request->user();
    $comment = Comment::find($commentId);
    if ($user->moderator) {
        $comment->moderated = true;
        $comment->save();
    }
    return response()->json($comment, 200);
}]);

// Add or update a rating on a comment, and then update the average rating
$app->post('api/v1/comment/{commentId}/rate/{ratingValue}', ['middleware' => 'auth', function (Request $request, $commentId, $ratingValue) {
    $user = $request->user();
    $rating_id = $request->input('rating_id');
    if (empty($rating_id)) {
        $rating = new Rating;
        $rating->comment_id = $commentId;
    } else {
        $rating = Rating::find($rating_id);
    }
    $rating->user_id = $user->id;
    $rating->value = $ratingValue;
    $rating->save();

    $averageRating = Rating::where('comment_id', $commentId)->avg('value');
    $comment = Comment::find($commentId);
    $comment->average_rating = $averageRating;
    $comment->save();
    return response()->json($comment, 200);
}]);

// Get all comments for a given user
$app->get('api/v1/comments/{userId}', ['middleware' => 'auth', function (Request $request, $userId) {
    $user = $request->user();
    $comments = Comment::where('user_id', $userId)->get();
    return response()->json($comments, 200);
}]);