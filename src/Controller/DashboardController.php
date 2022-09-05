<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}