<?php

namespace App\Repositories;

use App\Models\GlpiItilFollowups;
use App\Models\GlpiDocuments;
use App\Models\GlpiDocumentsItems;

use Exception;
use stdClass;

class GlpiFollowupRepository
{

    protected $model;
    protected GlpiItilFollowups $glpifollowup;
    protected GlpiDocuments $glpiDocument;
    protected GlpiDocumentsItems $glpiDocumentsItems;

    /**
     * A fresh builder instance should contain a blank product object, which is
     * used in further assembly.
     */
    public function __construct(GlpiItilFollowups $model)
    {
        $this->model = $model;
        $this->glpifollowup = new GlpiItilFollowups();
        $this->glpiDocument = new GlpiDocuments();
        $this->glpiDocumentsItems = new GlpiDocumentsItems();
    }
    public function reset(): void
    {
        $this->model = new GlpiItilFollowups();
        $this->glpifollowup = new GlpiItilFollowups();
        $this->glpiDocument = new GlpiDocuments();
        $this->glpiDocumentsItems = new GlpiDocumentsItems();
    }   
    public function updateFollowup(int $followupId, array $data)
    {
        try {
            $user = $this->model->findOrFail($followupId);
            $user->update($data);
            return $user;
        } catch (Exception $e) {
            throw $e;
        }
    }
    /**
     * Función para eliminar un directorio y su contenido.
     *
     * @param string $dir Ruta del directorio a eliminar
     */
    private function deleteDirectory($dir)
    {
        // Asegurarse de que el directorio existe
        if (!file_exists($dir)) return;

        // Obtener todos los archivos dentro del directorio
        $files = array_diff(scandir($dir), array('.', '..'));

        foreach ($files as $file) {
            $filePath = $dir . DIRECTORY_SEPARATOR . $file;

            // Si es un directorio, eliminarlo recursivamente
            if (is_dir($filePath)) {
                $this->deleteDirectory($filePath);
            } else {
                // Si es un archivo, eliminarlo
                unlink($filePath);
            }
        }

        // Finalmente eliminar el directorio vacío
        rmdir($dir);
    }
    public function createResourceFollowup($file)
    {   
        $folderPath=null;
        try {
            if ($file && $file->isValid()) {
                // Obtener información del archivo (por ejemplo, su tipo y nombre)
                $imageType = $file->getClientOriginalExtension(); // Obtener la extensión del archivo
                $basePath = env('SOFTLOGY_HELDEKS_RESOURCES') . DIRECTORY_SEPARATOR . strtoupper($imageType); // Ruta base desde .env

                // Generar un nombre de carpeta aleatorio de 4 caracteres
                $randomFolder = substr(md5(uniqid(rand(), true)), 0, 4);
                $folderPath = $basePath . DIRECTORY_SEPARATOR . $randomFolder;

                // Crear la carpeta si no existe
                if (!file_exists($folderPath)) {
                    mkdir($folderPath, 0777, true); // Crear el directorio si no existe
                }

                // Generar un nombre único para el archivo
                $imageName = md5(uniqid(rand(), true)) . '.' . $imageType;
                $imagePath = $folderPath . DIRECTORY_SEPARATOR . $imageName;


                // Verificar que el archivo temporal existe
                $tempFilePath = $file->getPathname();

                if (!file_exists($tempFilePath)) {
                    throw new Exception("El archivo temporal no existe en la ruta: " . $tempFilePath);
                }

                // Asegurarse de que la ruta de destino usa barras diagonales                        
                $tempFilePath = str_replace('/', DIRECTORY_SEPARATOR, $tempFilePath);

                // Usar rename para mover el archivo, ya que 'rename' es más eficiente y directo
                if (!rename($tempFilePath, $imagePath)) {
                    // Si no se puede renombrar (mover), intentar copiar y luego eliminar el archivo original
                    if (!copy($tempFilePath, $imagePath)) {
                        throw new Exception("No se pudo mover el archivo a: " . $imagePath);
                    }
                    // Eliminar el archivo original si se copió correctamente
                    unlink($tempFilePath);
                }

                // Calcular el sha1sum del archivo
                $sha1sum = sha1_file($imagePath);

                // Generar un tag único
                $tag = md5(uniqid(rand(), true));

                // Retornar los detalles del archivo (ruta, sha1sum y tag)
                return (object) [
                    'path' => $imagePath,
                    'sha1sum' => $sha1sum,
                    'tag' => $tag,
                    'imageType' => $imageType,
                    'randomFolder'=>$randomFolder,
                    'imageName'=>$imageName
                ];
            }
            return null;  // Si el archivo no es válido, retornar null
        } catch (Exception $e) {
            // Si ocurre un error, eliminar la carpeta creada
            if (isset($folderPath) && file_exists($folderPath)) {
                $this->deleteDirectory($folderPath); // Eliminar la carpeta si existe
            }

            // Capturar y lanzar la excepción si ocurre un error
            throw new Exception("Error al mover el archivo: " . $e->getMessage());
        }
    }
    public function followupMessageBuilder(int $ticketId, $user, string $message, $imageData)
    {
        // Limpiar Message
        // Validar si la subcadena '-🎴Imagen Añadida-' está presente en el mensaje
        if (strpos($message, '-🎴Imagen Añadida-') !== false) {
            // Si la subcadena está presente, eliminarla
            $message = str_replace('-🎴Imagen Añadida-', '', $message);
        }
        $followupData = [
            'itemtype' => "Ticket",
            'items_id' => $ticketId,
            'date' => now()->format('Y-m-d H:i:s'),
            'users_id' => $user->glpi_id,
            'content' => '',
            'is_private' => 0,
            'requesttypes_id' => 7,
            'date_mod' => now()->format('Y-m-d H:i:s'),
            'date_creation' => now()->format('Y-m-d H:i:s'),
            'timeline_position' => 0,
            'sourceitems_id' => 0,
            'sourceof_item_id' => 0,
        ];
        // Only message
        if (empty($imageData)) {
            $followupData['content'] = "&#60;p&#62;{$message}&#60;/p&#62;";

            $this->model->create($followupData);
        } else {
            
            $followup=$this->model->create($followupData);

            $glpidocumentData = [
                'entities_id' => $user->entities_id,
                'is_recursive' => 0,
                'name' => 'Imagen_caso' . $ticketId . '.' . $imageData->imageType,
                'filename' => 'image_paste' . $ticketId . '.' . $imageData->imageType,
                'filepath' => strtoupper($imageData->imageType) . '/' . $imageData->randomFolder . '/' . $imageData->imageName,
                'documentcategories_id' => 1,
                'mime' => 'image/' . $imageData->imageType,
                'date_mod' => now()->format('Y-m-d H:i:s'),
                'is_deleted' => 0,
                'users_id' => $user->glpi_id,
                'tickets_id' => $ticketId,
                'sha1sum' => $imageData->sha1sum,
                'is_blacklisted' => 0,
                'tag' => $imageData->tag,
                'date_creation' => now()->format('Y-m-d H:i:s'),
            ];

            $glpiDocumentBuilded = $this->glpiDocument->create($glpidocumentData);

            $glpiDocumentsItemsData = [
                'documents_id' => $glpiDocumentBuilded->id,
                'items_id' => $followup->id,
                'itemtype' => "ITILFollowup",
                'entities_id' => $user->entities_id,
                'is_recursive' => 0,
                'date_mod' => now()->format('Y-m-d H:i:s'),
                'users_id' => $user->glpi_id,
                'timeline_position' => -1,
                'date_creation' => now()->format('Y-m-d H:i:s'),
                'date' => now()->format('Y-m-d H:i:s'),
            ];

            $glpiDocumentsItems = $this->glpiDocumentsItems->create($glpiDocumentsItemsData);

            $content = [
                'content' => "&#60;p&#62;{$message}&#60;br&#62;&#60;a href='/front/document.send.php?docid={$glpiDocumentBuilded->id}&#38;itemtype=Ticket&#38;items_id={$glpiDocumentsItems->id}' target='_blank' &#62;&#60;img alt='{$imageData->tag}' 
            width='550' height='500' src='/front/document.send.php?docid={$glpiDocumentBuilded->id}&#38;itemtype=Ticket&#38;items_id={$glpiDocumentsItems->id}' /&#62;&#60;/a&#62;&#60;/p&#62;",
            ];            

            $this->updateFollowup($followup->id,$content);

        }
    }
    public function createFollowup(int $ticketId, $user, string $message, $file)
    {
        try {
            $imageData = $this->createResourceFollowup($file);
            $this->followupMessageBuilder($ticketId, $user, $message, $imageData);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
