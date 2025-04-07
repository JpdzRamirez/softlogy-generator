# Definir las claves a buscar en FEWindowsServices.exe.config
$keysToFind = @(
    "Endpoint", "EndpointOnline", "TokenEndpoint", "TokenUser", "TokenPass", 
    "IdTienda", "BrandId", "Tienda", "EndpointRPD", "EndPointSolicitudes"
)

# Definir las rutas de los archivos de configuración
$archivoConfig = "C:\FE.Bots\Fe.Robot\FEWindowsServices.exe.config"
$archivoCredenciales = "C:\FE.Bots\Fe.Robot\reporting.config"

# Verificar si los archivos existen
if (-Not (Test-Path $archivoConfig)) {
    Write-Host "El archivo de configuración principal no existe." -ForegroundColor Red
    exit
}

if (-Not (Test-Path $archivoCredenciales)) {
    Write-Host "El archivo de credenciales no existe." -ForegroundColor Red
    exit
}

# Leer el contenido de los archivos XML
try {
    [xml]$xmlContent = Get-Content $archivoConfig
    [xml]$xmlCredenciales = Get-Content $archivoCredenciales
} catch {
    Write-Host "Error al leer los archivos XML. Verifique la estructura y el formato." -ForegroundColor Red
    exit
}

# Crear un objeto para almacenar los valores
$jsonData = @{ data = @{} }

# Extraer valores de las claves en appSettings y guardarlos en el JSON
foreach ($key in $keysToFind) {
    $node = $xmlContent.configuration.appSettings.add | Where-Object { $_.key -eq $key }
    if ($node) {
        $jsonData.data[$key] = $node.value
    } else {
        $jsonData.data[$key] = ""
    }
}

# Extraer credenciales de autenticación
$credenciales = @{}
$credenciales["username"] = ($xmlCredenciales.configuration.appSettings.add | Where-Object { $_.key -eq "username" }).value
$credenciales["password"] = ($xmlCredenciales.configuration.appSettings.add | Where-Object { $_.key -eq "password" }).value
$credenciales["plataform"] = ($xmlCredenciales.configuration.appSettings.add | Where-Object { $_.key -eq "plataform" }).value

# Convertir credenciales a JSON
$credencialesJson = $credenciales | ConvertTo-Json -Depth 10

# Obtener el token de autenticación
$authUrl = "http://127.0.0.1:8000/api/get-softlogymicro-token"
$authHeaders = @{ 
    "Cookie" = "XDEBUG_SESSION=VSCODE"
    "Content-Type" = "application/json" 
}

try {
    $authResponse = Invoke-RestMethod -Uri $authUrl -Method Post -Headers $authHeaders -Body $credencialesJson -ContentType "application/json"
    $token = $authResponse.access_token
    Write-Host "Token obtenido exitosamente." -ForegroundColor Green
} catch {
    Write-Host "Error al obtener el token: $_" -ForegroundColor Red
    exit
}

# Convertir los datos extraídos a JSON
$jsonString = $jsonData | ConvertTo-Json -Depth 10
#Write-Host "Datos a enviar: $jsonString" -ForegroundColor Cyan

# Definir los encabezados para la solicitud REST final
$headers = @{
    "Cookie" = "XDEBUG_SESSION=VSCODE"
    "Authorization" = "Bearer $token"
    "Content-Type" = "application/json"
}

# URL del servicio REST final (modifícala según sea necesario)
$apiUrl = "http://127.0.0.1:8000/api/report-status-store"

# Enviar la solicitud POST con los datos y el token obtenido
try {
    $response = Invoke-RestMethod -Uri $apiUrl -Method Post -Headers $headers -Body $jsonString -ContentType "application/json"
    Write-Host "Respuesta del servidor: $(ConvertTo-Json $response -Depth 10)"
} catch {
    Write-Host "Error al enviar la solicitud: $_" -ForegroundColor Red
}
