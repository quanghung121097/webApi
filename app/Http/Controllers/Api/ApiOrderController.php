<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\CustomerShippingAddress;
use App\Models\ShippingAddress;
use App\Models\Payment;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use  Auth;

class ApiOrderController extends Controller
{
    public function __construct()
    {
        
    }

    //Khách hàng
    /*đặt hàng*/
	public function getAdd(){
		if(!session()->has('cart')) return response(['success' => true, 'message' => 'Chưa có sản phẩm nào trong giỏ']);
		$listCustomerShippingAddress = CustomerShippingAddress::where('customer_id',Auth::guard('account_customer')->user()->person->customer->id)->get();
        return response(['success' => true, 'data' => $listCustomerShippingAddress]);
	}

	public function postAddOrder(Request $request){
		if(!$request->intShippingAddress) return response(['success' => true, 'message' => 'Bạn chưa có địa chỉ giao hàng']);
		$customerShippingAddress = CustomerShippingAddress::find($request->intShippingAddress);
		$shippingAddress = new ShippingAddress;
		$shippingAddress->recipient_name = $customerShippingAddress->recipient_name;
		$shippingAddress->recipient_phone = $customerShippingAddress->recipient_phone;
		$shippingAddress->province  = $customerShippingAddress->province ;
		$shippingAddress->district  = $customerShippingAddress->district ;
		$shippingAddress->wards  = $customerShippingAddress->wards ;
		$shippingAddress->address_detail  = $customerShippingAddress->address_detail ;
		$shippingAddress->save();
		#payment
		$payment = new Payment;
		$payment->method = ($request->intPayment==0)?'COD':'Khác'; 
		$payment->save();
		#	
		if(session('cart')){
			$cart = session('cart');
			$order = new Order;
			$order->shipping_address_id = $shippingAddress->id;
			$order->payment_id = $payment->id;
			$order->customer_id = Auth::guard('account_customer')->user()->person->customer->id;
			$order->total_quantity = $cart->getTotalQuantity();
			$order->grand_total = $cart->getGrandTotal();
			$order->note = $request->stringNote;
			$order->save();

			$listCartItem = $cart->getListCartItem();
			foreach ($listCartItem as $item) {
				# code...
				$review = new Review;
				$review->save();
				#
				$orderItem = new OrderItem;
				$orderItem->order_id = $order->id;
				$orderItem->product_id = $item->getProduct()->id;
				$orderItem->review_id = $review->id;
				$orderItem->price_sell = $item->getPriceSell(); 
				$orderItem->quantity = $item->getQuantity();
				$orderItem->sub_total = $item->getSubTotal();
				$orderItem->save();
			}
			session()->forget('cart');
            return response(['success' => true, 'message' => 'Bạn chưa có địa chỉ giao hàng']);
		}
		
	}
	/*xem lịch sử đặt hàng*/
    public function getOrderHistory(){
    	$listOrder = Order::where('customer_id',Auth::guard('account_customer')->user()->person->customer->id)->get();
        return response(['success' => true, 'data' => $listOrder]);
    }
    /*chỉ tiết đơn*/ 
    public function getDetailOrder($id){
    	$order = Order::find($id);
        return response(['success' => true, 'data' => $order]);
    }
    /*hủy đơn nếu chưa xử lý*/
    public function getCancelOrder($id){
    	$order = Order::find($id);
    	if($order->status=='Chờ xử lý')
    	$order->status = 'Hủy';
    	$order->save();
        return response(['success' => true, 'message' => 'Đơn đặt hàng đã bị hủy']);
  
    }
	/*back end*/
	public function getList(Request $request){
		$listOrder = Order::paginate($request->limit ?? 10);
        return response(['success' => true, 'data' => $listOrder]);
	}
	public function getDetail($id){
		$order = Order::find($id);
		return response(['success' => true, 'data' => $order]);
	}
	/*postStatus*/
	public function checkQuantity($order_items){
		foreach ($order_items as $key => $value) {
			if($value->product->quantity_in_stock < $value->quantity)
				return 0;
		}
		return 1;
	}
	public function postStatus(Request $request,$id){
		$order = Order::find($id);
		switch ($request->intStatus) {
			case 0:
                return response(['success' => true, 'message' => 'Đơn vốn chờ xử lý rồi']);
				break;
			case 1:
				# code...
				$order->status = 'Hủy';
				break;
			case 2:
				$order->status = 'Đang giao';
				$order_items = $order->order_items;
				if($this->checkQuantity($order_items)==0) return response(['success' => true, 'message' => 'Không đủ sản phẩm trong kho']);
				else{
					foreach ($order_items as $key => $value) {
						$product = Product::find($value->product_id);
						$product->quantity_in_stock-=$value->quantity;
						$product->save();
					}
				}
				break;
			case 3:
				$order->status = 'Đã nhận hàng';
				$payment = Payment::find($order->payment_id);
				$payment->status = 'Đã thanh toán';
				$payment->save();
				break;	
			default:
				# code...
				break;
		}
		$order->save();
        return response(['success' => true, 'message' => 'Cập nhật thành công']);
	}
}
