<?php

namespace App\Services;

use App\Contracts\CastServicesInterface;

use Exception;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\PngEncoder;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class CastServices implements CastServicesInterface
{


    public function __construct()
    {

    }

    /**
     * Castea y transforma una imagen a base 64 y la comprime para reducir su tamaño.
     *
     * @param object $photo
     * @return mixed
     */
    public function processPhoto(object $photo)
    {   
        // Crear una instancia de ImageManager con el driver GD
        if ($photo->getClientOriginalExtension() === 'heic') {
            throw new Exception('Error de formato no compatible | HEIC | Iphone.');
        }elseif(!in_array(strtolower($photo->getClientOriginalExtension()),['jpeg','jpg','png'])){
            $manager = new ImageManager(new Driver());
    
            // Leer la imagen desde el archivo y convertirla a PNG
            $image = $manager->read($photo->getRealPath());
    
            $encoder = new PngEncoder(9);
            
            $image = $image->encode($encoder);
    
            // Convertir la imagen PNG a Base64
            $base64Photo = base64_encode($image->toString());
        
            // Retornar la imagen en formato Base64 con el encabezado correcto
            return 'data:image/png;base64,' . $base64Photo;
        }else{
            $photoPath = $photo->getRealPath();
            $base64Photo = base64_encode(file_get_contents($photoPath));
            return 'data:image/' . $photo->getClientOriginalExtension() . ';base64,' . $base64Photo;
        }

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

    public function undecodeBase64(string $base64): string
    {
        // Decodifica la cadena Base64
        $decodedString = base64_decode($base64);
    
        // Verifica si la decodificación fue exitosa
        if ($decodedString === false) {
            return '';
        }
    
        return $decodedString;
    }
}