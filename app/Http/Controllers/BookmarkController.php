<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookmarkResource;
use App\Models\Bookmark;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    public function index() {
        $bookmarks = Bookmark::with('menu:id,name,price,description,image')
            ->where('user_id', Auth::id())
            ->get();

        return BookmarkResource::collection(
            $bookmarks
        );
    }

    public function store($menu_id) {
        $bookmark = Bookmark::create([
            'user_id' => Auth::id(),
            'menu_id' => $menu_id,
        ]);

        return new BookmarkResource($bookmark);
    }

    public function destroy($id) {
        $bookmark = Bookmark::where('menu_id', $id)->where('user_id', Auth::id())->first();
        $bookmark->delete();
        return new BookmarkResource($bookmark);
    }
}
