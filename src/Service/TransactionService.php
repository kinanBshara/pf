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

    public function isTimeToSendEmail() : bool
    {
        $current_time = time();
        $new_date_time = date('h:i A', ($current_time));

        $sunrise = "06:00 am";
        $sunset = "10:00 pm";

        $date1 = \DateTime::createFromFormat('h:i a', $current_time);
        $date2 = \DateTime::createFromFormat('h:i a', $sunrise);
        $date3 = \DateTime::createFromFormat('h:i a', $sunset);


        if ($date1 > $date2 && $date1 < $date3)
        {
            dd('hello');
            echo 'here';
        }

        dd('top');
    }

}