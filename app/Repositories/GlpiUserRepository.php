<?php

namespace App\Repositories;


use App\Models\GlpiUser;


class GlpiUserRepository 
{
    protected $model;

    /**
     * A fresh builder instance should contain a blank product object, which is
     * used in further assembly.
     */
    public function __construct(GlpiUser $model)
    {
        $this->model = $model;
    }
    public function reset(): void
    {
        $this->model = new GlpiUser();
    }
    /**
     * All production steps work with the same product instance.
     */
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $user = $this->model->findOrFail($id);
        $user->update($data);
        return $user;
    }

    public function delete($id)
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

    /**
     * Obtiene solo ciertas columnas
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function only(array $columns = ['*'])
    {
        return $this->model->select($columns)->get();
    }
}