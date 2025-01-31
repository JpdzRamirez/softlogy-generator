<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
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
     * 
     * @param int $userId
     * @param mixed $ticketName
     * @param mixed $ticketStatus
     * @param mixed $ticketType
     * @param mixed $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllTicketsUser(int $userId, $ticketName, $ticketStatus, $ticketType, $perPage)
    {
        $query = $this->model
            ->where('users_id', $userId)
            ->where('type', 1) // Asumiendo que 'type' es un campo en el modelo principal
            ->whereHas('ticketContent', function ($query) use ($ticketName, $ticketStatus, $ticketType) {
                // Filtro por estado dentro de la relación
                $query->where('is_deleted', '!=', 1); // Filtra los tickets no eliminados

                // Filtrar por nombre del ticket en ticketContent
                if ($ticketName) {
                    $query->where('name', 'like', '%' . $ticketName . '%'); // Cambia 'name' por el campo adecuado
                }

                // Filtrar por estado en ticketContent
                if ($ticketStatus) {
                    $query->where('status', $ticketStatus); // Filtra por el campo 'status' de la relación
                }

                // Filtrar por tipo en ticketContent
                if ($ticketType) {
                    $query->where('type', $ticketType); // Filtra por el campo 'type' de la relación
                }
            });

        // Seleccionar los campos necesarios
        return $query->select('tickets_id', 'users_id', 'type')
            ->paginate($perPage);
    }

    /**
     * Obtiene todos los destinatarios de un ticket específico.
     *
     * @param int $ticketId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllRecipients(int $ticketId)
    {
        return $this->model->with('user')
        ->where('tickets_id', $ticketId)
        ->where('type', '!=', 1)
        ->get()
        ->map(function ($ticketUser) {
            $recipient = new \stdClass(); // Crea un nuevo objeto stdClass
            $recipient->tickets_id = $ticketUser->tickets_id;
            $recipient->users_id = $ticketUser->users_id;
            $recipient->type = $ticketUser->type;
            $recipient->firstname = optional($ticketUser->user)->firstname;
            $recipient->realname = optional($ticketUser->user)->realname;
            return $recipient; // Retorna el objeto stdClass
        });
    }
    /**
     * Summary of createTicket
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function createTicket(array $data)
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
