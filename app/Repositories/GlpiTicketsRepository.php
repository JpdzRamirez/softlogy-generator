<?php

namespace App\Repositories;


use App\Models\GlpiTicketsUsers;


class GlpiTicketsRepository
{
    protected $model;

    /**
     * A fresh builder instance should contain a blank product object, which is
     * used in further assembly.
     */
    public function __construct(GlpiTicketsUsers $model)
    {
        $this->model = $model;
    }
    public function reset(): void
    {
        $this->model = new GlpiTicketsUsers();
    }
    /**
     * All production steps work with the same product instance.
     */
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * 
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllTicketsUser(int $userId){
        return $this->model
        ->where('users_id', $userId)
        ->where('type', 1)
        ->whereHas('ticketContent', function ($query) {
            $query->where('is_deleted', '!=', 1); // Filtra los tickets no eliminados
        })
        ->select('tickets_id', 'users_id', 'type')
        ->get();
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
     * Obtiene solo ciertas columnas y carga relaciones
     * @param array $columns
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function only(array $columns = ['*'], array $relations = [])
    {
        $query = $this->model->select($columns);

        // Cargar las relaciones si existen
        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->get();
    }
}
