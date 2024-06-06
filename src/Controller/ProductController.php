<?php

namespace App\Controller;

use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/allFourniture', name: 'allFourniture')]
    public function index(DocumentManager $documentManager): Response
    {
        $cursor = $documentManager
            ->getDocumentCollection(Product::class)
            ->find()
            ;

        return $this->json([
            'innoDeco_db' => $cursor->toArray(),
        ]);
    }
}