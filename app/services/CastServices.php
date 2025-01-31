<?php

namespace App\Services;

use App\Contracts\CastServicesInterface;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CastServices implements CastServicesInterface
{

     /**
     * Castea y transforma un arreglo de habilidades.
     *
     * @param object $photo
     * @return string
     */
    public function processPhoto(object $photo)
    {   
        $photoPath = $photo->getRealPath();
        $base64Photo = base64_encode(file_get_contents($photoPath));
        $photo = 'data:image/' . $photo->getClientOriginalExtension() . ';base64,' . $base64Photo;
        
        return $photo;
    }
     /**
     * Castea y transforma un arreglo de habilidades.
     *
     * @param string $date La fecha a transformar.
     * @param string $format El formato en el que se desea obtener la fecha
     * @return \DateTime|false
     */
    public function formatDate(string $date,string $format){
        $dateObject='';
        switch ($format) {
            case 'd-m-y':
                $dateObject = Carbon::createFromFormat('d-m-Y', $date);
                break;

            default:
                // Registrar en log el tipo no reconocido
                Log::warning("Tipo de fomrato no reconocido: {$format}");
                break; // Se puede omitir ya que está al final
        }     
        return $dateObject;
    }

    /**
     * Summary of glpiContenTicketBuilder
     * @param mixed $descriptionTicketData
     * @param mixed $photoTicketData
     * @return void
     */
    public function glpiContenTicketBuilder($ticketData,$ticketID){
        
         // Decodificar la imagen en Base64
        $imageData = base64_decode($ticketData['photoTicketData']);

        // Obtener la ruta desde el archivo .env
        $basePath = env('SOFTLOGY_HELDEKS_RESOURCES'); // Ruta base desde el .env

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

        $dataDocument=[
            'entities_id'=>$ticketData['entities_id'],
            'is_recursive'=>0,
            'name'=>"Documento de caso ".$ticketID,
            'filename'=>"image_paste".$ticketID,
            'filepath'=>"image_paste".$ticketID,
            'ticketID'=>$ticketID,
            
        ];


    }
}