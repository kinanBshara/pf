<?php

namespace App\Controller;

use App\Entity\UserBankAccount;
use App\Form\UserBankAccountType;
use App\Repository\UserBankAccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/users/{id}/accounts')]
class UserBankAccountController extends AbstractController
{
    #[Route('/', name: 'user_bank_account_index', methods: ['GET'])]
    public function getAccountByUser(UserBankAccount $userBankAccount, UserBankAccountRepository $userBankAccountRepository): Response
    {
        if(!$userBankAccount){
            return $this->json("User not found", 404, ['Content-Type' => 'application/json']);
        }
        return $this->json($userBankAccount, 200, ['Content-Type' => 'application/json']);
    }


    #[Route('/debit', name: 'user_bank_account_debit', methods: ['PUT'])]
    public function putDebit(Request $request, UserBankAccount $userBankAccount, EntityManagerInterface $entityManager): Response
    {
        if(!$userBankAccount){
            return $this->json("User not found", 404, ['Content-Type' => 'application/json']);
        }
        $amount = $request->get('amount');
        //transactionService->debit($amount, $userBankAccount);
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
