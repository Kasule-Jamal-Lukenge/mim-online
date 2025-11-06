<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    //View all orders of the logged-in buyer
    public function index(Request $request){
        $orders = Order::with('items.product')
                    ->where('user_id', $request->user()->id)
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json($orders);
    }

    //placing a new order
    public function store(Request $request){
         $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();
        $items = $request->items;

        $total = 0;
        $orderItems = [];

        //Calculating the total
        foreach($items as $item){
            $product = Product::find($item['product_id']);
            $subtotal = $product->price * $item['quantity'];
            $total += $subtotal;

            $orderItems[] = [
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
            ];
        }

        DB::beginTransaction();

        try{
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'Received',
                'total_price' => $total,
            ]);

            //Inserting order items
            foreach($orderItems as $item){
                $item['order_id'] = $order->id;
                OrderItem::create($item);
            }

            DB::commit();

            return response()->json([
                'message' => 'Order placed successfully.',
                'order' => $order->load('items.product')
            ], 201);
        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to place order.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Viewing single order (with its items)
    public function show($id, Request $request)
    {
        $order = Order::with('items.product')
                    ->where('user_id', $request->user()->id)
                    ->find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        return response()->json($order);
    }
}
