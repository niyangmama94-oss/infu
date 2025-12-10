<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Hiring;
use App\Models\HiringConversation;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ManageHiringController extends Controller
{

    public function index()
    {
        $pageTitle = 'All Hiring';
        $hirings = $this->filterHiring();
        return view('admin.hiring.list', compact('pageTitle', 'hirings'));
    }

    public function pending()
    {
        $pageTitle = 'Pending Hiring';
        $hirings =  $this->filterHiring('pending');
        return view('admin.hiring.list', compact('pageTitle', 'hirings'));
    }

    public function inprogress()
    {
        $pageTitle = 'Inprogress Hiring';
        $hirings =  $this->filterHiring('inprogress');
        return view('admin.hiring.list', compact('pageTitle', 'hirings'));
    }

    public function jobDone()
    {
        $pageTitle = 'Job Done Hiring';
        $hirings =  $this->filterHiring('JobDone');
        return view('admin.hiring.list', compact('pageTitle', 'hirings'));
    }

    public function completed()
    {
        $pageTitle = 'Completed Hiring';
        $hirings = $this->filterHiring('completed');
        return view('admin.hiring.list', compact('pageTitle', 'hirings'));
    }

    public function reported()
    {
        $pageTitle = 'Reported Hiring';
        $hirings =  $this->filterHiring('reported');
        return view('admin.hiring.list', compact('pageTitle', 'hirings'));
    }

    public function cancelled()
    {
        $pageTitle = 'Cancelled Hiring';
        $hirings =  $this->filterHiring('cancelled');
        return view('admin.hiring.list', compact('pageTitle', 'hirings'));
    }


    protected function filterHiring($scope = null)
    {
        $hirings = Hiring::paymentCompleted();
        
        if ($scope) {
            $hirings = Hiring::$scope();
        } else {
            $hirings = Hiring::query();
        }
        return $hirings->searchable(['user:username', 'influencer:username'])->orderBy('id', 'desc')->paginate(getPaginate());
    }


    public function detail($id)
    {
        $pageTitle     = 'Hiring Detail';
        $hiring        = Hiring::with('user', 'influencer')->findOrFail($id);
        $conversations = HiringConversation::where('hiring_id', $hiring->id)->orderBy('id', 'desc')->take(10)->get();
        return view('admin.hiring.detail', compact('pageTitle', 'hiring', 'conversations'));
    }

    public function takeAction($id, $status)
    {
   
        $hiring = Hiring::with('user', 'influencer')->findOrFail($id);

        if ($status == Status::HIRING_COMPLETED) {
            $this->inFavourOfInfluencer($hiring);
        }

        if ($status == Status::HIRING_REJECTED) {
            $this->inFavourOfClient($hiring);
        }

        $hiring->status = $status;
        $hiring->save();

        $notify[] = ['success', 'Action taken successfully'];
        return back()->withNotify($notify);
    }

    protected function inFavourOfClient($hiring)
    {
        $influencer = $hiring->influencer;
        $user       = $hiring->user;
        $user->balance += $hiring->amount;
        $user->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $hiring->amount;
        $transaction->post_balance = $user->balance;
        $transaction->trx_type     = '+';
        $transaction->details      = 'Payment refunded due to incomplete hiring task';
        $transaction->trx          = getTrx();
        $transaction->remark       = 'payment_refunded';
        $transaction->save();

        $general = gs();

        notify($user, 'HIRING_REFUND', [
            'site_currency' => $general->cur_text,
            'title'         => $hiring->title,
            'amount'        => showAmount($hiring->amount),
            'post_balance'  => showAmount($user->balance),
            'hiring_no'     => $hiring->hiring_no,
        ]);

        notify($influencer, 'HIRING_REJECTED', [
            'site_currency' => $general->cur_text,
            'amount'        => showAmount($hiring->amount),
            'post_balance'  => showAmount($influencer->balance),
            'hiring_no'     => $hiring->hiring_no,
            'title'         => $hiring->title,
        ]);
    }

    protected function inFavourOfInfluencer($hiring)
    {
        $influencer = $hiring->influencer;
        $user       = $hiring->user;

        $influencer->balance += $hiring->amount;
        $influencer->increment('completed_order');
        $influencer->save();

        $transaction                = new Transaction();
        $transaction->influencer_id = $influencer->id;
        $transaction->amount        = $hiring->amount;
        $transaction->post_balance  = $influencer->balance;
        $transaction->trx_type      = '+';
        $transaction->details       = 'Payment received for completing hiring task';
        $transaction->trx           = getTrx();
        $transaction->remark        = 'payment_on_hiring';
        $transaction->save();

        $general = gs();

        $shortCodes = [
            'site_currency' => $general->cur_text,
            'amount'        => showAmount($hiring->amount),
            'hiring_no'     => $hiring->hiring_no,
            'title'         => $hiring->title,
        ];

        notify($influencer, 'HIRING_COMPLETED_INFLUENCER', $shortCodes);
        notify($user, 'HIRING_COMPLETED_CLIENT', $shortCodes);
    }

    public function conversationStore(Request $request, $id)
    {
        $hiring    = Hiring::find($id);
        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $conversation                = new HiringConversation();
        $conversation->hiring_id     = $hiring->id;
        $conversation->user_id       = $hiring->user_id;
        $conversation->influencer_id = $hiring->influencer_id;
        $conversation->admin_id      = auth()->guard('admin')->id();
        $conversation->sender        = 'admin';
        $conversation->message       = $request->message;
        $conversation->save();

        return view('admin.hiring.last_message', compact('conversation'));
    }

    public function conversationMessage(Request $request)
    {
        $conversations = HiringConversation::where('hiring_id', $request->hiring_id)->take($request->messageCount)->orderBy('id', 'desc')->get();
        return view('admin.hiring.conversation', compact('conversations'));
    }

    public function download($attachment)
    {
        $path = getFilePath('conversation');
        $file = $path . '/' . $attachment;
        return response()->download($file);
    }
}
