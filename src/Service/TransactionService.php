<?php

namespace App\Service;

use App\Entity\UserBankAccount;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;

class TransactionService
{
    public const FLOOR_AMOUNT = 1000;

    public function __construct(private ManagerRegistry $managerRegistry)
    {}

    public function debit(int $amount, UserBankAccount $userBankAccount) : int
    {

        $balance = $userBankAccount->getBalance();

        if($balance >= $amount) {
            $userBankAccount->setBalance($balance - $amount);
        } else {
            $userBankAccount->setBalance(0);
            $amount = $balance;
        }

        $em = $this->managerRegistry->getManager();
        $em->flush();
        $this->isTimeToSendEmail();

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

        $em = $this->managerRegistry->getManager();
        $em->flush();

        $this->isTimeToSendEmail();

        return $amount;

    }

    public function isTimeToSendEmail() : void
    {
        $now = date('h:i A', time());
        //$now = "11:00 pm";
        //echo ($now);
        $current_time = \DateTime::createFromFormat('h:i a', $now);
        $start = \DateTime::createFromFormat('h:i a', "06:00 am");
        $end = \DateTime::createFromFormat('h:i a', "10:00 pm");


        if ($current_time >= $start && $current_time < $end)
        {
            echo 'don\'t send notification';

        }
        echo 'send notification';
    }

}