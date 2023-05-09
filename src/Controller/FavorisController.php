<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Entity\User;
use App\Repository\UserRepository;
use Composer\DependencyResolver\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavorisController extends AbstractController
{
    #[Route('/favoris', name: 'app_favoris')]
    public function index(): Response
    {
        return $this->render('favoris/favoris.html.twig', [
            'controller_name' => 'Favoris',
        ]);
    }

    #[Route('/favoris/new/{id}', name: 'app_favoris_new')]
    public function addFavoris(Restaurant $restaurant, UserRepository $userRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $user->addFavorites($restaurant);

        $userRepository->save($user, true);

        return $this->redirectToRoute('app_favoris');
    }

    #[Route('/favoris/remove/{id}', name: 'app_favoris_remove')]
    public function removeFavoris(Restaurant $restaurant, UserRepository $userRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $user->removeFavorites($restaurant);

        $userRepository->save($user, true);

        return $this->redirectToRoute('app_favoris');
    }
}