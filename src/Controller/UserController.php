<?php

namespace App\Controller;

use App\Entity\UserBankAccount;
use App\Form\UserBankAccountType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/add', name: 'user_add')]
    public function add(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $user = new UserBankAccount();
        $form = $this->createForm(UserBankAccountType::class, $user);

        if ($request->isMethod('POST')){
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()){

               $data =  $form->getData();

               $em = $managerRegistry->getManager();
               $em->persist($user);
               $em->flush();

               $this->redirectToRoute('user_add');

            }
        }

        return $this->render('user/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
