<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Comment;
use App\Order;
use App\Product;
use App\Customer;
use App\Libraries\Utilities\OrderStatus;
use App\Libraries\Utilities\CommentStatus;

use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function showByMobile($mobile)
    {
        $user = Customer::where('mobile', $mobile)->get();
        $user_array = json_decode($user, true);

        $user_id =$user_array[0]['id'];

        $customer = Customer::find($user_array[0]['id']);
        $orders = $customer->orders()->where('order_status', OrderStatus::COMMENTED)->get();
        $orders_array = json_decode($orders, true);

        $final_resp = [];
        foreach($orders as $o) {
            $resp = [];

            $o_array = json_decode($o, true);
            $order_id = $o_array['id'];
            $resp = $o_array;

            $products = $o->products()->wherePivot('commented', 1)->get();
            
            $resp_1 = array();
            for($i = 0; $i < sizeof($products); $i++) {
                $p = $products[$i];
                $resp_1[$i] = $p;

                $p_array = json_decode($p, true);
                $p_obj = Product::find($p_array['id']);

                $comments = $p_obj->comments()->where('order_id', $order_id)->get();
                $resp_1[$i]['comments'] = $comments;
            }

            $resp['products'] = $resp_1;
      
            array_push($final_resp, $resp);
        }

        return json_encode($final_resp);
    }

    public function showProductsNotCommented($orderId)
    {
        $order = Order::find($orderId);

        return $order->products()->wherePivot('commented', 0)->get();

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //select the previous commnet
        $old_comments = Comment::where('order_id', $request['order_id'])->where('product_id', $request['product_id'])->orderBy('created_at', 'DESC')->get();
        $old_comments_array = json_decode($old_comments, true);
        Log::debug($old_comments_array);

        if(sizeof($old_comments_array) > 0) {
            $old_comment_latest = $old_comments_array[0];
            $comment_prev_id = $old_comment_latest['id'];
        } else {
            $comment_prev_id = 0;
        }

        Log::debug('comment id:');
        Log::debug($request['comment_id']);
        $comment = Comment::updateOrCreate(
            [ 'order_id' => $request['order_id'],
              'product_id' => $request['product_id'],
              'comment' => $request['comment'],
              'rating' => $request['rating']
            ],
            [
              'order_id' => $request['order_id'],
              'product_id' => $request['product_id'],
              'comment' => $request['comment'],
              'comment_date' => $request['comment_date'],
              'rating' => $request['rating'],
              'comment_owner'=> $request['comment_owner'],
              'prev_id' => $comment_prev_id,
            ]
        );

        if($comment_prev_id) {
            $prev = Comment::find($comment_prev_id);
            $prev->next_id = $comment->id;
            $prev->save();
        }

        // set comment status to true if each product commented in this order
        $order = Order::find($request['order_id']);

        foreach($order->products as $product) {

            if($product['id'] == $request['product_id']) {
                $product_id = $request['product_id'];
                $quantity = $product->pivot->quantity;
                $price = $product->pivot->price;
                $order->products()->detach($product_id);
                $order->products()->attach([
                    $product_id => ['quantity' => $quantity, 'price' => $price, 'commented' => true]
                ]);

                break;
            }
        }

        // count the products which have been commented
        $order = Order::find($request['order_id']);
        $number = 0;
        foreach($order->products as $product) {
            if($product->pivot->commented) $number++;
        }

        $products = $order->products()->get();

        Log::debug('xxxxxxxxxxxxxxxxxx');
        Log::debug(count($products));
        Log::debug($number);

        if($number == count($products)) { // all products have been commented
            $order->order_status = OrderStatus::COMMENTED;
            $order->comment_status = CommentStatus::COMMENTED;
            $order->save(); 
        }

        return json_encode($comment);
     }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
