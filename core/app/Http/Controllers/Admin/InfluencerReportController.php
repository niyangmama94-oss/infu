<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use App\Models\Transaction;
use App\Models\UserLogin;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InfluencerReportController extends Controller {
    public function transaction(Request $request) {
        $pageTitle = 'Transaction Logs';

        $remarks = Transaction::where('influencer_id','!=',0)->distinct('remark')->orderBy('remark')->get('remark');

        $transactions = Transaction::where('influencer_id','!=',0)->with('influencer')->orderBy('id', 'desc')->paginate(getPaginate());

        return view('admin.influencers.reports.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function loginHistory(Request $request) {
        $pageTitle = 'Login History';
        $loginLogs = UserLogin::searchable(['influencer.username'])->where('influencer_id','!=',0)->orderBy('id', 'desc')->with('influencer')->paginate(getPaginate());
        return view('admin.influencers.reports.logins', compact('pageTitle', 'loginLogs'));
    }

    public function loginIpHistory($ip) {
        $pageTitle = 'Login by - ' . $ip;
        $loginLogs = UserLogin::where('influencer_id','!=',0)->where('user_ip', $ip)->orderBy('id', 'desc')->with('influencer')->paginate(getPaginate());
        return view('admin.influencers.reports.logins', compact('pageTitle', 'loginLogs', 'ip'));

    }

    public function notificationHistory(Request $request) {
        $pageTitle = 'Notification History';
        $logs      = NotificationLog::searchable(['influencer:username'])->where('influencer_id','!=',0)->orderBy('id', 'desc')->with('influencer')->paginate(getPaginate());
        return view('admin.influencers.reports.notification_history', compact('pageTitle', 'logs'));
    }

    public function emailDetails($id) {
        $pageTitle = 'Email Details';
        $email     = NotificationLog::findOrFail($id);
        return view('admin.influencers.reports.email_details', compact('pageTitle', 'email'));
    }

}
