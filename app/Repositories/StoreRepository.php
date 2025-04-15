<?php

namespace App\Repositories;


use App\Models\Store;


class StoreRepository 
{
    protected $model;

    /**
     * A fresh builder instance should contain a blank product object, which is
     * Store in further assembly.
     */
    public function __construct(Store $model)
    {
        $this->model = $model;
    }

    /**
     * Summary of reset
     * @return void
     */
    public function reset(): void
    {
        $this->model = new Store();
    }

    /**
     * Find a Store by their ID.
     * 
     * @param int $id
     * @return Store
     */
    public function findById(int $id){
        return $this->model->findOrFail($id);
    }

    /**
     * Find a Store by their name.
     * 
     * @param string $id
     * @return Store
     */
    public function findByUser_Id(string $id)
    {
        return $this->model->where('user_id', $id)->first();
    }

    /**
     * Create a Store in the local database.
     * 
     * @param array $data
     * @return Store
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update Store information.
     * 
     * @param int $id
     * @param array $data
     * @return Store
     */
    public function update(int $id, array $data)
    {
        $store = $this->model->findOrFail($id);
        $store->update($data);
        return $store;
    }

    /**
     * Delete a Store from the database.
     * 
     * @param int $id
     * @return void
     */
    public function delete(int $id)
    {
        $store = $this->model->findOrFail($id);
        $store->delete();
    }
    
    /**
     * Obtiene todos los Store.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model->all();
    }

}