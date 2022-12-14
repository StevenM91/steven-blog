<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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
        $notification = new Notification();

        $newArticle = $this->createForm(ArticleType::class, $article);

        $newArticle->handleRequest($request);



        if ($newArticle->isSubmitted() && $newArticle->isValid()) {

            $article->setPublishedAt(new \DateTime);
            $article->setAuthor($this->getUser());

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

            // nouvelle notification pour l'auteur disant "Votre article a ??tait publier"
            $notification->setUser($this->getUser());
            $notification->setCreatedAt(new \DateTime);
            $notification->setMessage("Felicitation ! Votre article vient d'??tre publier");


            $admins = $this->manager->getRepository(User::class)->findByRole("ROLE_ADMIN"); // r??cup??ration des admin via la m??thode findByRole() des UserRepository que l'on ?? cr??e

            //ENVOYER LES NOTIFICATION UNIQUEMENT AUX ADMIN
            foreach ($admins as $admin) {
                // On boucle sur les admins pour leur envoyer une notification
                $notification->setUser($admin);
                $notification->setCreatedAt(new \DateTime);
                $notification->setMessage("chere administrateur, un article vient d'??tre publier");
            }


            $this->manager->persist($notification);
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
    public function viewArticle(Article $article, Request $request, $id): Response
    {
        // cette fonction va permettre de cr??e une view quand on clique sur la card cela nous perettra d'??tre rediriger vers une pages

        // $singleArticle = $this->manager->getRepository(Article::class)->findBy(['id' => $id]);
        // On r??cupere l'article qui a l'id qui correspond ?? l'id de l'url
        $newComment = new Comment();

        $formComment = $this->createForm(CommentType::class, $newComment);


        $formComment->handleRequest($request);

        if ($formComment->isSubmitted() && $formComment->isValid()) {
            $newComment->setPublishedAt(new \DateTime);
            $newComment->setArticle($article);
            $newComment->setUser($this->getUser());

            $this->manager->persist($newComment);
            $this->manager->flush();
            return $this->redirectToRoute('app_single_article', ['id' => $id]);
        }



        // Ajouter un nouveau commentaire en relation avec l'article et l'utilisateur connecte


        return $this->render('article/single.html.twig', [
            // "singleArticle" => $singleArticle[0],
            "singleArticle" => $article,
            "formComment" => $formComment->createView(),

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

        // Je r??cupere dans la bdd 1 article gr??ce ?? l'id
        $singleArticle = $this->manager->getRepository(Article::class)->findBy(['id' => $id]);
        // J'envoie la valuer de picture ?? null pour ??viter tout probl??me lors de la materialisation du formulaire
        $singleArticle[0]->setPicture(null);

        // materialiser le formulaire et je donne l'article recuperer en bdd ?? celui ci
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