<?php

namespace App\Controller;

use App\Assembler\UserBankAccountAssembler;
use App\Dto\UserBankAccountDto;
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
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/users/{id}/accounts')]
class UserBankAccountController extends AbstractController
{
    public function __construct(
        private UserBankAccountRepository $userBankAccountRepository,
        private  UserBankAccountAssembler $userBankAccountAssembler,
//        private SerializerInterface $serializer
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
    public function putDebit(int $id, UserBankAccountDto $userBankAccountDto,Request $request): Response
    {
        $userBankAccount = $this->userBankAccountRepository->find($id);
        if(!$userBankAccount){
            return $this->json(
                ['message' => 'User not found'],
                404,
                ['Content-Type' => 'application/json']
            );
        }

        $amount = json_decode($request->getContent())->amount;
        $userBankAccountDto->transactionAmount = $amount;
        $userBankAccountDto->transactionType = UserBankAccountDto::TRANSACTION_DEBIT;


        $userBankAccountDto = $this->userBankAccountAssembler->transform($userBankAccountDto, $userBankAccount);
        return $this->json($userBankAccountDto, 200, ['Content-Type' => 'application/json']);

    }

    #[Route('/credit', name: 'user_bank_account_credit', methods: ['PUT'])]
    public function putCredit(int $id, Request $request, UserBankAccountDto $userBankAccountDto): Response
    {
        $userBankAccount = $this->userBankAccountRepository->find($id);
        $amount = json_decode($request->getContent())->amount;
        $userBankAccountDto->transactionAmount = $amount;
        $userBankAccountDto->transactionType = UserBankAccountDto::TRANSACTION_CREDIT;



        if(!$userBankAccount){
            return $this->json("User not found", 404, ['Content-Type' => 'application/json']);
        }

        $userBankAccountDto = $this->userBankAccountAssembler->transform($userBankAccountDto, $userBankAccount);

        return $this->json($userBankAccountDto, 200, ['Content-Type' => 'application/json']);

    }
    
}
