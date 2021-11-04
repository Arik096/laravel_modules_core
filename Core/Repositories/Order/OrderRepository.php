<?php


namespace Modules\Core\Repositories\Order;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Modules\Core\Models\Hub\HubWmm;
use Modules\Core\Models\Orders\Order;
use Modules\Core\Models\Orders\OrderData;
use Modules\Core\Models\Products\RetailProduct;
use Modules\Core\Models\User\RetailUser;
use Modules\Core\Repositories\Contracts\OrderRepositoryInterface;
use Modules\Core\Repositories\Product\RetailProductRepository;

class OrderRepository implements OrderRepositoryInterface
{

    public $Error;

    public function orderHistory($prefix){
        Session::forget('search_data');
        Session::forget('search_start_date');
        Session::forget('search_end_date');
        return Order::with('orderData','user','payment', 'payment.payMethod', 'payment.payMode', 'payment.payBank', 'foInfo')
            //->whereBetween('created_at', [date('Y-m-01 00:00:00'), date('Y-m-d 23:59:59')])
             ->where(['order_type'=>'1'])
            ->orderBy('id', 'desc')
            ->paginate(100);
    }



    public function orderHistoryFilter($request,$prefix){
        Session::forget('search_data');
        Session::forget('search_start_date');
        Session::forget('search_end_date');

        $order = Order::with('payment','user', 'payment.payMethod', 'payment.payMode', 'payment.payBank', 'foInfo')
            ->where(['order_type'=>'1']);

        if($request->search != ""){
            $order->where(['order_table_unique_id'=>$request->search])->orWhereHas('user',function($query) use($request){
                $query->where('name','like','%'.$request->search.'%')->orWhere(['mobile'=>$request->search]);
            });

            Session::put('search_data',$request->search);
        }

        if($request->dates != ""){
            $ex = explode("/",$request->dates);
            $start_date = trim($ex[0])." 00:00:00";
            $end_date = trim($ex[1])." 23:59:59";
            $order->whereBetween('created_at', [$start_date, $end_date]);
            Session::put('search_start_date',$start_date);
            Session::put('search_end_date',$end_date);
        }

        return $order->orderBy('id', 'desc')
            ->paginate(100);
    }



    public function orderHistoryExport(){
        $order = Order::with('payment','user', 'payment.payMethod', 'payment.payMode', 'payment.payBank', 'foInfo')
            ->where(['order_type'=>'1']);

        if(Session::has('search_data') && Session::get('search_data') != ""){
            $order->where(['order_table_unique_id'=>Session::get('search_data')])->orWhereHas('user',function($query) {
                $query->where('name','like','%'.Session::get('search_data').'%')->orWhere(['mobile'=>Session::get('search_data')]);
            });
        }

        if((Session::has('search_start_date') && Session::has('search_end_date')) && (Session::get('search_end_date') != "")){
            $order->whereBetween('created_at', [Session::get('search_start_date'), Session::get('search_end_date')]);
        }
        return $order->orderBy('id', 'desc')
            ->get();
    }




    public function submitOrder($request,$prefix){
        if (!$request->has('name')) {
            $this->Error = "NO Product Selected";
            return false;
        }

        //make Data set
        $products = $request->product_or_package_id;
        $qty = $request->qty;
        $dataSet = array();

        for ($i = 0; $i < count($products); $i++) {
            $dataSet[$i]['name'] = $products[$i];
        }
        for ($i = 0; $i < count($qty); $i++) {
            $dataSet[$i]['qty'] = $qty[$i];
        }

        try{
            DB::beginTransaction();
            $hub_id = HubWmm::where(['user_id' => $request->user_id])
                ->first()->hub_id;

            #product Orders Table
            $rid = date('ymdhis');

            $req = new Order();
            $req->order_table_unique_id = $rid;
            $req->user_id = $request->user_id;
            $req->bonus_point = $request->bonus_point;
            $req->order_type = "1";
            $req->save();

            //product Orders Data Table
            $totalPayment = 0;
            $j = 0; // 2increment
            for ($i = 0; $i < count($request->name); $i++) {

                $retailProduct = new RetailProductRepository();

                $products = RetailProduct::with( 'product')
                    ->where(['id' => $request->product_or_package_id[$i]])
                    ->first();

                $dataReq = new OrderData();
                $dataReq->removeAllTranslation(); //Remove All translation

                $dataReq->product_id = $request->product_or_package_id[$i];
                $dataReq->company_id = $products->company_id;
                $totalPayment += (($request->pprice[$i] * $request->qty[$i]) - ($request->discount[$i] * $request->qty[$i]));
                $dataReq->order_id = $req->id;
                $dataReq->product_name = $products->product->getDataJsonFormate($products->product_id)->product_name;

                $dataReq->qty = $request->qty[$i];
                $dataReq->price = $request->pprice[$i];
                $dataReq->discount = $request->discount[$i];
                $dataReq->distributor_price = $products->distributor_purchase_price;

                /*************** unit ****************/
                $dataReq->unit_quantity = $request->unit_quantity[$i];
                $dataReq->unit_identify = $request->unit_identify[$i];
                $jdata = json_decode($request->unit[$j]);
                $j += 2;
                $dataReq->unit = $jdata;
                /*************** unit ****************/

                $dataReq->hub_id = $hub_id;
                $dataReq->pro_type = 'product';
                $dataReq->save();
            }


            RetailUser::where(['id' => $request->user_id])->increment('points', $request->bonus_point);

            DB::commit();
            Session::flash('success', getLangMessage('success_message'));
            Session::put('order_table_unique_id', $rid);
            Session::put('order_id', $req->id);
            Session::put('hub_id', $hub_id);
            Session::put('totalAmount', $totalPayment);
            Session::put('order_data_count', 0);

            return true;
        }catch (\Exception $exception){
            DB::rollBack();
            $this->Error = $exception->getMessage();
            return false;
        }




    }

    public function index(){

    }




}
