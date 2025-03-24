<?php

namespace App\Http\Middleware;

use App\Models\Comment;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserComment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::id();
        $comment = Comment::findOrFail($request->id);
        if($user !== $comment->user_id) {
            return response()->json(['message' => 'Data Tidak Ditemukan'], 404);
        }
        return $next($request);
    }
}
