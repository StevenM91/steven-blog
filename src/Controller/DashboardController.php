<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/admin/dashboard", name="app_dashboard")
     */
    public function index(): Response
    {


        $infoUser = $this->manager->getRepository(User::class)->findAll();



        return $this->render('dashboard/index.html.twig', [
            "users" => $infoUser,
        ]);
    }

    /**
     * @Route("admin/dashboard/remove/{id}", name="app_remove_user")
     */
    // public function deleteUser($id)
    // {
    //     $singleUser = $this->manager->getRepository(User::class)->findBy(['id' => $id]);

    //     $this->manager->remove($singleUser[0]);
    //     $this->manager->flush();

    //     return $this->redirectToRoute('app_dashboard');
    // }


    // public function updateArticle(Request $request, $id)
    // {

    //     // Je récupere dans la bdd 1 article grâce à l'id
    //     $singleUser = $this->manager->getRepository(Article::class)->findBy(['id' => $id]);


    //     $form = $this->createForm(RegisterType::class, $singleUser[0]);
    //     $form->handleRequest($request);


    //     // Si le formulaire et soumis et en meme temp valide alors j'envoi les modification en bdd
    //     if ($form->isSubmitted() && $form->isValid()) {


    //         $this->manager->persist($singleUser[0]);
    //         $this->manager->flush();

    //         return $this->redirectToRoute('app_home');
    //     }



    //     return $this->render('article/update.html.twig', [
    //         'form' => $form->createView(),
    //         'singleArticle' => $singleArticle[0]
    //     ]);
    // }
}