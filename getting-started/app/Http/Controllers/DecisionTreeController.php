<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use App\ML\DecisionTreeClassifier;
use Illuminate\Http\Response;
use Phpml\Exception\FileException;
use Phpml\Exception\InvalidArgumentException;
use Phpml\Exception\SerializeException;
use Phpml\ModelManager;

class DecisionTreeController extends Controller
{
    /**
     * @return Application|ResponseFactory|Response
     * @throws FileException
     * @throws InvalidArgumentException
     * @throws SerializeException
     */
    public function trainDecisionTree(): Response|Application|ResponseFactory
    {
        $samples = ["I'm so happy!", "Yay, I got a job!", "This is terrible", "I'm so sad", "Yay, it's raining!", "I hate it", "I enjoy something"];

        // Preprocess samples
        $preprocessed_samples = [];
        foreach ($samples as $sample) {
            // Remove punctuation
            $sample = preg_replace("/[^a-zA-Z\s]/", "", $sample);
            // Add preprocessed sample to array
            $preprocessed_samples[] = $sample;
        }

        // Check if preprocessed samples array is not empty and is an array
        if (!empty($preprocessed_samples) && is_array($preprocessed_samples)) {
            $labels = ["positive", "positive", "negative", "negative", "neutral", "negative", "positive"];

            $classifier = new DecisionTreeClassifier();
            $classifier->train($preprocessed_samples, $labels);

            return response('Decision tree model trained and saved.');
        } else {
            return response('Error: preprocessed samples is empty or not an array.');
        }
    }


    /**
     * @throws SerializeException
     * @throws FileException
     */
    public function predictDecisionTree(): string
    {
        // Load the trained model from the file
        $modelManager = new ModelManager();
        $decisionTreeClassifier = $modelManager->restoreFromFile(storage_path('app/decision_tree_model.txt'));

        // Define the sentence to predict its label
        $sentence = "I enjoy video games";

        // Preprocess the sentence
        $sentence = preg_replace("/[^a-zA-Z\s]/", "", $sentence);

        // Predict the label of the sentence
        $predictedLabel = $decisionTreeClassifier->predict([$sentence]);

        // Print the predicted label
        return "The predicted label for the sentence '$sentence' is: $predictedLabel";
    }

}
