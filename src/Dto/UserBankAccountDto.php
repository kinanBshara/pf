<?php

namespace App\Dto;

class UserBankAccountDto
{
    public const TRANSACTION_DEBIT = 'debit';
    public const TRANSACTION_CREDIT = 'credit';

    public $id;
    public $fullname;
    public $email;
    public $transactionAmount;
    public $balance;
    public $debitedAmount;
    public $creditedAmount;
    public $transactionType;

}