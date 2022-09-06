<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{

    public function __construct(EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHash)
    {
        $this->manager = $manager;
        $this->passwordHash = $passwordHash;
    }


    /**
     * @Route("/user", name="app_user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    // Modification d'un utilisateur

    /**
     * @Route("/user/update/{id}", name="app_update_user")
     */
    public function updateUser($id, Request $request)
    {
        // Je recupere dans la bdd l article grace a l'id
        $singleUser = $this->manager->getRepository(User::class)->findBy(['id' => $id]);

        // Je materialise le formulaire et je donne l atricle recuperer en bdd a celui ci
        $form = $this->createForm(RegisterType::class, $singleUser[0]);
        $form->handleRequest($request);

        // Si le formulaire et soumis et en meme temp valide alors j envoi les modification en bdd
        if ($form->isSubmitted() && $form->isValid()) {

            $passwordEncod = $this->passwordHash->hashPassword($singleUser[0], $singleUser[0]->getPassword());

            $singleUser[0]->setPassword($passwordEncod);

            $this->manager->persist($singleUser[0]);
            $this->manager->flush();
            // Pour finir je fait une redirection
            return $this->redirectToRoute('app_dashboard');
        }


        return $this->render('user/update.html.twig', [
            'singleUser' => $singleUser[0],
            'form' => $form->createView()
        ]);
    }

    // Suppression d'un user

    /**
     * @Route("/user/remove/{id}", name="app_remove_user")
     */
    public function deleteUser($id)
    {
        $singleUser = $this->manager->getRepository(User::class)->findBy(['id' => $id]);

        $this->manager->remove($singleUser[0]);
        $this->manager->flush();

        return $this->redirectToRoute('app_dashboard');
    }
}