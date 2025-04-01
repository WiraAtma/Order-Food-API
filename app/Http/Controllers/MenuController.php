<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuDetailResource;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index() {
        $menu = Menu::all();
        return MenuDetailResource::collection(
            $menu->loadMissing('comments:id,user_id,menu_id,comment,created_at')
        );
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif',
            'category' => 'required',
        ]);

        $imageDefault = '';
        if($request->file('image')) {
            $formatFile = $request->file('image')->getClientOriginalExtension();
            $imageDefault = now()->timestamp . "." . $formatFile;
            $request->file('image')->storeAs('image', $imageDefault, 'public');
            $imageDefault = asset('storage/image/' . $imageDefault);
        }
        
        $menu = Menu::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imageDefault,
            'category' => $request->category
        ]);
        
        return new MenuDetailResource($menu);
    }

    public function update(Request $request, $id) {
        $validated = $request->validate([
            'name' => 'required|max:255|sometimes',
            'description' => 'required|sometimes',
            'price' => 'required|sometimes',
            'category' => 'required|sometimes',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif',
        ]);

        $menu = Menu::findOrFail($id);

        $imageDefault = $menu->image;

        if($request->file('image')) {
            if ($menu->image) {
                $oldImagePath = str_replace(asset('storage/'), '', $menu->image);
                Storage::disk('public')->delete($oldImagePath);
            }
        
            $formatFile = $request->file('image')->getClientOriginalExtension();
            $imageDefault = now()->timestamp . "." . $formatFile;
            $request->file('image')->storeAs('image', $imageDefault, 'public');
            $imageDefault = asset('storage/image/' . $imageDefault);
        }

        $menu->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imageDefault,
            'category' => $request->category
        ]);
        return new MenuDetailResource($menu); 
    }

    public function destroy($id) {
        $menu = Menu::findOrFail($id);
        $menu->delete();
        return new MenuDetailResource($menu); 
    }
}
