<?php

namespace App\Assembler;

use App\Dto\UserBankAccountDto;
use App\Entity\UserBankAccount;
use App\Service\TransactionService;

class UserBankAccountAssembler
{
    public function __construct(private TransactionService $transactionService)
    {}


    public function transform(?UserBankAccountDto $userBankAccountDto, UserBankAccount $userBankAccount) : UserBankAccountDto
    {
        $userBankAccountDto = $userBankAccountDto ?? new UserBankAccountDto();

        $userBankAccountDto->id                 = $userBankAccount->getId();
        $userBankAccountDto->fullname           = $userBankAccount->getFullName();
        $userBankAccountDto->email              = $userBankAccount->getEmail();
        $userBankAccountDto->balance            = $userBankAccount->getBalance();
        $userBankAccountDto->transactionRemain  = $this->getTransactionRemain($userBankAccountDto);

        return $userBankAccountDto;
    }

    public function reverseTransform(UserBankAccountDto $userBankAccountDto, UserBankAccount $userBankAccount): UserBankAccount
    {

        if ($userBankAccountDto->transactionType === UserBankAccount::TRANSACTION_DEBIT) {

            $debitedAmount = $this->transactionService->debit($userBankAccountDto->transactionAmount, $userBankAccount);
            $userBankAccountDto->debitedAmount  = $debitedAmount;
            $userBankAccountDto->creditedAmount = 0;
            $userBankAccount->setBalance($userBankAccount->getBalance() - $debitedAmount);
        }


        if ($userBankAccountDto->transactionType === UserBankAccount::TRANSACTION_CREDIT) {

            $creditedAmount =  $this->transactionService->credit($userBankAccountDto->transactionAmount, $userBankAccount);
            $userBankAccountDto->creditedAmount = $creditedAmount;
            $userBankAccountDto->debitedAmount = 0;

            $userBankAccount->setBalance($userBankAccount->getBalance() + $creditedAmount);
        }

        return $userBankAccount;
    }

    public function getTransactionRemain(UserBankAccountDto $userBankAccountDto) : int
    {
        return match($userBankAccountDto->transactionType){
            UserBankAccount::TRANSACTION_DEBIT => $userBankAccountDto->transactionAmount - $userBankAccountDto->debitedAmount,
            UserBankAccount::TRANSACTION_CREDIT => $userBankAccountDto->transactionAmount - $userBankAccountDto->creditedAmount
        };
    }

}
