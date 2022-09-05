<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController // class HomeController qui hérite de la classe AbstractController
{
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }


    // ceci est une annotation elle est lu au même titre que le reste

    /**
     * @Route("/", name="app_home")  
     */
    public function index(): Response // ici on retourne une reponse donc un return
    {

        // Va en BDD est récupere moi les donner de article 
        $articles = $this->manager->getRepository(Article::class)->findAll(); // findAll() -> recupere moi tout
        // dd($articles);

        return $this->render('home/index.html.twig', [ // la fonction render renvoie une vus àl'internaute
            'articles' => $articles, // ici on passe des variable à la vue pour y acceder dans celle ci

        ]);
    }

    /**
     * @Route("/redirect", name="app_redirect")  
     */
    public function redirectToUser()
    {

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_dashboard');
        } else {
            return $this->redirectToRoute('app_profil');
        }
    }

    // La fonction $this->getUser() permet de récuperer l'utilisateur connecté coter php
    // on peut aussi faire comme sa 
    //     if ($this->getUser()->getRole() == 'Admin') {
    //         return $this->redirectToRoute('app_dashboard');
    //     } else {
    //         return $this->redirectToRoute('app_profil');
    //     }
    // }
}