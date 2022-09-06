<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{

    public function __construct(EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHash)
    {
        $this->manager = $manager; // on injecte l'entity manager dans le constructeur
        $this->passwordHash = $passwordHash; // On injecte le service de hashage de mot de passe
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function index(Request $request): Response // Requeste est une classe qui permet en l'occurence de récuperer les données d'un formulaire
    {

        // 1- je vais instancier un nouvelle user (importation de la classe User)
        $user = new User();
        // 2- je vais materialisé un formulaire (importation de la classe Registertype)
        $form = $this->createForm(RegisterType::class, $user); // creteForm() est une méthode de AbstractController qui permet de materialisé un formulaire
        $form->handleRequest($request); // handleRequest() est une méthode de AbstractController qui permet de traiter la requete
        // 3- je vais traiter le formulaire avec des conditions
        if ($form->isSubmitted() && $form->isValid()) {
            //  dd($form->getData()); // La fonction dd() c'est comme le vardump() ! pour débuger
            $user->setCreatedAt(new \DateTime); // on set la date de création de l'utilisateur

            // faire en sorte d'encoder le mot de passe avant l'envoie en bdd
            $user->setFullname($user->getLastname() . ' ' . $user->getFirstname());
            $passwordEncod = $this->passwordHash->hashPassword($user, $user->getPassword()); // on hash le mot de passe
            $user->setPassword($passwordEncod); // on set le mot de passe de l'utilisateur
            // $passwordEncod = $this->passwordHash->hashPassword($user, $form->get('password')->getData()); // Autre méthode
            // dd($passwordEncod);


            // 4- je vais enregistrer l'utilisateur en base de données
            $this->manager->persist($user); // on prépare les données à être envoyer ($user)
            $this->manager->flush();
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(), //createView() est une méthode de AbstractController qui permet de créer une vue de formulaire
        ]);
    }
}