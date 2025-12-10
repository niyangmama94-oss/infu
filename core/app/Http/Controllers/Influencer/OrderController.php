<?php

namespace App\Http\Controllers\Influencer;

use App\Constants\Status;
use App\Events\MessageSent;
use App\Events\OrderMessage;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderConversation;
use App\Models\Transaction;
use App\Models\User;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller {
    public function index() {
        $this->pageTitle = 'All Orders';
        return $this->filterOrder();
    }

    public function pending() {
        $this->pageTitle = 'Pending Orders';
        return $this->filterOrder('pending');
    }

    public function inprogress() {
        $this->pageTitle = 'Processing Orders';
        return $this->filterOrder('inprogress');
    }

    public function jobDone() {
        $this->pageTitle = 'Job Done Orders';
        return $this->filterOrder('JobDone');
    }

    public function completed() {
        $this->pageTitle = 'Completed Orders';
        return $this->filterOrder('completed');
    }

    public function reported() {
        $this->pageTitle = 'Reported Orders';
        return $this->filterOrder('reported');
    }

    public function cancelled() {
        $this->pageTitle = 'Cancelled Orders';
        return $this->filterOrder('cancelled');
    }

    protected function filterOrder($scope = null) {
        $influencerId = authInfluencerId();
        if ($scope) {
            $orders = Order::$scope();
        } else {
            $orders = Order::query();
        }
        $orders = $orders->searchable(['order_no','user:username'])->where('influencer_id', $influencerId)->with('user')->latest()->paginate(getPaginate());

        $pageTitle = $this->pageTitle;

        $pendingOrder = Order::pending()->where('influencer_id', $influencerId)->count();

        return view('Template::influencer.order.list', compact('pageTitle', 'orders', 'pendingOrder'));
    }

    public function detail($id) {
        $pageTitle = 'Order Detail';
        $order     = Order::where('influencer_id', authInfluencerId())->with('user', 'service','review')->findOrFail($id);
        return view('Template::influencer.order.detail', compact('pageTitle', 'order'));
    }

    public function cancelOrder($id) {
        $influencer    = authInfluencer();
        $order         = Order::where('id', $id)->where('influencer_id', $influencer->id)->with('user')->firstOrFail();
        $order->status = Status::ORDER_CANCELLED;
        $order->save();

        $user    = $order->user;
        $general = gs();

        if($order->payment_status == Status::PAYMENT_SUCCESS){
            $user->balance += $order->amount;
            $user->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $order->amount;
        $transaction->post_balance = $user->balance;
        $transaction->trx_type     = '+';
        $transaction->details      = 'Payment refunded for order cancellation';
        $transaction->trx          = getTrx();
        $transaction->remark       = 'order_refund';
        $transaction->save();

        }

        notify($user, 'ORDER_CANCELLED', [
            'influencer'    => $influencer->username,
            'site_currency' => $general->cur_text,
            'amount'        => showAmount($order->amount),
            'order_no'      => $order->order_no,
            'post_balance'  => showAmount($user->balance),
            'title'         => $order->title,
        ]);

        $notify[] = ['success', 'Order canceled successfully'];
        return back()->withNotify($notify);
    }

    public function orderAccept($id) {
        $influencer    = authInfluencer();
        $order         = Order::where('id', $id)->where('influencer_id', $influencer->id)->with('user')->firstOrFail();
        $order->status = Status::ORDER_INPROGRESS;
        $order->save();

        $user    = $order->user;
        $general = gs();
        notify($user, 'ORDER_ACCEPT', [
            'influencer'    => $influencer->username,
            'site_currency' => $general->cur_text,
            'title'         => $order->title,
            'amount'        => showAmount($order->amount),
            'order_no'      => $order->order_no,
        ]);

        $notify[] = ['success', 'Order accepted successfully'];
        return back()->withNotify($notify);
    }

    public function jobDoneStatus($id) {
        $influencer    = authInfluencer();
        $order         = Order::where('id', $id)->where('influencer_id', $influencer->id)->with('user')->firstOrFail();
        $order->status = Status::ORDER_DELIVERED ;
        $order->save();

        $user    = $order->user;
        $general = gs();
        notify($user, 'JOB_DONE', [
            'influencer'    => $influencer->username,
            'site_currency' => $general->cur_text,
            'title'         => $order->title,
            'amount'        => showAmount($order->amount),
            'order_no'      => $order->order_no,
        ]);
        $notify[] = ['success', 'Order has been done successfully'];
        return back()->withNotify($notify);
    }

    public function conversation($id) {
        $pageTitle           = 'Order Conversation';
        $order               = Order::where('influencer_id', authInfluencerId())->with('orderMessage')->findOrFail($id);
        $user                = User::where('id', $order->user_id)->first();
        $conversationMessage = $order->orderMessage->take(10);
        return view('Template::influencer.order.conversation', compact('pageTitle', 'conversationMessage', 'user', 'order'));
    }

    public function conversationStore(Request $request, $id) {
        $order = Order::where('influencer_id', authInfluencerId())->find($id);

        if (!$order) {
            return response()->json(['error' => 'Order not found.']);
        }

        $validator = Validator::make($request->all(), [
            'message'       => 'required',
            'attachments'   => 'nullable|array',
            'attachments.*' => ['required', new FileTypeValidate(['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'txt'])],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $user = User::find($order->user_id);

        $message                = new OrderConversation();
        $message->order_id      = $order->id;
        $message->user_id       = $user->id;
        $message->influencer_id = authInfluencerId();
        $message->sender        = 'influencer';
        $message->message       = $request->message;

        if ($request->hasFile('attachments')) {

            foreach ($request->file('attachments') as $file) {
                try {
                    $arrFile[] = fileUploader($file, getFilePath('conversation'));
                } catch (\Exception$exp) {
                    return response()->json(['error' => 'Couldn\'t upload your image']);
                }

            }

            $message->attachments = json_encode($arrFile);
        }

        $channelName = "chat-data-conversion-{$order->id}";

        event(new OrderMessage($message, $channelName));

        $message->save();
        return view('Template::user.conversation.last_message', compact('message'));
    }

    public function conversationMessage(Request $request) {
        $conversationMessage = OrderConversation::where('order_id', $request->order_id)->take($request->messageCount)->latest()->get();
        return view('Template::influencer.conversation.message', compact('conversationMessage'));
    }

}
