<?php

namespace App\Http\Controllers;

use Phpml\FeatureExtraction\StopWords\English;
use Phpml\FeatureExtraction\TfidfTransformer;
use Phpml\Tokenization\WhitespaceTokenizer;
use Phpml\Classification\SVC;
use Phpml\SupportVectorMachine\Kernel;

class TextClassificationController
{
    public function example()
    {
        // Charger les données d'apprentissage
        $trainSamples = [
            ['This is a positive sentence', 'positive'],
            ['This is a negative sentence', 'negative'],
            ['This is another positive sentence', 'positive'],
            ['This is another negative sentence', 'negative']
        ];
        // Charger les données de test
        $testSamples = [
            'This is a positive test sentence',
            'This is a negative test sentence'
        ];

        // Définir le tokenizer et le transformateur TF-IDF
        $tokenizer = new WhitespaceTokenizer();
        $englishStopWords = new English();
        $tfIdfTransformer = new TfidfTransformer(array($englishStopWords));

        // Appliquer la tokenization et la transformation TF-IDF sur les données d'apprentissage
        $transformedSamples = [];
        foreach ($trainSamples as $sample) {
            $transformedSamples[] = [
                $tfIdfTransformer->transform($tokenizer->tokenize($sample[0])), // échantillon transformé
                $sample[1] // label de classe correspondant à l'échantillon
            ];
        }

        // Initialiser le classificateur SVM
        $classifier = new SVC(Kernel::LINEAR, $cost = 1000);

        // Entraîner le classificateur SVM sur les données transformées
        $classifier->train($transformedSamples, $sample);

        // Appliquer la tokenization et la transformation TF-IDF sur les données de test
        $transformedTestSamples = [];
        foreach ($testSamples as $sample) {
            $transformedTestSamples[] = $tfIdfTransformer->transform($tokenizer->tokenize($sample));
        }

        // Prédire les classes des données de test en utilisant le classificateur SVM entraîné
        $predictedLabels = $classifier->predict($transformedTestSamples);

        // Afficher les prédictions
        foreach ($predictedLabels as $predictedLabel) {
            echo $predictedLabel . "\n";
        }
    }
}
