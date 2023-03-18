<?php

namespace App\ML;
use Phpml\Classification\DecisionTree;
use Phpml\Dataset\ArrayDataset;
use Phpml\Exception\FileException;
use Phpml\Exception\InvalidArgumentException;
use Phpml\Exception\SerializeException;
use Phpml\ModelManager;


class DecisionTressClassifier
{
    protected $model;

    public function __construct()
    {
        $this->model = new DecisionTree();
    }

    /**
     * @throws SerializeException
     * @throws FileException
     * @throws InvalidArgumentException
     */
    public function train($samples, $labels)
    {
        $dataset = new ArrayDataset($samples, $labels);
        $this->model->train($dataset);

        // Save the trained model
        $modelManager = new ModelManager();
        $modelManager->saveToFile($this->model, storage_path('app\decision_tree_model.txt'));
    }

    /**
     * @throws SerializeException
     * @throws FileException
     */
    public function predict($samples)
    {
        // Load the trained model
        $modelManager = new ModelManager();
        $this->model = $modelManager->restoreFromFile(storage_path('app\decision_tree_model.txt'));

        return $this->model->predict($samples);
    }
}
