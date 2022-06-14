<?php

namespace App\Controller;

use App\Entity\UserBankAccount;
use App\Form\UserBankAccountType;
use App\Repository\UserBankAccountRepository;
use App\Service\TransactionService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
        private TransactionService $transactionService
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
        return $this->json($userBankAccount, 200, ['Content-Type' => 'application/json']);
    }


    #[Route('/debit', name: 'user_bank_account_debit', methods: ['PUT'])]
    public function putDebit(int $id, Request $request): Response
    {
        $userBankAccount = $this->userBankAccountRepository->find($id);

        if(!$userBankAccount){
            return $this->json(
                ['message' => 'User not found'],
                404,
                ['Content-Type' => 'application/json']
            );
        }
        $amount = $request->get('amount');
        $debitedAmount = $this->transactionService->debit($amount, $userBankAccount);

        return $this->json($userBankAccount, 200, ['Content-Type' => 'application/json']);

    }

    #[Route('/debit', name: 'user_bank_account_debit', methods: ['PUT'])]
    public function putCredit(Request $request, UserBankAccount $userBankAccount, EntityManagerInterface $entityManager): Response
    {
        if(!$userBankAccount){
            return $this->json("User not found", 404, ['Content-Type' => 'application/json']);
        }
        $amount = $request->get('amount');
        //transactionService->credit($amount, $userBankAccount);
        return $this->json($userBankAccount, 200, ['Content-Type' => 'application/json']);

    }
    
}
