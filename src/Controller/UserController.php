<?php
namespace App\Controller;

use App\Document\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UserController extends AbstractController
{
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function createUser(Request $request, DocumentManager $dm): Response
    {
        // Récupérer les données de la requête JSON
        $data = json_decode($request->getContent(), true);

        // Vérifier si l'utilisateur existe déjà
        $existingUser = $dm->getRepository(User::class)->findOneBy(['username' => $data['username']]);

        if ($existingUser) {
            // Retourner une erreur car l'utilisateur existe déjà
            return $this->json(['message' => 'Cet utilisateur existe déjà'], 400);
        }

        // Créer une nouvelle instance de User
        $user = new User();
        $user->setFirstName($data['firstName'] ?? '');
        $user->setLastName($data['lastName'] ?? '');
        $user->setUsername($data['username'] ?? '');
        $user->setPassword($data['password'] ?? '');
        $user->setBirthDate(new \DateTime($data['birthDate']) ?? '');
        $user->setGender($data['gender'] ?? '');
        $user->setRole("ROLE_USER");

        // Persister l'utilisateur dans la base de données
        $dm->persist($user);
        $dm->flush();

        // Retourner une réponse JSON
        return $this->json(['message' => 'Utilisateur créé avec succès']);
    }
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/api/users', name: 'update_user', methods: ['PUT'])]
    public function updateUser(Request $request, DocumentManager $dm): Response
{
    // Récupérer les données de la requête JSON
    $data = json_decode($request->getContent(), true);

    // Vérifier si l'utilisateur est authentifié
    if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
        return $this->json(['message' => 'Accès non autorisé'], 403);
    }

    // Récupérer l'utilisateur à modifier
    $user = $dm->getRepository(User::class)->findOneBy(['username' => $data['username']]);

    // Vérifier si l'utilisateur existe
    if (!$user) {
        return $this->json(['message' => 'Utilisateur non trouvé'], 404);
    }

    // Vérifier si l'utilisateur authentifié est autorisé à modifier l'utilisateur
    if ($user->getId() !== $this->getUser()->getId()) {
        return $this->json(['message' => 'Accès non autorisé à cet utilisateur'], 403);
    }

    // Mettre à jour les champs de l'utilisateur
    $user->setFirstName($data['firstName'] ?? $user->getFirstName());
    $user->setLastName($data['lastName'] ?? $user->getLastName());
    $user->setUsername($data['lastName'] ?? $user->getUsername());
    $user->setPassword($data['password'] ?? $user->getPassword());
    $user->setBirthdaydate(new \DateTime($data['birthDate']) ?? $user->getBirthDate());
    $user->setGender($data['gender'] ?? $user->getGender());
    $user->setRole($data['role'] ?? $user->getRole() ?? 'ROLE_USER');
    
    // Persister les changements dans la base de données
    $dm->flush();

    // Retourner une réponse JSON
    return $this->json(['message' => 'Utilisateur mis à jour avec succès']);
}
   
}
