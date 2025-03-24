<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{

    public function store(Request $request) {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'comment' => 'required',
        ]);

        $comment = Comment::create([
            'menu_id' => $request->menu_id,
            'comment' => $request->comment,
            'user_id' => Auth::id(),
        ]);

        return new CommentResource($comment);
    }

    public function destroy($id) {
        $comment = Comment::findOrFail($id);
        $comment->delete();
        return new CommentResource($comment);
    }
}
