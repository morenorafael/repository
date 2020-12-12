<?php

namespace MorenoRafael\Repository;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Class Repository
 * @package MorenoRafael\Repository
 */
abstract class Repository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var string
     */
    protected $baseKey;

    /**
     * @param Collection|null $all
     * @return Collection
     */
    public function all(Collection $all = null): Collection
    {
        return Cache::rememberForever("{$this->baseKey}.all", function () use ($all) {
            if (!is_null($all)) {
                return $all;
            }

            return $this->model::all();
        });
    }

    /**
     * @param int $id
     * @return Model
     */
    public function find(int $id): Model
    {
        return Cache::rememberForever("{$this->baseKey}.find.{$id}", function () use ($id) {
            if (Cache::has("{$this->baseKey}.all")) {
                return $this->all()->where('slug', $id)->first();
            }

            return $this->model::find($id);
        });
    }

    /**
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        $model = $this->model::create($data);

        $this->pushToAll($model);

        return Cache::rememberForever("{$this->baseKey}.find.{$model->id}", function () use ($model) {
            return $model;
        });
    }

    /**
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update(int $id, array $data): Model
    {
        $model = $this->model::find($id);
        $model->update($data);
        $model = $model->fresh();

        if ($this->deleteModelCache($id, $model)) {
            $this->pushToAll($model);

            return Cache::rememberForever("{$this->baseKey}.find.{$model->id}", function () use ($model) {
                return $model;
            });
        }
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $model = $this->model::find($id);

        if ($model->delete()) {

            return $this->deleteModelCache($id, $model);
        }

        return false;
    }

    /**
     * @param int $id
     * @param Model $model
     * @return bool
     */
    protected function deleteModelCache(int $id, Model $model): bool
    {
        $all = $this->all()->reject(function (Model $model) use ($id) {
            return $model->id === $id;
        });

        Cache::forget("{$this->baseKey}.all");
        $this->all($all->sortBy('id'));
        Cache::forget("{$this->baseKey}.find.{$model->id}");

        return true;
    }

    /**
     * @param Model $model
     */
    protected function pushToAll(Model $model): void
    {
        $all = $this->all()->push($model);
        Cache::forget("{$this->baseKey}.all");
        $this->all($all->sortBy('id'));
    }
}
