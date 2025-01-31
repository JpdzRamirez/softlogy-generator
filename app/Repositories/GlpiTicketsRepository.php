<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\GlpiTickets;

use Exception;
use stdClass;

class GlpiTicketsRepository
{

    protected $model;

    /**
     * A fresh builder instance should contain a blank product object, which is
     * used in further assembly.
     */
    public function __construct(GlpiTickets $model)
    {
        $this->model = $model;
    }
    public function reset(): void
    {
        $this->model = new GlpiTickets();
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
        $query = $this->model->whereHas('ticketsUser', function ($query) use ($userId) {
            $query->where('users_id', $userId)
                ->where('type', 1); // Filtra solo los tickets de este usuario con type=1
        })
            ->where('is_deleted', '!=', 1); // Excluir tickets eliminados

        // Filtrar por nombre del ticket
        if ($ticketName) {
            $query->where('name', 'like', '%' . $ticketName . '%');
        }

        // Filtrar por estado
        if ($ticketStatus) {
            $query->where('status', $ticketStatus);
        }

        // Filtrar por tipo
        if ($ticketType) {
            $query->where('type', $ticketType);
        }

        return $query->select(
            'id',
            'entities_id',
            'name',
            'date',
            'closedate',
            'solvedate',
            'status',
            'users_id_recipient',
            'content',
            'urgency',
            'impact',
            'type'
        ) // Campos necesarios
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
        return $this->model->where('id', $ticketId) // Filtra el ticket por ID
            ->with(['ticketsUser' => function ($query) {
                $query->where('type', '!=', 1) // Filtra usuarios que no sean de tipo 1
                    ->with('user:id,firstname,realname'); // Carga solo los datos necesarios del usuario
            }])
            ->get()  // Obtiene la colección de tickets
            ->flatMap(function ($ticket) {
                // Itera sobre los tickets y extrae los usuarios asociados
                return $ticket->ticketsUser->map(function ($ticketUser) {
                    // Crea un nuevo objeto stdClass para cada usuario
                    $recipient = new stdClass();
                    $recipient->tickets_id = $ticketUser->tickets_id;
                    $recipient->users_id = $ticketUser->users_id;
                    $recipient->type = $ticketUser->type;
                    $recipient->firstname = optional($ticketUser->user)->firstname;
                    $recipient->realname = optional($ticketUser->user)->realname;
                    return $recipient;
                });
            });
    }
    /**
     * Summary of CreateTicket
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function createTicket_first_step(array $ticketData)
    {
        $urgency = 1;
        $impact = 1;
        $priority = 1;
        $slas_id_ttr = 53;
        $slas_id_tto = 0;
        $slalevels_id_ttr = 0;
        switch ($ticketData['ticketCheck']) {
            case 406:
                $urgency = 4;
                $impact = 3;
                $priority = 4;
                break;
            case 201:
                $urgency = 3;
                $impact = 2;
                $priority = 2;
                break;
            case 207:
                $urgency = 3;
                $impact = 3;
                $priority = 2;
                break;
            case 389:
                $urgency = 4;
                $impact = 3;
                $priority = 4;                
                break;
            default;
                $urgency = 3;
                $impact = 3;
                $priority = 3;
        };
        $dataFormatted = [
            'entities_id' => $ticketData['entities_id'],
            'name' => $ticketData['ticketData'],
            'date' => now()->format('Y-m-d H:i:s'),
            'users_id_lastupdater' => $ticketData['glpi_id'],
            'status' => 1,
            'users_id_recipient' => $ticketData['glpi_id'],
            'requesttypes_id' => 1,
            'content' => "",
            'urgency' => $urgency,
            'impact' => $impact,
            'priority' => $priority,
            'itilcategories_id' => $ticketData['ticketCheck'],
            'type' => 1,
            'global_validation'=>1,
            'slas_id_ttr'=>$slas_id_ttr,
            'slas_id_tto'=>$slas_id_tto,
            'slalevels_id_ttr'=>$slalevels_id_ttr,
            'sla_waiting_duration'=>0,
            'ola_waiting_duration'=>0,
            'olas_id_tto'=>0,
            'olas_id_ttr'=>0,
            'olalevels_id_ttr'=>0,
            'waiting_duration'=>0,
            'close_delay_stat'=>0,
            'solve_delay_stat'=>0,
            'takeintoaccount_delaystat'=>0,
            'locations_id'=>$ticketData['location_id'],
            'validation_percent'=>0,
            'date_creation'=>now()->format('Y-m-d H:i:s'),
        ];

        try {
        } catch (Exception $e) {
            throw $e;
        }
        return $this->model->create($ticketData);
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
