<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderDetailResource;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index() {
        $order = OrderItem::all();
        return OrderDetailResource::collection(
            $order->loadMissing('menu')
        );
    }  

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer',
            'items' => 'required|array',
            'items.*.menu_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);
    
        $userId = Auth::id();
        $orderId = $request->order_id;
    
        $itemsToInsert = collect($request->items)->map(function ($item) use ($userId, $orderId) {
            return [
                'user_id' => $userId,
                'order_id' => $orderId,
                'menu_id' => $item['menu_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'status' => 'order',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();
    
        OrderItem::insert($itemsToInsert);
    
        return response()->json(['message' => 'Order items saved successfully.']);
    }

}
