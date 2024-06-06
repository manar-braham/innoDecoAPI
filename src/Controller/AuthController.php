<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


use Doctrine\ODM\MongoDB\DocumentManager; // Assurez-vous d'importer la classe DocumentManager depuis le bon namespace

class AuthController extends AbstractController
{
    private $jwtManager;
    private $documentManager;

    public function __construct(JWTTokenManagerInterface $jwtManager, DocumentManager $documentManager)
    {
        $this->jwtManager = $jwtManager;
        $this->documentManager = $documentManager; // Injectez le DocumentManager dans le constructeur
    }

    public function login(Request $request): Response
    {
        // Récupérez les informations d'identification de la demande
        $credentials = $request->request->all();
        dump($credentials);
        // Recherchez l'utilisateur correspondant dans la base de données MongoDB
        $userRepository = $this->documentManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['username' => $credentials['username']]);

        // Vérifiez si l'utilisateur existe et si le mot de passe est correct
        if (!$user || !password_verify($credentials['password'], $user->getPassword())) {
            // Retournez une réponse d'erreur si les informations d'identification sont invalides
            return $this->json(['message' => 'Identifiants invalides'], Response::HTTP_UNAUTHORIZED);
        }

        // Générez le token JWT pour l'utilisateur
        $token = $this->jwtManager->create($user);
       
        // Pour vérifier les informations du token
        $jwtPayload = $this->jwtManager->parse($token);
        dump($jwtPayload);

        // Retournez le token JWT et les informations de l'utilisateur dans la réponse
        return $this->json([
            'token' => $token,
        ]);
    }
    public function getUserInfo(UserInterface $user): JsonResponse
    {
        return $this->json([
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'id' => $user->getId(),
            'birthDate'=>$user->getBirthDate(),
            'gender'=>$user->getGender()
        ]);
    }
    public function logout(TokenStorageInterface $tokenStorage)
    {
        // Supprimer le token JWT côté client en le définissant comme expiré
        $token = $tokenStorage->getToken();
        if ($token instanceof TokenInterface) {
            $token->setAuthenticated(false);
        }

        return $this->json(['message' => 'Déconnexion réussie']);
    }
}