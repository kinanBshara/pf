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

    $userBankAccountDto->id             = $userBankAccount->getId();
    $userBankAccountDto->fullname       = $userBankAccount->getFullName();
    $userBankAccountDto->email          = $userBankAccount->getEmail();

    if ($userBankAccountDto->transactionType === UserBankAccountDto::TRANSACTION_DEBIT) {
        $userBankAccountDto->debitedAmount  = $this->transactionService->debit($userBankAccountDto->transactionAmount, $userBankAccount);
    }
    if ($userBankAccountDto->transactionType === UserBankAccountDto::TRANSACTION_CREDIT) {
        $userBankAccountDto->creditedAmount = $this->transactionService->credit($userBankAccountDto->transactionAmount, $userBankAccount);
    }
    $userBankAccountDto->balance        = $userBankAccount->getBalance();

    return $userBankAccountDto;
}

}
