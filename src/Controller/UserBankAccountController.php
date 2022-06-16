<?php

namespace App\Controller;

use App\Assembler\UserBankAccountAssembler;
use App\Dto\UserBankAccountDto;
use App\Entity\UserBankAccount;
use App\Repository\UserBankAccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/users/{id}/accounts')]
class UserBankAccountController extends AbstractController
{
    public function __construct(
        private UserBankAccountRepository $userBankAccountRepository,
        private  UserBankAccountAssembler $userBankAccountAssembler,
        private EntityManagerInterface $entityManager
    )
    {}


    #[Route('/', name: 'user_bank_account_index', methods: ['GET'])]
    public function getAccountByUser(int $id): Response
    {
        $userBankAccount = $this->userBankAccountRepository->find($id);
        if(!$userBankAccount){
            return $this->json(
                ['message' => 'User not found'],
                404,
                ['Content-Type' => 'application/json']
            );
        }
        return $this->json(
            $userBankAccount,
            200,
            ['Content-Type' => 'application/json']);
    }


    #[Route('/debit', name: 'user_bank_account_debit', methods: ['PUT'])]
    public function putDebit(int $id, UserBankAccountDto $userBankAccountDto,Request $request): Response
    {
        $userBankAccount = $this->userBankAccountRepository->find($id);
        if(!$userBankAccount){
            return $this->json(['message' => 'User not found'], 404, ['Content-Type' => 'application/json']);
        }

        $transactionAmount = json_decode($request->getContent())->amount;
        $userBankAccountDto->transactionAmount = $transactionAmount;

        $userBankAccountDto->transactionType = UserBankAccount::TRANSACTION_DEBIT;

        $this->userBankAccountAssembler->reverseTransform($userBankAccountDto, $userBankAccount);
        $this->entityManager->flush();

        return $this->json(
            $this->userBankAccountAssembler->transform($userBankAccountDto, $userBankAccount),
            200,
            ['Content-Type' => 'application/json']
        );

    }

    #[Route('/credit', name: 'user_bank_account_credit', methods: ['PUT'])]
    public function putCredit(int $id, Request $request, UserBankAccountDto $userBankAccountDto): Response
    {
        $userBankAccount = $this->userBankAccountRepository->find($id);
        if(!$userBankAccount){
            return $this->json("User not found", 404, ['Content-Type' => 'application/json']);
        }

        $transactionAmount = json_decode($request->getContent())->amount;

        $userBankAccountDto->transactionAmount = $transactionAmount;
        $userBankAccountDto->transactionType = UserBankAccount::TRANSACTION_CREDIT;

        $this->userBankAccountAssembler->reverseTransform($userBankAccountDto, $userBankAccount);
        $this->entityManager->flush();

        return $this->json(
            $this->userBankAccountAssembler->transform($userBankAccountDto, $userBankAccount),
            200,
            ['Content-Type' => 'application/json']);
    }
    
}
