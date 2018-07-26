<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use App\Jobs\SendOrderEmail;
use App\Order;
use Log;

class MailController extends Controller
{
    public function index() {

        for ($i=0; $i<1; $i++) { $order = Order::findOrFail( rand(1,50) ); if (rand(1, 3) > 1) {
            SendOrderEmail::dispatch($order)->onQueue('email');
        } else {
            SendOrderEmail::dispatch($order)->onQueue('sms');
        }
        }

        return 'Dispatched orders';
    }
}
