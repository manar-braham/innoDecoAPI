<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class RecommendationController extends AbstractController
{
    /**
     * @Route("/api/recommendations", name="recommendations_post", methods={"POST"})
     */
    public function recommend(Request $request)
    {
        set_time_limit(300); // 300 secondes

        $data = json_decode($request->getContent(), true);
        $imgPath = $data['image'] ?? '';
        $distanceThreshold = $data['distance_threshold'] ?? 0.5;

        if (!$imgPath) {
            return new JsonResponse(['error' => 'Image path is required'], 400);
        }

        // Log the received parameters
        error_log("Image Path: " . $imgPath);
        error_log("Distance Threshold: " . $distanceThreshold);

        // Log current working directory
        $currentDir = getcwd();
        error_log("Current working directory: " . $currentDir);

        // Run the Python script
        $process = new Process(['python', 'recommendation_script.py', $imgPath, (string)$distanceThreshold]);
        $process->setWorkingDirectory('C:/Manarwork/Frontend/AI');
        $process->setEnv(['PYTHONIOENCODING' => 'utf-8']); // Définit l'encodage de l'environnement Python
        $process->setTimeout(300); // Augmenter le délai d'attente à 300 secondes

        $process->run();

        // Log output and error output
        $processOutput = $process->getOutput();
        $processErrorOutput = $process->getErrorOutput();
        error_log("Process Output: " . $processOutput);
        error_log("Process Error Output: " . $processErrorOutput);

        if (!$process->isSuccessful()) {
            return new JsonResponse(['error' => 'Process failed', 'details' => $processErrorOutput], 500);
        }

        $cleanOutput = preg_replace('/[^\PC\s]/u', '', $processOutput);

        // Try to extract image paths from the output
        preg_match_all('/"[^"]*\.webp"/', $cleanOutput, $matches);
        $recommendedImages = $matches[0];

        if (empty($recommendedImages)) {
            return new JsonResponse(['error' => 'No recommended images found'], 500);
        }

        // Supprimer les caractères "\" au début et à la fin de chaque chemin d'accès
        $cleanedRecommendedImages = array_map(function($path) {
            return trim($path, '"');
        }, $recommendedImages);

        // Renvoyer la liste nettoyée des chemins d'accès
        return new JsonResponse($cleanedRecommendedImages);
    }
}
