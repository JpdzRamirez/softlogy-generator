<?php

namespace App\Http\Controllers;


/**
 * Clase abastracta que comparte metodos y funciones para los demás controladores
 */
abstract class Controller
{
    // Método para verificar si el contenido es XML
    public function isXML($content)
    {
        try {
            new \SimpleXMLElement($content);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // Método para convertir TXT a XML
    public function convertTxtToXml($txtContent)
    {
        // Aquí construyes la estructura XML según los datos del TXT
        $lines = explode("\n", $txtContent);
        $xml = new \SimpleXMLElement('<root/>');

        foreach ($lines as $line) {
            if (trim($line)) {
                $item = $xml->addChild('item', trim($line));
            }
        }

        return $xml->asXML();
    }
    public function convertBase64($imageData)
    {
        if (file_exists($imageData)) {
            $imageData = file_get_contents($imageData);
            return 'data:image/png;base64,' . base64_encode($imageData);
        }
        return null;
    }
}
