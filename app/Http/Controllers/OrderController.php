<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderDetailResource;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index() {
        $order = Order::all();
        return OrderDetailResource::collection(
            $order->loadMissing('orderItem:id,order_id,menu_id,quantity,price')
        );
    }  

    public function store(Request $request) {
        $request->validate([
            'items' => 'required|array',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required',
        ]);

        $totalPrice = 0;

        $order = Order::create([
            'user_id' => Auth::user()->id,
            'total_price' => 0,
            'status' => 'pending',
        ]);

        foreach($request->items as $item) {
            $menu = Menu::findOrFail($item['menu_id']);
            $subTotal = $menu->price * $item['quantity'];
            $totalPrice += $subTotal;

            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $menu->id,
                'quantity' => $item['quantity'],
                'price' => $menu->price,
            ]);
        }

        $order->update(['total_price' => $totalPrice]);
        return response()->json(['message' => 'Order created successfully', 'order' => $order]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);
    
        $order = Order::findOrFail($id);
    
        $order->orderItem()->delete(); 
    
        $totalPrice = collect($request->items)->sum(function ($item) use ($order) {
            $menu = Menu::find($item['menu_id']);
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $menu->id,
                'quantity' => $item['quantity'],
                'price' => $menu->price,
            ]);
            return $menu->price * $item['quantity'];
        });

        $order->update(['total_price' => $totalPrice]);
    
        return response()->json([
            'message' => 'Order updated successfully',
            'order' => $order->load('orderItem')
        ]);
    }
    
    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        $order->orderItem()->delete();

        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }
}
