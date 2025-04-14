<?php

namespace App\Repositories;


use App\Models\SqlConfig;


class SqlConfigRespository 
{
    protected $model;

    /**
     * A fresh builder instance should contain a blank product object, which is
     * SqlConfig in further assembly.
     */
    public function __construct(SqlConfig $model)
    {
        $this->model = $model;
    }
    
    /**
     * Summary of reset
     * @return void
     */
    public function reset(): void
    {
        $this->model = new SqlConfig();
    }

    /**
     * Find a SqlConfig by their ID.
     * 
     * @param int $id
     * @return SqlConfig
     */
    public function findById(int $id){
        return $this->model->findOrFail($id);
    }

    /**
     * Find a SqlConfig by their name.
     * 
     * @param string $name
     * @return SqlConfig
     */
    public function findByName(string $name)
    {
        return $this->model->where('name', $name)->first();
    }

    /**
     * Create a SqlConfig in the local database.
     * 
     * @param array $data
     * @return SqlConfig
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update SqlConfig information.
     * 
     * @param int $id
     * @param array $data
     * @return SqlConfig
     */
    public function update(int $id, array $data)
    {
        $sqlConfig = $this->model->findOrFail($id);
        $sqlConfig->update($data);
        return $sqlConfig;
    }

    /**
     * Delete a SqlConfig from the database.
     * 
     * @param int $id
     * @return void
     */
    public function delete(int $id)
    {
        $sqlConfig = $this->model->findOrFail($id);
        $sqlConfig->delete();
    }
    
    /**
     * Obtiene todos los SqlConfig.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model->all();
    }

}