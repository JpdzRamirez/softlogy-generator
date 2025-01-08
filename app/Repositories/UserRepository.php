<?php

namespace App\Repositories;


use App\Models\User;


class UserRepository 
{
    protected $model;

    /**
     * A fresh builder instance should contain a blank product object, which is
     * used in further assembly.
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }
    /**
     * Summary of reset
     * @return void
     */
    public function reset(): void
    {
        $this->model = new User();
    }
    /**
     * 
     * @param int $id
     * @return TModel
     */
    public function findById(int $id){
        return $this->model->findOrFail($id);
    }
    /**
     * /
     * @param string $name
     * @return TModel
     */
    public function findByName(string $name)
    {
        return $this->model->where('name', $name)->firstOrFail();
    }
    /**
     *  Create an User on local database
     * @param array $data
     * @return TModel
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }
    /**
     *  Update information of an user
     * @param int $id
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function update(int $id, array $data)
    {
        $user = $this->model->findOrFail($id);
        $user->update($data);
        return $user;
    }
    /**
     *  Delete user from database
     * @param int $id
     * @return void
     */
    public function delete(int $id)
    {
        $presentation = $this->model->findOrFail($id);
        $presentation->delete();
    }
    
    /**
     * Obtiene todos los usuarios.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model->all();
    }

}