<?php

namespace App\Controller;


use App\Form\RestaurantType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RestaurantRepository;
use App\Entity\Restaurant;




#[Route(path: '/restaurant', name: 'app_restaurant_')]
class RestaurantController extends AbstractController
{
    #[Route(path: '/', name: 'index')]
    public function index(): Response
    {
       return $this->render('restaurant/index.html.twig');
    }

    #[Route(path: '/new', name: 'new')]
    public function new(Request $request, RestaurantRepository $restaurantRepository): Response
    {
        $restaurant = new Restaurant();
        $form = $this->createForm(RestaurantType::class, $restaurant);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $restaurantRepository->save($restaurant, true);

            return $this->redirectToRoute('app_restaurant_index');
        }

        return $this->renderForm('restaurant/new.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form,
        ]);
    }

}

