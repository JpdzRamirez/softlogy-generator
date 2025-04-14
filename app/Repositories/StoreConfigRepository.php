<?php

namespace App\Repositories;


use App\Models\StoreConfig;


class StoreConfigRepository 
{
    protected $model;

    /**
     * A fresh builder instance should contain a blank product object, which is
     * StoreConfig in further assembly.
     */
    public function __construct(StoreConfig $model)
    {
        $this->model = $model;
    }
    
    /**
     * Summary of reset
     * @return void
     */
    public function reset(): void
    {
        $this->model = new StoreConfig();
    }

    /**
     * Find a StoreConfig by their ID.
     * 
     * @param int $id
     * @return StoreConfig
     */
    public function findById(int $id){
        return $this->model->findOrFail($id);
    }

    /**
     * Find a StoreConfig by their name.
     * 
     * @param string $name
     * @return StoreConfig
     */
    public function findByName(string $name)
    {
        return $this->model->where('name', $name)->first();
    }

    /**
     * Create a StoreConfig in the local database.
     * 
     * @param array $data
     * @return StoreConfig
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update StoreConfig information.
     * 
     * @param int $id
     * @param array $data
     * @return StoreConfig
     */
    public function update(int $id, array $data)
    {
        $storeConfig = $this->model->findOrFail($id);
        $storeConfig->update($data);
        return $storeConfig;
    }

    /**
     * Delete a StoreConfig from the database.
     * 
     * @param int $id
     * @return void
     */
    public function delete(int $id)
    {
        $storeConfig = $this->model->findOrFail($id);
        $storeConfig->delete();
    }
    
    /**
     * Obtiene todos los StoreConfig.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model->all();
    }

}