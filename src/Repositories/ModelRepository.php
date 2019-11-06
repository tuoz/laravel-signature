<?php


namespace Hypocenter\LaravelSignature\Repositories;


use Hypocenter\LaravelSignature\Interfaces\Repository;
use Hypocenter\LaravelSignature\Interfaces\ToDefine;
use Hypocenter\LaravelSignature\Define;

class ModelRepository implements Repository
{
    private $model;

    public function findByAppId($appId): Define
    {
        $cls = $this->model;
        $model = $cls::query()->find($appId);

        assert($model instanceof ToDefine);

        return $model->toDefine();
    }

    public function setConfig(array $config)
    {
        if (isset($config['model'])) {
            $this->model = $config['model'];
        }
    }

}