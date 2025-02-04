<?php

namespace App\Services;

use App\Contracts\CastServicesInterface;



use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class CastServices implements CastServicesInterface
{


    public function __construct()
    {

    }

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
    public function formatDate(string $date, string $format)
    {
        $dateObject = '';
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
}