<?php


namespace Hypocenter\LaravelSignature\Define\Repositories;


use Hypocenter\LaravelSignature\Define\Define;
use Hypocenter\LaravelSignature\Exceptions\InvalidArgumentException;
use Hypocenter\LaravelSignature\Define\Repository;
use Hypocenter\LaravelSignature\Define\IntoDefine;
use Hypocenter\LaravelSignature\Interfaces\Configurator;
use Illuminate\Database\Eloquent\Model;

class ModelRepository implements Repository, Configurator
{
    private $model;

    public function findByAppId($appId): ?Define
    {
        $cls = $this->model;
        /** @var IntoDefine $model */
        $model = $cls::query()->find($appId);
        if ($model === null) {
            return null;
        }

        return $model->intoSignatureDefine();
    }

    public function setConfig(array $config): void
    {
        $this->model = $config['model'] ?? null;
        if (empty($this->model)) {
            throw new InvalidArgumentException('The model must not be null');
        }
        if (!is_subclass_of($this->model, IntoDefine::class, true)) {
            throw new InvalidArgumentException('The model must implement the IntoDefine interface.');
        }
        if (!is_subclass_of($this->model, Model::class, true)) {
            throw new InvalidArgumentException('The model must be subclass of Model');
        }
    }

}