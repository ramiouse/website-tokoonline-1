<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Cart;
use App\Models\Transaction;
use App\Models\TransactionDetail;

use Exception;

use Midtrans\Snap;
use Midtrans\Config;
use Midtrans\Notification;

class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        // save users data
        $user = Auth::user();
        $user->update($request->except('total_price'));

        // process checkout
        $code = 'STORE-' . mt_rand(000000,999999);
        $carts = Cart::with(['product', 'user'])
                       ->where('users_id', Auth::user()->id)
                       ->get();

        // Transaction create
        $transaction = Transaction::create([
            'users_id' => Auth::user()->id,
            'insurance_price' => 0,
            'shipping_price' => 0,
            'total_price' => $request->total_price,
            'transaction_status' => 'PENDING',
            'code' => $code
        ]);

        // save transaction details
        foreach ($carts as $cart) {
            $trx = 'TRX-' . mt_rand(000000,999999);
            
            TransactionDetail::create([
                'transactions_id' => $transaction->id,
                'products_id' => $cart->product->id,
                'price' => $cart->product->price,
                'shipping_status' => 'PENDING',
                'resi' => '',
                'code' => $trx
            ]);
        }

        // delete chart data ketika selesai payment
        Cart::where('users_id', Auth::user()->id)->delete();

        // Konfigurasi midtrans
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = config('services.midtrans.isSanitized');
        Config::$is3ds = config('services.midtrans.is3ds');

        // create array for data to midtrans
        $midtrans = [
            'transaction_details' => [
                "order_id" => $code,
                "gross_amount" => (int) $request->total_price
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email
            ],
            'enabled_payments' => [
                'gopay',
                'permata_va',
                'bank_transfer'
            ],
            'vtweb' => []
        ];

        try {
            // get snap payment page url
            $paymentUrl = Snap::createTransaction($midtrans)->redirect_url;
            // redirect to snap payment page
            return redirect($paymentUrl);
        
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        // return dd($transaction);
    }
    public function callback(Request $request)
    {
        // set konfigurasi midtrans
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = config('services.midtrans.isSanitized');
        Config::$is3ds = config('services.midtrans.is3ds');

        // instance midtrans notification
        $notification = new Notification();

        // assign ke variable untuk mudahin coding
        $status = $notification->transaction_status;
        $type= $notification->payment_type;
        $fraud= $notification->fraud_status;
        $order_id= $notification->order_id;

        // cari transaksi berdasarkan id
        $transaction = Transaction::findOrFail($order_id);
        // handle notification status
        if ($status == 'capture') {
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    $transaction->status = 'PENDING';
                }
            } else {
                $transaction->status = 'SUCCESS';
            }
        }
        else if($status == 'settlement') {
            $transaction->status = 'SUCCESS';
        }
        else if($status == 'pending') {
            $transaction->status =  'PENDING';
        }
        else if($status == 'deny') {
            $transaction->status = 'CANCELLED';
        }
        else if($status == 'expire') {
            $transaction->status = 'CANCELLED';
        }
        else if($status == 'cancel') {
            $transaction->status = 'CANCELLED';
        }

        // simpan transaksi
        $transaction->save();
    }
}
