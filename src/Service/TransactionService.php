<?php

namespace App\Service;

use App\Entity\UserBankAccount;
use Doctrine\ORM\EntityManagerInterface;

class TransactionService
{
    public const FLOOR_AMOUNT = 1000;

    public function __construct(private EntityManagerInterface $em)
    {}

    public function debit(int $amount, UserBankAccount $userBankAccount) : int
    {
        $balance = $userBankAccount->getBalance();

        if($balance >= $amount) {
            $amount = $balance - $amount;
            $userBankAccount->setBalance($amount);
        } else {
            $userBankAccount->setBalance(0);
            $amount = $balance;
        }

        $this->em->flush();
//        $this->isTimeToSendEmail();

        return $amount;
    }

    public function credit(int $amount, UserBankAccount $userBankAccount) : int
    {
        $balance = $userBankAccount->getBalance();

        if($balance + $amount > self::FLOOR_AMOUNT) {
            $userBankAccount->setBalance(self::FLOOR_AMOUNT);
            $amount = self::FLOOR_AMOUNT - $balance;
        } else {
            $userBankAccount->setBalance($balance + $amount);
        }

        $this->em->flush();

//        $this->isTimeToSendEmail();

        return $amount;

    }

    public function isTimeToSendEmail() : bool
    {
        $now = date('h:i A', time());

        $current_time = \DateTime::createFromFormat('h:i a', $now);
        $start = \DateTime::createFromFormat('h:i a', "06:00 am");
        $end = \DateTime::createFromFormat('h:i a', "10:00 pm");


        if ($current_time > $start && $current_time < $end)
        {
            dd('hello');
            echo 'here';
        }

        dd('top');
    }

}