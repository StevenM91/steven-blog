<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
{
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }


    /**
     * @Route("/article", name="app_article")
     */
    public function newArticle(Request $request, SluggerInterface $slugger): Response
    {
        $article = new Article();

        $newArticle = $this->createForm(ArticleType::class, $article);

        $newArticle->handleRequest($request);

        if ($newArticle->isSubmitted() && $newArticle->isValid()) {

            $article->setPublishedAt(new \DateTime);
            $article->setAuthor($this->getUser()->getFullname());

            // Upload d'image


            $brochureFile = $newArticle->get('picture')->getData();

            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();


                try {
                    $brochureFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new FileException($e);
                }

                $article->setPicture($newFilename);
            }






            $this->manager->persist($article);
            $this->manager->flush();
            return $this->redirectToRoute('app_home');
        }





        return $this->render('article/index.html.twig', [
            'newArticle' => $newArticle->createView(),
        ]);
    }

    /**
     * @Route("/article/single/{id}", name="app_single_article")
     */
    public function viewArticle(Article $article): Response
    {
        // cette fonction va permettre de crée une view quand on clique sur la card cela nous perettra d'être rediriger vers une pages

        // $singleArticle = $this->manager->getRepository(Article::class)->findBy(['id' => $id]);
        // On récupere l'article qui a l'id qui correspond à l'id de l'url


        return $this->render('article/single.html.twig', [
            // "singleArticle" => $singleArticle[0],
            "singleArticle" => $article,
        ]);
    }


    /**
     * @Route("/article/remove/{id}", name="app_remove_article")
     */
    public function deleteArticle($id)
    {
        $singleArticle = $this->manager->getRepository(Article::class)->findBy(['id' => $id]);

        $this->manager->remove($singleArticle[0]);
        $this->manager->flush();

        return $this->redirectToRoute('app_home');


        // recuperer l'article avec l'id qui et passer dans l'url puis le supprimer de la bdd
        // redirection sur la pages home
    }

    /**
     * @Route("/article/update/{id}", name="app_update_article")
     */
    public function updateArticle(Request $request, $id)
    {

        // Je récupere dans la bdd 1 article grâce à l'id
        $singleArticle = $this->manager->getRepository(Article::class)->findBy(['id' => $id]);
        // J'envoie la valuer de picture à null pour éviter tout problème lors de la materialisation du formulaire
        $singleArticle[0]->setPicture(null);

        // materialiser le formulaire et je donne l'article recuperer en bdd à celui ci
        $form = $this->createForm(ArticleType::class, $singleArticle[0]);
        $form->handleRequest($request);


        // Si le formulaire et soumis et en meme temp valide alors j'envoi les modification en bdd
        if ($form->isSubmitted() && $form->isValid()) {


            $this->manager->persist($singleArticle[0]);
            $this->manager->flush();

            return $this->redirectToRoute('app_home');
        }



        return $this->render('article/update.html.twig', [
            'form' => $form->createView(),
            'singleArticle' => $singleArticle[0]
        ]);
    }
}