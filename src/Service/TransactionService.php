<?php

namespace App\Service;

use App\Entity\UserBankAccount;

class TransactionService
{
    public function debit(int $amount, UserBankAccount $userBankAccount) : int
    {
        $balance = $userBankAccount->getBalance();

        if($balance >= $amount) {
            $userBankAccount->setBalance($balance - $amount);
            return $amount;
        } else {
            $userBankAccount->setBalance(0);
            return $balance;
        }

    }

    public function credit(int $amount, UserBankAccount $userBankAccount) : int
    {

    }


}