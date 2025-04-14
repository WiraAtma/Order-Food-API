<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderDetailResource;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
    
        $orderItems = OrderItem::with('menu')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    
        $groupedOrders = $orderItems->groupBy('order_id')->map(function ($items, $orderId) {
            return [
                'order_id' => $orderId,
                'user_id' => $items[0]->user_id,
                'status' => $items[0]->status,
                'created_at' => Carbon::parse($items[0]->created_at)->locale('id')->translatedFormat('d F Y H:i:s'),
                'updated_at' => Carbon::parse($items[0]->updated_at)->locale('id')->translatedFormat('d F Y H:i:s'),
                'items' => $items->map(function ($item) {
                    return [
                        'menu_id' => $item->menu_id,
                        'menu_name' => $item->menu->name ?? 'Unknown',
                        'image' => $item->menu->image ?? 'Unknown',
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ];
                })->values()
            ];
        })->values();
    
        return response()->json($groupedOrders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.menu_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $userId = Auth::id();
        $orderId = random_int(1000000000, 9999999999);
    
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
