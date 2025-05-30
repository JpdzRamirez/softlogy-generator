<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;



use App\Contracts\XmlServicesInterface;
use App\Contracts\CastServicesInterface;
use App\Models\Paises;

use SimpleXMLElement;
use DOMDocument;
use DOMXPath;
use Exception;

class XmlServices implements XmlServicesInterface
{


    protected $castServices;

    /**
     * Create a new class instance.
     */
    public function __construct(CastServicesInterface $castServices)
    {
        $this->castServices = $castServices;
    }



    public function xmlProcessData($xml, $request)
    {

        // Obtenemos todos los input del request en un array asociado al name
        $data = $request->all();

        // Modificar valores existentes
        // Asignar valores a 'tiporeceptor' y 'tipoDocRec'
        if (in_array($data['tipeDocument'], [1, 2, 3])) {
            $xml->Encabezado->tiporeceptor = 1;
        } elseif ($data['tipeDocument'] == 4) {
            $xml->Encabezado->tiporeceptor = 2;
        }

        $tipoDocRecMap = [
            1 => 22,
            2 => 50,
            3 => 31,
            4 => 13,
        ];
        if (isset($data['tipeDocument']) && isset($tipoDocRecMap[$data['tipeDocument']])) {
            $xml->Encabezado->tipoDocRec = $tipoDocRecMap[$data['tipeDocument']];
        }

        // Asignar valores básicos si están definidos
        if (isset($data['identificator'])) {
            $xml->Encabezado->nitreceptor = $data['identificator'];
        }
        if (isset($data['digit'])) {
            $xml->Encabezado->digitoverificacion = $data['digit'];
        }
        if (isset($data['firstName'])) {
            $xml->Encabezado->nombrereceptor = $data['firstName'];
        }
        if (isset($data['postalCode'])) {
            $xml->Encabezado->codigopostal = $data['postalCode'];
        }

        $xml->Encabezado->paisreceptor = $data['country'] ?? 'CO';

        // Obtener información del país
        $pais = Paises::where('codigo', $data['country'])->first();

        // Asignar estado, ciudad, y dirección con valores predeterminados
        if (isset($data['state'])) {
            $xml->Encabezado->departamentoreceptor = $data['state'];
        } elseif ($pais) {
            $xml->Encabezado->departamentoreceptor = $pais->nombre;
        }

        if (isset($data['city'])) {
            $xml->Encabezado->ciudadreceptor = $data['city'];
        } elseif ($pais) {
            $xml->Encabezado->ciudadreceptor = $pais->nombre;
        }

        if (isset($data['address'])) {
            $xml->Encabezado->direccionreceptor = $data['address'];
        } elseif ($pais) {
            $xml->Encabezado->direccionreceptor = $pais->nombre;
        }

        // Asignar correo electrónico si está presente
        if (isset($data['emailReceptor'])) {
            $xml->Encabezado->mailreceptor = $data['emailReceptor'];
            $xml->Encabezado->mailreceptorcontacto = $data['emailReceptor'];
        }

        // Asignar número de teléfono si está presente
        if (isset($data['phone'])) {
            $xml->Encabezado->telefonoreceptor = $data['phone'];
        }

        // Asignar nombre comercial y contacto
        if (in_array($data['tipeDocument'], [2, 3]) && isset($data['firstName'])) {
            $xml->Encabezado->nombrecomercialreceptor = $data['firstName'];
            $xml->Encabezado->nombrecontactoreceptor = $data['firstName'];
        } else {
            if (isset($data['firstName'], $data['secondName'], $data['lastName'])) {
                $nombreCompleto = "{$data['firstName']} {$data['secondName']} {$data['lastName']}";
                $xml->Encabezado->nombrecomercialreceptor = $nombreCompleto;
                $xml->Encabezado->nombrecontactoreceptor = $nombreCompleto;
            } else {
                // Asignar solo nombre si alguno de los campos está presente
                if (isset($data['firstName'])) {
                    $xml->Encabezado->nombrecontactoreceptor = $data['firstName'];
                }
                if (isset($data['secondName'])) {
                    $xml->Encabezado->segnombrereceptor = $data['secondName'];
                }
                if (isset($data['lastName'])) {
                    $xml->Encabezado->apellidosreceptor = $data['lastName'];
                }
            }
        }

        // Asignar folio y nciddoc si están presentes
        if (isset($data['folio'])) {
            $xml->Encabezado->folio = $data['folio'];
            $xml->Encabezado->nciddoc = $xml->Encabezado->prefijo . $data['folio'];
        }

        // Asignar fecha y hora
        $fecha = now()->format('Y-m-d');
        $hora = now()->subHour()->format('H:i:s');
        $xml->Encabezado->fecha = $fecha;
        $xml->Encabezado->fechavencimiento = $fecha;
        $xml->Encabezado->hora = $hora;

        return $xml;
    }
    public function xmlCambiarDatos(array $newData)
    {
        try {
            // Limpiar la cadena de entrada
            $rowText = str_replace(';NULL;', '', $newData['factura']); // Eliminar ;NULL;
            $rowText = str_replace(';', '', $rowText);

            // Asegurar que el XML se procese correctamente
            $rowText = htmlspecialchars_decode($rowText, ENT_QUOTES); // Decodificar caracteres especiales

            // Convertir a UTF-8 si no lo está
            $rowText = mb_convert_encoding($rowText, 'UTF-8', 'auto');

            // Cargar el XML
            $xml = simplexml_load_string($rowText, 'SimpleXMLElement', LIBXML_NOERROR | LIBXML_ERR_NONE | LIBXML_NOCDATA);

            if ($xml === false) {
                return 'Error al cargar el XML'; // Manejo de error en caso de que el XML no sea válido
            }

            // Cambiamos los datos
            if ($newData['type'] == 2 || $newData['type'] == 3) {
                if (!empty($newData['prefijo'])) {
                    $xml->Encabezado->prefijo = $newData['prefijo'];
                }
                if (!empty($newData['resolucion'])) {
                    $xml->Encabezado->noresolucion = $newData['resolucion'];
                }
            }
            if ($newData['type'] == 3) {
                $xml->Encabezado->tipocomprobante = $newData['tipoComprobante'];
                $xml->Encabezado->xslt = $newData['xslt'];
            }

            $xml->Encabezado->folio = $newData['folio'];
            $xml->Encabezado->nciddoc = $xml->Encabezado->prefijo . $xml->Encabezado->folio;

            if ($newData['type'] == 3) {
                $xml->Encabezado->fecha = $newData['fecha'];
                $xml->Encabezado->fechavencimiento = $newData['fecha'];
                $hora = now()->subHour()->format('H:i:s');
                $xml->Encabezado->hora = $hora;
            } else {
                $fecha = now()->format('Y-m-d');
                $hora = now()->subHour()->format('H:i:s');
                $xml->Encabezado->fecha = $fecha;
                $xml->Encabezado->fechavencimiento = $fecha;
                $xml->Encabezado->hora = $hora;
            }


            // Convertir el XML a cadena
            $xmlString = $xml->asXML();

            // Eliminar la primera línea (declaración XML) si existe
            $xmlString = preg_replace('/^<\?xml.*\?>\s*/', '', $xmlString);

            // Retornar el XML como texto sin la declaración
            return $xmlString;
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function xmlGenerar($dataInput)
    {
        try {
            $filePath = storage_path("documents/plantillas/xmlPapaJohns.xml");
            // normalizar path
            $normalizedPath = str_replace('/', '\\', $filePath);
            // Obtener el contenido del archivo XML
            $xmlContent = file_get_contents($normalizedPath);

            // Cargar el XML
            $xml = simplexml_load_string($xmlContent);

            // Cambiamos el folio noresolucion
            $xml->Encabezado->noresolucion = $dataInput['noresolucion'];
            $xml->Encabezado->prefijo = $dataInput['prefijo'];
            $xml->Encabezado->folio = $dataInput['folio'];
            $xml->Encabezado->nciddoc = $xml->Encabezado->prefijo . $dataInput['folio'];
            $xml->Encabezado->folioPOS = $dataInput['folioPOS'];
            $xml->Encabezado->checkid = $dataInput['checkid'];
            $xml->Encabezado->BrandId = $dataInput['BrandId'];
            $xml->Encabezado->StoreId = $dataInput['StoreId'];
            $fecha = now()->format('Y-m-d');
            $hora = now()->subHour()->format('H:i:s');
            $xml->Encabezado->fecha = $fecha;
            $xml->Encabezado->fechavencimiento = $fecha;
            $xml->Encabezado->hora = $hora;

            $xmlString = $xml->asXML();
            // Eliminar la primera línea (declaración XML) si existe
            $xmlString = preg_replace('/^<\?xml.*\?>\s*/', '', $xmlString);

            $xmlString = preg_replace('/>\s+</', '><', $xmlString);

            // Retornar el XML como texto sin la declaración
            return $xmlString;
        } catch (Exception $e) {
            throw $e;
        }
    }

public function JSONToXML($jsonData)
{
    try {
        $data = json_decode($jsonData, true);
        $xml = new SimpleXMLElement('<Detalle></Detalle>');
        
        foreach ($data['Detalles'] as $item) {
            // Saltar items con importe 0
            if ($item['Importe'] == "0") {
                continue;
            }
            
            $det = $xml->addChild('Det');
            $det->addChild('llaveComprobante', '1');
            $det->addChild('idConcepto', $item['IdConcepto']);
            $det->addChild('cantidad', $item['Cantidad']);
            $det->addChild('unidadmedida', '94');
            $det->addChild('impuestolinea', $item['Impuestolinea']);
            $det->addChild('tasa', number_format(floatval($item['Tasa']), 2, '.', ''));
            $det->addChild('tipo', $item['Tipo']);
            $det->addChild('baseimpuestos', $item['Baseimpuestos']);
            $det->addChild('UnidadMedidaBase', '0.00');
            $det->addChild('ValorporUnidad', '0.00');
            $det->addChild('subpartidaarancelaria', '');
            $det->addChild('MultipleImpuesto', 'false');
            $det->addChild('identificacionproductos', $item['Identificacionproductos']);
            $det->addChild('descripcion', $item['Descripcion']);
            $det->addChild('precioUnitario', $item['PrecioUnitario']);
            $det->addChild('importe', $item['Baseimpuestos']);
            $det->addChild('importemuestra', '0.00');
            $det->addChild('tipomuestra', '');
            $det->addChild('adicionalInfo', '');
            $det->addChild('articulocubierto', '0');
            
            // Añadir campos extra vacíos
            for ($i = 1; $i <= 15; $i++) {
                $det->addChild('extra' . $i, '');
            }
        }
        
        // Formatear el XML para que quede con indentación
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        
        // Eliminar la declaración XML y retornar solo el contenido
        $xmlString = $dom->saveXML();
        $xmlString = preg_replace('/<\?xml.*?\?>\n/', '', $xmlString);

        return [
                'xml' => trim($xmlString),
                'status' => true
        ];        
    } catch (Exception $e) {
        throw $e;
    }
}

    public function xmlAplicarDescuentos(String $factura)
    {
        try {
            // Limpiar la cadena de entrada
            $rowText = str_replace(';NULL;', '', $factura); // Eliminar ;NULL;
            $rowText = str_replace(';', '', $rowText);

            // Variables para descuentos
            $FCARCH30 = false;
            $productoMayorValor = 0;
            $idProductoMayorValor = '';
            $importeDescuento = 0;
            $nodesToRemove = [];
            $productoMayorValorNode = null;
            $impuestoLineaOriginal = 0;
            $baseImpuestoGeneral = 0;

            // Asegurar que el XML se procese correctamente
            $rowText = htmlspecialchars_decode($rowText, ENT_QUOTES); // Decodificar caracteres especiales

            // Convertir a UTF-8 si no lo está
            $rowText = mb_convert_encoding($rowText, 'UTF-8', 'auto');

            // Cargar el XML
            $xml = simplexml_load_string($rowText, 'SimpleXMLElement', LIBXML_NOERROR | LIBXML_ERR_NONE | LIBXML_NOCDATA);

            if ($xml === false) {
                return [
                    'status' => 0,
                    'xml' => 'Error al cargar el XML'
                ];
            }


            // 1. Primera pasada: Buscar FCARCH30 e identificar producto de mayor valor
            foreach ($xml->Detalle->Det as $detalle) {
                // Buscar descuento FCARCH30
                if (isset($detalle->identificacionproductos) && str_contains((string)$detalle->identificacionproductos, 'FCARCH')) {
                    $FCARCH30 = true;
                    $importeDescuento = abs((float)$detalle->importe); // Valor absoluto del descuento
                    $impuestoEnLineaDescuento = (float)$detalle->impuestolinea;
                    $importeDescuento = $importeDescuento - $impuestoEnLineaDescuento;
                    $nodesToRemove[] = $detalle;
                }

                // Identificar producto de mayor valor (excluyendo posibles descuentos)
                if ((float)$detalle->importe > 0 && $productoMayorValor < (float)$detalle->importe) {
                    $idProductoMayorValor = (string)$detalle->idConcepto;
                    $productoMayorValor = (float)$detalle->importe;
                    $productoMayorValorNode = $detalle;
                    $impuestoLineaOriginal = (float)$detalle->impuestolinea;
                }
            }

            // 2. Aplicar cambios si hay descuento FCARCH30
            if ($FCARCH30 && $productoMayorValorNode !== null) {
                // 2.1. Eliminar nodos de descuentos
                foreach ($nodesToRemove as $node) {
                    $dom = dom_import_simplexml($node);
                    $dom->parentNode->removeChild($dom);
                }

                // 2.2. Recalcular valores del producto de mayor valor
                $nuevoImporte = $productoMayorValor - $importeDescuento;
                $sumaParcial = $nuevoImporte + $impuestoLineaOriginal;
                $baseImponible = $sumaParcial / 1.08;
                $nuevoImpuesto = ($baseImponible * 8) / 100;

                // Actualizar valores en el XML
                $productoMayorValorNode->importe = number_format($baseImponible, 2, '.', '');
                $productoMayorValorNode->baseimpuestos = number_format($baseImponible, 2, '.', '');
                $productoMayorValorNode->impuestolinea = number_format($nuevoImpuesto, 2, '.', '');

                // 2.3. Agregar sección de descuentos inmediatamente después de Detalle
                // Convertir a DOM para manipulación de posición
                $dom = new DOMDocument('1.0', 'UTF-8');
                $dom->loadXML($xml->asXML());

                $xpath = new DOMXPath($dom);
                $descuentosLineaNodes = $xpath->query('//DescuentosLinea');

                if ($descuentosLineaNodes->length === 0) {
                    $descuentosNode = $dom->createElement('DescuentosLinea');
                    $detDesc = $dom->createElement('detDesc');

                    $detDesc->appendChild($dom->createElement('idlineaDescuento', $idProductoMayorValor));
                    $detDesc->appendChild($dom->createElement('basedescuentodet', number_format($importeDescuento, 2, '.', '')));
                    $detDesc->appendChild($dom->createElement('importedescuentodet', number_format($importeDescuento, 2, '.', '')));
                    $detDesc->appendChild($dom->createElement('razondescuentodet', 'DESCUENTO'));
                    $detDesc->appendChild($dom->createElement('porcentajedescdet', '100'));

                    $descuentosNode->appendChild($detDesc);

                    $detalleNodes = $xpath->query('//Detalle');
                    if ($detalleNodes->length > 0) {
                        $detalleNode = $detalleNodes->item(0);
                        $detalleNode->parentNode->insertBefore($descuentosNode, $detalleNode->nextSibling);
                    } else {
                        $dom->documentElement->appendChild($descuentosNode);
                    }
                } else {
                    $detDesc = $dom->createElement('detDesc');
                    $detDesc->appendChild($dom->createElement('idlineaDescuento', $idProductoMayorValor));
                    $detDesc->appendChild($dom->createElement('basedescuentodet', number_format($importeDescuento, 2, '.', '')));
                    $detDesc->appendChild($dom->createElement('importedescuentodet', number_format($importeDescuento, 2, '.', '')));
                    $detDesc->appendChild($dom->createElement('razondescuentodet', 'DESCUENTO'));
                    $detDesc->appendChild($dom->createElement('porcentajedescdet', '100'));

                    $descuentosLineaNodes->item(0)->appendChild($detDesc);
                }

                // Volver a SimpleXML para continuar con el procesamiento
                $xml = simplexml_load_string($dom->saveXML());

                // Recalculamos etiquetas generales
                $baseImpuestoGeneral = $xml->Encabezado->totalsindescuento;
                $nuevoTotalBaseImpuesto = ($baseImpuestoGeneral / 1.08);
                $nuevoTotalImpuestoGeneral = ($nuevoTotalBaseImpuesto * 8) / 100;

                // Modificamos los encabezados
                $xml->Encabezado->baseimpuesto = number_format($nuevoTotalBaseImpuesto, 2, '.', '');
                $xml->Encabezado->subtotal = number_format($nuevoTotalBaseImpuesto, 2, '.', '');
                $xml->Encabezado->totalimpuestos = number_format($nuevoTotalImpuestoGeneral, 2, '.', '');

                $xml->Encabezado->extra11 = number_format($importeDescuento, 2, '.', '');

                // Verificar si existen los nodos de impuestos antes de modificarlos
                if (isset($xml->Impuestos) && isset($xml->Impuestos->Imp)) {
                    $xml->Impuestos->Imp->baseimpuestos = number_format($nuevoTotalBaseImpuesto, 2, '.', '');
                    $xml->Impuestos->Imp->importe = number_format($nuevoTotalImpuestoGeneral, 2, '.', '');
                }

                // Cambiamos los datos de fechas para envío                              
                $fecha = now()->format('Y-m-d');
                $hora = now()->subHour()->format('H:i:s');
                $xml->Encabezado->fecha = $fecha;
                $xml->Encabezado->fechavencimiento = $fecha;
                $xml->Encabezado->hora = $hora;
            }

            // Convertir el XML a cadena
            $xmlString = $xml->asXML();

            // Eliminar la primera línea (declaración XML) si existe
            $xmlString = preg_replace('/^<\?xml.*\?>\s*/', '', $xmlString);

            // Retornar el XML como texto sin la declaración
            return [
                'xml' => $xmlString,
                'status' => $FCARCH30
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function xmlCrearNotaCredito(array $data)
    {
        try {
            // Limpiar la cadena de entrada
            $rowText = str_replace(';NULL;', '', $data['factura']); // Eliminar ;NULL;
            $rowText = str_replace(';', '', $rowText);

            // Asegurar que el XML se procese correctamente
            $rowText = htmlspecialchars_decode($rowText, ENT_QUOTES); // Decodificar caracteres especiales

            // Convertir a UTF-8 si no lo está
            $rowText = mb_convert_encoding($rowText, 'UTF-8', 'auto');

            // Cargar el XML
            $xml = simplexml_load_string($rowText, 'SimpleXMLElement', LIBXML_NOERROR | LIBXML_ERR_NONE | LIBXML_NOCDATA);

            if ($xml === false) {
                return [
                    'status' => 0,
                    'xml' => 'Error al cargar el XML'
                ];
            }
            // 1. Primera pasada: Buscar FCARCH30 e identificar producto de mayor valor
            // 2. Aplicar cambios si hay descuento FCARCH30                         
            // 2.3. Agregar sección de descuentos inmediatamente después de Detalle
            // Convertir a DOM para manipulación de posición
            $dom = new DOMDocument('1.0', 'UTF-8');
            $dom->loadXML($xml->asXML());
            
            $xpath = new DOMXPath($dom);
            $encabezadoNodes = $xpath->query('//Encabezado');

            if ($encabezadoNodes->length > 0) {
                /** @var DOMElement $encabezado */
                $encabezado = $encabezadoNodes->item(0);
                // Array con nuevos valores
                $nuevosValores = [
                    'tipoOpera' => '20',
                    'totalAnticipo' => '0.00',
                    'ncidfact' => $data['prefijo'] . $data['folio'],
                    'nccod' => '2',                    
                    'ncuuid' => $data['cufe'],
                    'ncfecha' => $data['fecha'],
                    'ndidfact' => '',
                    'ndcod' => '',
                    'ndiddoc' => '',
                    'nduuid' => '',
                    'ndfecha' => '',
                ];
                // 'nciddoc' => $xml->Encabezado->ncidfact,
                if($data['tipo']==1){
                    $nuevosValores['nciddoc']= $xml->Encabezado->ncidfact;
                }elseif($data['tipo']== 2){                    
                    $nuevosValores['nciddoc']= $data['prefijoFE'] . $data['folioFE'];
                }

                foreach ($nuevosValores as $tag => $valor) {
                    $nodo = null;

                    foreach ($encabezado->getElementsByTagName($tag) as $child) {
                        $nodo = $child;
                        break;
                    }

                    if ($nodo) {
                        $nodo->nodeValue = $valor;
                    } else {
                        $nuevoNodo = $dom->createElement($tag, $valor);
                        $encabezado->appendChild($nuevoNodo);
                    }
                }
            }
            // Volver a SimpleXML para continuar con el procesamiento
            $xml = simplexml_load_string($dom->saveXML());

            $xml->Encabezado->tipocomprobante = '91';
            $xml->Encabezado->noresolucion = $data['resolucion'];
            $xml->Encabezado->prefijo = $data['prefijo'];
            $xml->Encabezado->folio = $data['folio'];

            $xml->Encabezado->xslt = '5';

            if ($data['tipo'] == 2) {
                $xml->Encabezado->subtotal = number_format((float)$data['valor'], 2, '.', '');
                $xml->Encabezado->baseimpuesto = number_format((float)$data['valor'], 2, '.', '');
                $xml->Encabezado->totalsindescuento = number_format((float)($data['valor']+$data['impuesto']), 2, '.', '');
                $xml->Encabezado->totalimpuestos = number_format((float)$data['impuesto'], 2, '.', '');
                $xml->Encabezado->total = number_format((float)($data['valor']+$data['impuesto']), 2, '.', '');
                $xml->Encabezado->montoletra = $this->castServices->numeroALetras($data['valor']+$data['impuesto']);

                $xml->Encabezado->direccionreceptor = $data['direccion'];
                $xml->Encabezado->mailreceptor = $data['correo'];
                $xml->Encabezado->mailreceptorcontacto = $data['correo'];
                $xml->Encabezado->extra1 = $data['marca'];
                $xml->Encabezado->extra2 = $data['direccion'];
                $xml->Encabezado->extra4 = $data['correo'];
                $xml->Encabezado->extra7 = number_format((float)$data['impuesto'], 2, '.', '');
                $xml->Encabezado->folioPOS = $data['folioPos'];
                $xml->Encabezado->IdentificadorOrden = $data['checkId'];

                $xml->Detalle->Det->impuestolinea=number_format((float)$data['impuesto'], 2, '.', '');
                $xml->Detalle->Det->baseimpuestos=number_format((float)$data['valor'], 2, '.', '');
                $xml->Detalle->Det->precioUnitario=number_format((float)$data['valor'], 2, '.', '');
                $xml->Detalle->Det->importe=number_format((float)$data['valor'], 2, '.', '');

                $xml->Impuestos->Imp->baseimpuestos=number_format((float)$data['valor'], 2, '.', '');
                $xml->Impuestos->Imp->importe=number_format((float)$data['impuesto'], 2, '.', '');                                   
            }

            // Cambiamos los datos de fechas para envío                              
            $fecha = now()->format('Y-m-d');
            $hora = now()->subHour()->format('H:i:s');
            $xml->Encabezado->fecha = $fecha;
            $xml->Encabezado->fechavencimiento = $fecha;
            $xml->Encabezado->hora = $hora;


            // Convertir el XML a cadena
            $xmlString = $xml->asXML();

            // Eliminar la primera línea (declaración XML) si existe
            $xmlString = preg_replace('/^<\?xml.*\?>\s*/', '', $xmlString);

            // Retornar el XML como texto sin la declaración
            return [
                'xml' => $xmlString,
                'status' => true
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function xmlCrearContingencias(array $data)
    {
        try {
            // Limpiar la cadena de entrada
            $rowText = str_replace(';NULL;', '', $data['factura']); // Eliminar ;NULL;
            $rowText = str_replace(';', '', $rowText);

            // Asegurar que el XML se procese correctamente
            $rowText = htmlspecialchars_decode($rowText, ENT_QUOTES); // Decodificar caracteres especiales

            // Convertir a UTF-8 si no lo está
            $rowText = mb_convert_encoding($rowText, 'UTF-8', 'auto');

            // Cargar el XML
            $xml = simplexml_load_string($rowText, 'SimpleXMLElement', LIBXML_NOERROR | LIBXML_ERR_NONE | LIBXML_NOCDATA);

            if ($xml === false) {
                return [
                    'status' => 0,
                    'xml' => 'Error al cargar el XML'
                ];
            }
            if ($data['optionSelect'] == 1) {
                $xml->Encabezado->tipocomprobante = '03';
                $xml->Encabezado->noresolucion = $data['resolucion'];
                $xml->Encabezado->prefijo = $data['prefijo'];
                $xml->Encabezado->ncuuid = '';
                $xml->Encabezado->nccod = 'C1';
                $xml->Encabezado->tipoOpera = 10;
                $xml->Encabezado->xslt = 7;
                if( isset($data['nit']) && !empty($data['nit']) ){
                    $xml->Encabezado->nitemisor = $data['nit'];
                    $xml->Encabezado->codSucursal = $data['nit'];
                } 
            }
            $xml->Encabezado->folio = $data['folio'];
            $xml->Encabezado->nciddoc = '';
            $xml->Encabezado->ncidfact =  $xml->Encabezado->prefijo . $data['folio'];
            $xml->Encabezado->ncfecha = $data['fecha'];

            if ($data['optionSelect'] == 2) {
                $xml->Encabezado->subtotal = number_format((float)$data['base'], 2, '.', '');
                $xml->Encabezado->baseimpuesto = number_format((float)$data['base'], 2, '.', '');
                $xml->Encabezado->totalsindescuento = number_format((float)$data['total'], 2, '.', '');
                $xml->Encabezado->total = number_format((float)$data['total'], 2, '.', '');
                $xml->Encabezado->totalimpuestos = number_format((float)$data['impuesto'], 2, '.', '');

                $xml->Encabezado->montoletra = $this->castServices->numeroALetras($data['total']);

                $xml->Encabezado->extra7 = number_format((float)$data['impuesto'], 2, '.', '');

                $xml->Impuestos->Imp->baseimpuestos = number_format((float)$data['base'], 2, '.', '');
                $xml->Detalle->Det->impuestolinea = number_format((float)$data['impuesto'], 2, '.', '');
                $xml->Detalle->Det->baseimpuestos = number_format((float)$data['base'], 2, '.', '');
                $xml->Detalle->Det->baseimpuestos = number_format((float)$data['base'], 2, '.', '');
                $xml->Detalle->Det->precioUnitario = number_format((float)$data['base'], 2, '.', '');
                $xml->Detalle->Det->importe = number_format((float)$data['base'], 2, '.', '');


                $xml->Impuestos->Imp->baseimpuestos = number_format((float)$data['base'], 2, '.', '');
                $xml->Impuestos->Imp->importe = number_format((float)$data['impuesto'], 2, '.', '');
            }

            // Cambiamos los datos de fechas para envío                                              
            $hora = now()->subHour()->format('H:i:s');
            $xml->Encabezado->fecha = $data['fecha'];
            $xml->Encabezado->fechavencimiento = $data['fecha'];
            $xml->Encabezado->hora = $hora;


            // Convertir el XML a cadena
            $xmlString = $xml->asXML();

            // Eliminar la primera línea (declaración XML) si existe
            $xmlString = preg_replace('/^<\?xml.*\?>\s*/', '', $xmlString);

            // Retornar el XML como texto sin la declaración
            return [
                'xml' => $xmlString,
                'status' => true
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }
}
