<?php

    namespace App\Controller;

    use App\Entity\Image;
    use App\Form\RestaurantType;
    use App\Repository\ImageRepository;
    use Symfony\Component\Security\Http\Attribute\IsGranted;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\File\Exception\FileException;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use App\Repository\RestaurantRepository;
    use App\Entity\Restaurant;
    use Symfony\Component\String\Slugger\SluggerInterface;

    #[Route(path: '/restaurant', name: 'restaurant_')]
    #[IsGranted('ROLE_OWNER')]
class RestaurantController extends AbstractController
{
    #[Route(path: '/', name: 'index')]
    public function index(): Response
    {
        return $this->render('restaurant/index.html.twig');
    }

    private function handleImageUpload(
        array $files,
        Restaurant $restaurant,
        SluggerInterface $slugger,
        ImageRepository $imageRepository
    ): void {
        foreach ($files as $file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '_' . uniqid() . '_' . $file->guessExtension();

            try {
                $file->move(
                    $this->getParameter('restaurant_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
            }

            $image = new Image();
            $image->setUrl('uploads/restaurant/' . $newFilename);
            $imageRepository->save($image);
            $restaurant->addImage($image);
        }
    }

    #[Route(path: '/new', name: 'new')]
    public function new(
        Request $request,
        RestaurantRepository $restaurantRepository,
        SluggerInterface $slugger,
        ImageRepository $imageRepository,
    ): Response {
        $restaurant = new Restaurant();
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $files = $form['images']->getData();
            $restaurant->setUser($this->getUser());

            if ($files) {
                $this->handleImageUpload($files, $restaurant, $slugger, $imageRepository);
            }
            $restaurantRepository->save($restaurant, true);

            return $this->redirectToRoute('restaurant_show', ['id' => $restaurant->getId()]);
        }

        return $this->render('restaurant/new.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form,
        ]);
    }

    #[Route(path: '/edit/{id}', name: 'edit')]
    public function edit(
        Request $request,
        Restaurant $restaurant,
        RestaurantRepository $restaurantRepository,
        SluggerInterface $slugger,
        ImageRepository $imageRepository,
    ): Response {
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $files = $form['images']->getData();
            $restaurant->setUser($this->getUser());

            if ($files) {
                $this->handleImageUpload($files, $restaurant, $slugger, $imageRepository);
            }
            $restaurantRepository->save($restaurant, true);

            return $this->redirectToRoute('restaurant_show', ['id' => $restaurant->getId()]);
        }

        return $this->render('restaurant/edit.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'show', methods: ['GET'])]
    public function show(Restaurant $restaurant): Response
    {
        return $this->render('restaurant/show.html.twig', [
            'restaurant' => $restaurant,
        ]);
    }
}
