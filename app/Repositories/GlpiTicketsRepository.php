<?php

namespace App\Repositories;

use App\Models\GlpiTickets;
use App\Models\GlpiTicketsUsers;
use App\Models\GlpiDocuments;
use App\Models\GlpiDocumentsItems;

use Exception;
use stdClass;

class GlpiTicketsRepository
{

    protected $model;


    protected GlpiTicketsUsers $glpiticketsUsers;
    protected GlpiDocuments $glpiDocument;
    protected GlpiDocumentsItems $glpiDocumentsItems;

    /**
     * A fresh builder instance should contain a blank product object, which is
     * used in further assembly.
     */
    public function __construct(GlpiTickets $model)
    {
        $this->model = $model;
        $this->glpiticketsUsers = new GlpiTicketsUsers();
        $this->glpiDocument = new GlpiDocuments();
        $this->glpiDocumentsItems = new GlpiDocumentsItems();
    }
    public function reset(): void
    {
        $this->model = new GlpiTickets();
    }


    /**
     * Summary of getAllTicketsUser
     * @param int $userId
     * @param int $ticketID
     * @param string $ticketName
     * @param int $ticketStatus
     * @param int $ticketType
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllTicketsUser(int $userId,int $ticketID,string $ticketName,int $ticketStatus,int $ticketType,int $perPage)
    {
        $query = $this->model->whereHas('ticketsUser', function ($query) use ($userId) {
            $query->where('users_id', $userId)
                ->where('type', 1); // Filtra solo los tickets de este usuario con type=1
        })
            ->where('is_deleted', '!=', 1); // Excluir tickets eliminados

        // Filtrar por nombre del ticket
        if ($ticketID) {
            $query->where('id',  $ticketID);
        }
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
    public function createTicket(array $ticketData)
    {
        try {
            $titleTicket = "";
            $urgency = 1;
            $impact = 1;
            $priority = 1;
            $slas_id_ttr = 53;
            $slas_id_tto = 0;
            $slalevels_id_ttr = 0;
            switch ($ticketData['ticketCheck']) {
                case 406:
                    $titleTicket = "Error de Impresión" . " " . $ticketData['tienda'];
                    $urgency = 4;
                    $impact = 3;
                    $priority = 4;
                    break;
                case 201:
                    $titleTicket = "Identificador WEB" . " " . $ticketData['tienda'];
                    $urgency = 3;
                    $impact = 2;
                    $priority = 2;
                    break;
                case 207:
                    $titleTicket = "Personalizar Factura" . " " . $ticketData['tienda'];
                    $urgency = 3;
                    $impact = 3;
                    $priority = 2;
                    break;
                case 389:
                    $titleTicket = "Error de Facturación" . " " . $ticketData['tienda'];
                    $urgency = 4;
                    $impact = 3;
                    $priority = 4;
                    break;
                default:
                    $titleTicket = "Caso Entrante " . " " . $ticketData['tienda'];
                    $urgency = 3;
                    $impact = 3;
                    $priority = 3;
            };
            $glpiTicketData = [
                'entities_id' => $ticketData['entities_id'],
                'name' => $titleTicket,
                'date' => now()->format('Y-m-d H:i:s'),
                'takeintoaccountdate' => now()->format('Y-m-d H:i:s'),
                'date_mod' => now()->format('Y-m-d H:i:s'),
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
                'global_validation' => 1,
                'slas_id_ttr' => $slas_id_ttr,
                'slas_id_tto' => $slas_id_tto,
                'slalevels_id_ttr' => $slalevels_id_ttr,
                'sla_waiting_duration' => 0,
                'ola_waiting_duration' => 0,
                'olas_id_tto' => 0,
                'olas_id_ttr' => 0,
                'olalevels_id_ttr' => 0,
                'waiting_duration' => 0,
                'close_delay_stat' => 0,
                'solve_delay_stat' => 0,
                'takeintoaccount_delay_stat' => 0,
                'locations_id' => $ticketData['location_id'],
                'validation_percent' => 0,
                'date_creation' => now()->format('Y-m-d H:i:s'),
            ];

            return $this->model->create($glpiTicketData);
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function updateTicket(int $ticketID, array $data)
    {
        try {
            $user = $this->model->findOrFail($ticketID);
            $user->update($data);
            return $user;
        } catch (Exception $e) {
            throw $e;
        }
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
    /**
     * Summary of glpiContenTicketBuilder
     * @param array $ticketData
     * @param mixed $ticketID
     * @return array{content: string}
     */
    public function glpiContenTicketBuilder(array $ticketData,  $ticket)
    {

        try {
            // Creamos el contenido por defecto
            $content = "";

            if ($ticketData['photoTicketData'] && $ticketData['descriptionTicketData']) {

                $imageData = $ticketData['photoTicketData'];

                // Eliminar la cabecera 'data:image/png;base64,' si está presente
                if (strpos($imageData, 'data:image/png;base64,') === 0) {
                    $imageData = substr($imageData, strlen('data:image/png;base64,'));
                }

                // Decodificar la cadena base64
                $imageData = base64_decode($imageData);

                // Obtener la ruta desde el archivo .env
                $basePath = env('SOFTLOGY_HELDEKS_RESOURCES') . DIRECTORY_SEPARATOR . "PNG"; // Ruta base desde el .env

                // Generar un nombre de carpeta aleatorio de 4 caracteres
                $randomFolder = substr(md5(uniqid(rand(), true)), 0, 4);

                // Crear la carpeta en la ruta especificada
                $folderPath = $basePath . DIRECTORY_SEPARATOR . $randomFolder;

                if (!file_exists($folderPath)) {
                    mkdir($folderPath, 0777, true); // Crear la carpeta si no existe
                }
                // Generar un nombre único para el archivo de imagen
                $imageName = md5(uniqid(rand(), true)) . '.png';

                // Ruta completa del archivo de imagen
                $imagePath = $folderPath . DIRECTORY_SEPARATOR . $imageName;

                // Guardar la imagen en el archivo
                file_put_contents($imagePath, $imageData);

                // Calcular el sha1sum de la imagen
                $sha1sum = sha1_file($imagePath);

                // Generar un tag único
                $tag = md5(uniqid(rand(), true));

                $glpidocumentData = [
                    'entities_id' => $ticketData['entities_id'],
                    'is_recursive' => 0,
                    'name' => "Documento de caso " . $ticket->id,
                    'filename' => "image_paste" . $ticket->id,
                    'filepath' => "PNG/" . $randomFolder . DIRECTORY_SEPARATOR . $imageName,
                    'documentcategories_id' => 1,
                    'mime' => "image/png",
                    'date_mod' => now()->format('Y-m-d H:i:s'),
                    'is_deleted' => 0,
                    'users_id' => $ticketData['glpi_id'],
                    'tickets_id' => $ticket->id,
                    'sha1sum' => $sha1sum,
                    'is_blacklisted' => 0,
                    'tag' => $tag,
                    'date_creation' => now()->format('Y-m-d H:i:s'),
                ];

                $glpiDocumentBuilded = $this->glpiDocument->create($glpidocumentData);

                $glpiDocumentsItemsData = [
                    'documents_id' => $glpiDocumentBuilded->id,
                    'items_id' => $ticket->id,
                    'itemtype' => "Ticket",
                    'entities_id' => $ticketData['entities_id'],
                    'is_recursive' => 0,
                    'date_mod' => now()->format('Y-m-d H:i:s'),
                    'users_id' => $ticketData['glpi_id'],
                    'timeline_position' => -1,
                    'date_creation' => now()->format('Y-m-d H:i:s'),
                    'date' => now()->format('Y-m-d H:i:s'),
                ];

                $glpiDocumentsItems = $this->glpiDocumentsItems->create($glpiDocumentsItemsData);

                $content = [
                    'content' => "&#60;p&#62;{$ticketData['descriptionTicketData']}&#60;br&#62;&#60;a 
                    href='/front/document.send.php?docid={$glpiDocumentBuilded->id}&#38;itemtype=Ticket&#38;items_id={$glpiDocumentsItems->id}'
                    target='_blank' &#62;&#60;img alt='{$tag}'
                    width='759' src='/front/document.send.php?docid={$glpiDocumentBuilded->id}&#38;itemtype=Ticket&#38;items_id={$glpiDocumentsItems->id}' /&#62;&#60;/a&#62;&#60;/p&#62;",
                ];
            } elseif ($ticketData['descriptionTicketData']) {
                $content = [
                    'content' => "&#60;p&#62;{$ticketData['descriptionTicketData']}&#60;/p&#62;",
                ];
            } else {
                $message = __('general.ticket-default-message') . $ticket->name;
                $content = [
                    'content' => "&#60;p&#62;{$message}&#60;/p&#62;",
                ];
            }
            return $content;
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function createRelationTicketUser($ticket, $ticketData)
    {
        $dataTicketUser = [
            'tickets_id' => $ticket->id,
            'users_id' => $ticketData['glpi_id'],
            'type' => 1,
            'use_notification' => 1,
        ];
        $this->glpiticketsUsers->create($dataTicketUser);
    }
}
