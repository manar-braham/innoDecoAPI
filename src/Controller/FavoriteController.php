<?php

namespace App\Controller;

use App\Document\Favorite;
use App\Document\Product;
use App\Document\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class FavoriteController extends AbstractController
{
    #[Route('api/add_to_favorite', name: 'favorite_add', methods: ['POST'])]
    #[Security('is_granted("ROLE_USER")')]

    public function addToFavorite(Request $request, DocumentManager $dm): Response
    {
        $data = json_decode($request->getContent(), true);

        $userId = $data['userId'];
        $productId = $data['productId'];
        $isFavorite = $data['favorite'];
        // Récupérer l'utilisateur et le produit à partir de leur ID
        $user = $dm->getRepository(User::class)->find($userId);
        $product = $dm->getRepository(Product::class)->find($productId);

        // Vérifier si l'utilisateur et le produit existent
        if (!$user) {
            return new Response('Utilisateur non trouvé', Response::HTTP_NOT_FOUND);
        }

        if (!$product) {
            return new Response('Produit non trouvé', Response::HTTP_NOT_FOUND);
        }

        // Créer une instance de Favorite en fournissant les arguments requis
        $favorite = new Favorite($userId, $productId, $isFavorite);

        // Enregistrer dans la base de données
        $dm->persist($favorite);
        $dm->flush();

        return new Response('Favorite added', Response::HTTP_CREATED);
    }

    #[Route('api/remove_from_favorite', name: 'favorite_remove', methods: ['POST'])]
    #[Security('is_granted("ROLE_USER")')]

    public function removeFromFavorite(Request $request, DocumentManager $dm): Response
    {
        $data = json_decode($request->getContent(), true);

        $userId = $data['userId'];
        $productId = $data['productId'];

        // Recherche de l'élément dans la base de données
        $favorite = $dm->getRepository(Favorite::class)->findOneBy(['userId' => $userId, 'productId' => $productId]);

        if (!$favorite) {
            return new Response('Favorite not found', Response::HTTP_NOT_FOUND);
        }

        // Suppression de l'élément des favoris
        $dm->remove($favorite);
        $dm->flush();

        return new Response('Favorite removed', Response::HTTP_OK);
    }
    #[Route('api/get_favorite_Product', name: 'favorite_get', methods: ['POST'])]
    #[Security('is_granted("ROLE_USER")')]
    public function getFavorites(Request $request, DocumentManager $documentManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $userId = $data['userId'];
        // Récupérer les favoris pour l'utilisateur donné
    $favoritesRepository = $documentManager->getRepository(Favorite::class);
    $favorites = $favoritesRepository->findBy(['userId' => $userId]);

    // Construire la liste des productId
    $productIds = [];
    foreach ($favorites as $favorite) {
        $productIds[] = $favorite->getProductId();
    }

    // Retourner la liste des productId sous forme de JSON
    return $this->json($productIds);

        
    }
}