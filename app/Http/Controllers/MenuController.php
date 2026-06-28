<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuDetailResource;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(Request $request) {

        $category = $request->query('category');

        $query = Menu::query();

        if ($category) {
            $query->where('category', $category);
        }

        $menu = $query->get();
        
        return MenuDetailResource::collection(
            $menu->loadMissing('comments:id,user_id,menu_id,comment,created_at')
        );
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp',
            'category' => 'required',
        ]);


        $imagePath = null;
        if ($request->hasFile('image')) {
            $formatFile = $request->file('image')->getClientOriginalExtension();
            $fileName = now()->timestamp . '.' . $formatFile;
            $imagePath = $request->file('image')
                                 ->storeAs('menu-images', $fileName, 'supabase');
        }

        $menu = Menu::create([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'image'       => $imagePath,
            'category'    => $request->category,
        ]);

        return new MenuDetailResource($menu);
    }

    public function update(Request $request, $id) {
        $request->validate([
            'name'        => 'sometimes|max:255',
            'description' => 'sometimes',
            'price'       => 'sometimes|numeric',
            'category'    => 'sometimes',
            'image'       => 'sometimes|image|mimes:jpeg,png,jpg,gif',
        ]);

        $menu = Menu::findOrFail($id);
        $imagePath = $menu->image;

        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('supabase')->delete($imagePath);
            }

            $formatFile = $request->file('image')->getClientOriginalExtension();
            $fileName = now()->timestamp . '.' . $formatFile;
            $imagePath = $request->file('image')
                                 ->storeAs('menu-images', $fileName, 'supabase');
        }

        $data = $request->except('image');
        $data['image'] = $imagePath;

        $menu->update($data);
        Log::info('Updated menu data:', $menu->toArray());

        return new MenuDetailResource($menu);
    }

    public function destroy($id) {
        $menu = Menu::findOrFail($id);

        // Hapus gambar dari Supabase saat destroy
        if ($menu->image) {
            Storage::disk('supabase')->delete($menu->image);
        }

        $menu->delete();
        return new MenuDetailResource($menu);
    }
}