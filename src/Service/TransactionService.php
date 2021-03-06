<?php

namespace App\Service;

use App\Entity\UserBankAccount;
use Doctrine\ORM\EntityManagerInterface;

class TransactionService
{
    public const FLOOR_AMOUNT = 1000;

    public function __construct(private EmailService $emailService)
    {}

    public function debit(int $amount, UserBankAccount $userBankAccount) : int
    {
        $balance = $userBankAccount->getBalance();

        if ($balance <= $amount) {
            # Remain only debited
            $amount = $balance;
        }

        $this->notify($userBankAccount->getEmail());
        return $amount;

    }

    public function credit(int $amount, UserBankAccount $userBankAccount) : int
    {
        $balance = $userBankAccount->getBalance();
        if ($balance + $amount > self::FLOOR_AMOUNT) {
            $amount = self::FLOOR_AMOUNT - $balance;
        }

        $this->notify($userBankAccount->getEmail());

        return $amount;
    }

    public function isTimeToSendEmail() : bool
    {
        $now = date('h:i A', time());
        $current_time = \DateTime::createFromFormat('h:i a', $now);
        $start = \DateTime::createFromFormat('h:i a', "06:00 am");
        $end = \DateTime::createFromFormat('h:i a', "10:00 pm");

        if ($current_time >= $start && $current_time < $end)
        {
            echo 'don\'t send notification';
            return false;
        }

        echo 'send notification';
        return true;
    }

    public function notify(string $email) : void
    {
        if (true === $this->isTimeToSendEmail()) {
            $this->emailService->send($email);
        }
    }


}