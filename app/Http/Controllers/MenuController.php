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
        return MenuDetailResource::collection($menu);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required',
        ]);

        $imageDefault = '';
        if($request->file('file')) {
            $formatFile = $request->file('file')->getClientOriginalExtension();
            $imageDefault = $this->generateRandomString() . "-" . now()->timestamp . "." . $formatFile;
            $request->file('file')->storeAs('image', $imageDefault, 'public');
        }
        $request['image'] = $imageDefault;

        $menu = Menu::create($request->all());
        return new MenuDetailResource($menu); 
    }

    public function update(Request $request, $id) {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required',
        ]);

        $imageDefault = '';
        if($request->file('file')) {
            $formatFile = $request->file('file')->getClientOriginalExtension();
            $imageDefault = $this->generateRandomString() . "-" . now()->timestamp . "." . $formatFile;
            $request->file('file')->storeAs('image', $imageDefault, 'public');
        }

        $request['image'] = $imageDefault;

        $menu = Menu::findOrFail($id);
        $menu->update($request->all());
        return new MenuDetailResource($menu); 
    }

    public function destroy($id) {
        $menu = Menu::findOrFail($id);
        $menu->delete();
        return new MenuDetailResource($menu); 
    }
    
    function generateRandomString($length = 30) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
    
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
    
        return $randomString;
    }
}
