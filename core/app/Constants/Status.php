<?php

namespace App\Constants;

class Status{

    const ENABLE = 1;
    const DISABLE = 0;

    const YES = 1;
    const NO = 0;

    const VERIFIED = 1;
    const UNVERIFIED = 0;

    const PAYMENT_INITIATE = 0;
    const PAYMENT_SUCCESS = 1;
    const PAYMENT_PENDING = 2;
    const PAYMENT_REJECT = 3;

    CONST TICKET_OPEN = 0;
    CONST TICKET_ANSWER = 1;
    CONST TICKET_REPLY = 2;
    CONST TICKET_CLOSE = 3;

    CONST PRIORITY_LOW = 1;
    CONST PRIORITY_MEDIUM = 2;
    CONST PRIORITY_HIGH = 3;

    const USER_ACTIVE = 1;
    const USER_BAN = 0;

    const KYC_UNVERIFIED = 0;
    const KYC_PENDING = 2;
    const KYC_VERIFIED = 1;

    const GOOGLE_PAY = 5001;

    const INFLUENCER_ACTIVE = 1;
    const INFLUENCER_BAN = 0;

    const READ = 1;
    const NOT_READ = 0;
    const OFF = 0;
    const ON =1;

    const CUR_BOTH = 1;
    const CUR_TEXT = 2;
    const CUR_SYM = 3;

    const HIRING_PENDING    = 0;
    const HIRING_COMPLETED  = 1;
    const HIRING_INPROGRESS = 2;
    const HIRING_DELIVERED  = 3;
    const HIRING_REPORTED   = 4;
    const HIRING_CANCELLED  = 5;
    const HIRING_REJECTED   = 6;


    const ORDER_PENDING    = 0;
    const ORDER_COMPLETED  = 1;
    const ORDER_INPROGRESS = 2;
    const ORDER_DELIVERED  = 3;
    const ORDER_REPORTED   = 4;
    const ORDER_CANCELLED  = 5;
    const ORDER_REJECTED   = 6;

    const SERVICE_PENDING = 0;
    const SERVICE_APPROVED = 1;
    const SERVICE_REJECTED = 2;

}
