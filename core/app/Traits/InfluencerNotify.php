<?php
namespace App\Traits;

use App\Constants\Status;

trait InfluencerNotify
{
    public static function notifyToInfluencer(){
        return [
            'allInfluencers'              => 'All Influencers',
            'selectedInfluencers'         => 'Selected Influencers',
            'kycUnverified'         => 'Kyc Unverified Influencers',
            'kycVerified'           => 'Kyc Verified Influencers',
            'kycPending'            => 'Kyc Pending Influencers',
            'withBalance'           => 'With Balance Influencers',
            'emptyBalanceInfluencers'     => 'Empty Balance Influencers',
            'twoFaDisableInfluencers'     => '2FA Disable Influencer',
            'twoFaEnableInfluencers'      => '2FA Enable Influencer',
            'hasDepositedInfluencers'       => 'Deposited Influencers',
            'notDepositedInfluencers'       => 'Not Deposited Influencers',
            'pendingDepositedInfluencers'   => 'Pending Deposited Influencers',
            'rejectedDepositedInfluencers'  => 'Rejected Deposited Influencers',
            'topDepositedInfluencers'     => 'Top Deposited Influencers',
            'hasWithdrawInfluencers'      => 'Withdraw Influencers',
            'pendingWithdrawInfluencers'  => 'Pending Withdraw Influencers',
            'rejectedWithdrawInfluencers' => 'Rejected Withdraw Influencers',
            'pendingTicketInfluencer'     => 'Pending Ticket Influencers',
            'answerTicketInfluencer'      => 'Answer Ticket Influencers',
            'closedTicketInfluencer'      => 'Closed Ticket Influencers',
            'notLoginInfluencers'         => 'Last Few Days Not Login Influencers',
        ];
    }

    public function scopeSelectedInfluencers($query)
    {
        return $query->whereIn('id', request()->influencer ?? []);
    }

    public function scopeAllInfluencers($query)
    {
        return $query;
    }

    public function scopeEmptyBalanceInfluencers($query)
    {
        return $query->where('balance', '<=', 0);
    }

    public function scopeTwoFaDisableInfluencers($query)
    {
        return $query->where('ts', Status::DISABLE);
    }

    public function scopeTwoFaEnableInfluencers($query)
    {
        return $query->where('ts', Status::ENABLE);
    }

    public function scopeHasDepositedInfluencers($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->successful();
        });
    }

    public function scopeNotDepositedInfluencers($query)
    {
        return $query->whereDoesntHave('deposits', function ($q) {
            $q->successful();
        });
    }

    public function scopePendingDepositedInfluencers($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->pending();
        });
    }

    public function scopeRejectedDepositedInfluencers($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->rejected();
        });
    }

    public function scopeTopDepositedInfluencers($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->successful();
        })->withSum(['deposits'=>function($q){
            $q->successful();
        }], 'amount')->orderBy('deposits_sum_amount', 'desc')->take(request()->number_of_top_deposited_influencer ?? 10);
    }

    public function scopeHasWithdrawInfluencers($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->approved();
        });
    }

    public function scopePendingWithdrawInfluencers($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->pending();
        });
    }

    public function scopeRejectedWithdrawInfluencers($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->rejected();
        });
    }

    public function scopePendingTicketInfluencer($query)
    {
        return $query->whereHas('tickets', function ($q) {
            $q->whereIn('status', [Status::TICKET_OPEN, Status::TICKET_REPLY]);
        });
    }

    public function scopeClosedTicketInfluencer($query)
    {
        return $query->whereHas('tickets', function ($q) {
            $q->where('status', Status::TICKET_CLOSE);
        });
    }

    public function scopeAnswerTicketInfluencer($query)
    {
        return $query->whereHas('tickets', function ($q) {

            $q->where('status', Status::TICKET_ANSWER);
        });
    }

    public function scopeNotLoginInfluencers($query)
    {
        return $query->whereDoesntHave('loginLogs', function ($q) {
            $q->whereDate('created_at', '>=', now()->subDays(request()->number_of_days ?? 10));
        });
    }

    public function scopeKycVerified($query)
    {
        return $query->where('kv', Status::KYC_VERIFIED);
    }

}
