<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Uid\Uuid;

class ImagePredictionApiController extends AbstractController
{
    public function predictAndRecommend(Request $request): Response
    {
        set_time_limit(300); // 300 seconds

        // Récupérer le fichier image à partir de la requête
        $file = $request->files->get('image');

        // Vérifier si un fichier a été envoyé
        if (!$file || !$file->isValid()) {
            return $this->json(['error' => 'No image file provided or file is invalid'], Response::HTTP_BAD_REQUEST);
        }

        // Générer un nom de fichier unique pour éviter les conflits
        $fileName = Uuid::v4() . '.' . $file->getClientOriginalExtension();

        // Définir le chemin où enregistrer le fichier
        $uploadDir = $this->getParameter('upload_directory');
        $filePath = $uploadDir . '/' . $fileName;

        // Déplacer le fichier téléchargé vers le répertoire d'upload
        try {
            $file->move($uploadDir, $fileName);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Failed to move uploaded file'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Chemin absolu du fichier téléchargé
        $imagePath = $filePath;
        error_log('Image URL: ' . $imagePath);

        // Exécuter le script de prédiction
        $pythonPredictionScriptPath = $this->getParameter('python_script_path');
        $command = sprintf('python %s %s', escapeshellarg($pythonPredictionScriptPath), escapeshellarg($imagePath));
        error_log('Prediction command: ' . $command);
        $process = Process::fromShellCommandline($command, null, ['PYTHONIOENCODING' => 'utf-8']);
        $process->run();

        if (!$process->isSuccessful()) {
            return $this->json(['error' => 'Error executing prediction Python script', 'details' => $process->getErrorOutput()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $predictionOutput = $process->getOutput();
        $lines = explode("\n", trim($predictionOutput));
        $prediction = end($lines);
        error_log('Prediction: ' . $prediction);

        // Exécuter le script de recommandation
        $distanceThreshold = 0.5; // Valeur par défaut
        $pythonRecommendationScriptPath = 'C:/Manarwork/Frontend/AI/recommendation_script.py'; // Assurez-vous de mettre le bon chemin

        $recommendationCommand = sprintf('python %s %s %s', escapeshellarg($pythonRecommendationScriptPath), escapeshellarg($imagePath), escapeshellarg($distanceThreshold));
        error_log('Recommendation command: ' . $recommendationCommand);
        $recommendationProcess = Process::fromShellCommandline($recommendationCommand, 'C:/Manarwork/Frontend/AI', ['PYTHONIOENCODING' => 'utf-8']);
        $recommendationProcess->setTimeout(300);
        $recommendationProcess->run();

        if (!$recommendationProcess->isSuccessful()) {
            return $this->json(['error' => 'Error executing recommendation Python script', 'details' => $recommendationProcess->getErrorOutput()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $recommendationOutput = $recommendationProcess->getOutput();
        $cleanOutput = preg_replace('/[^\PC\s]/u', '', $recommendationOutput);

        preg_match_all('/"[^"]*\.webp"/', $cleanOutput, $matches);
        $recommendedImages = $matches[0];

        

        $cleanedRecommendedImages = array_map(function($path) {
            return trim($path, '"');
        }, $recommendedImages);

        $predictionResponse = [
            'prediction' => $prediction ?: "Aucune prédiction disponible",
            'recommended_images' => $cleanedRecommendedImages ?: []
        ];
        
        return $this->json($predictionResponse);
    }
}
