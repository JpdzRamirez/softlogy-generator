# Definir las claves a buscar en FEWindowsServices.exe.config
$keysToFind = @(
    "Endpoint", "EndpointOnline", "TokenEndpoint", "TokenUser", "TokenPass", 
    "IdTienda", "BrandId", "Tienda", "EndpointRPD", "EndPointSolicitudes"
)

# Definir las rutas de los archivos de configuración
$FEConfig = "C:\FE.Bots\Fe.Robot\FEWindowsServices.exe.config"
$APICredenciales = "C:\FE.Bots\Fe.Robot\reporting.config"

# Verificar si los archivos existen
if (-Not (Test-Path $FEConfig)) {
    Write-Host "El archivo de configuración principal no existe." -ForegroundColor Red
    exit
}

if (-Not (Test-Path $APICredenciales)) {
    Write-Host "El archivo de credenciales no existe." -ForegroundColor Red
    exit
}

# Leer el contenido de los archivos XML
try {
    [xml]$xmlFEContent = Get-Content $FEConfig
    [xml]$xmlCredenciales = Get-Content $APICredenciales
} catch {
    Write-Host "Error al leer los archivos XML. Verifique la estructura y el formato." -ForegroundColor Red
    exit
}

# Crear un objeto para almacenar los valores
$jsonFEData = @{ data = @{} }

# Extraer valores de las claves en appSettings y guardarlos en el JSON
foreach ($key in $keysToFind) {
    $node = $xmlFEContent.configuration.appSettings.add | Where-Object { $_.key -eq $key }
    if ($node) {
        $jsonFEData.data[$key] = $node.value
    } else {
        $jsonFEData.data[$key] = ""
    }
}

# Mostrar los valores extraídos de appSettings
Write-Host "Valores extraídos de appSettings:"
$jsonFEData.data.GetEnumerator() | ForEach-Object { Write-Host "$($_.Key) : $($_.Value)" }

# Extraer información de la cadena de conexión
$connectionStringNode = $xmlFEContent.configuration.connectionStrings.add | Where-Object { $_.name -eq "ConnectionStringSQL" }

if (-Not $connectionStringNode) {
    Write-Host "No se encontró la cadena de conexión en el archivo de configuración." -ForegroundColor Red
    exit
}

$connectionString = $connectionStringNode.connectionString

# Extraer la instancia SQL (Data Source)
if ($connectionString -match "Data Source=([^;]+)") {
    $sqlInstance = $matches[1]
}

# Extraer el nombre de la base de datos (Initial Catalog)
if ($connectionString -match "Initial Catalog=([^;]+)") {
    $databaseName = $matches[1]
}

# Extraer el usuario SQL (User ID)
if ($connectionString -match "User ID=([^;]+)") {
    $sqlUser = $matches[1]
}

# Extraer la contraseña (Password)
if ($connectionString -match "password=([^;]+)") {
    $sqlPassword = $matches[1]
}

# Mostrar los valores extraídos
Write-Host "Instancia SQL: $sqlInstance"
Write-Host "Base de Datos: $databaseName"
Write-Host "Usuario: $sqlUser"
Write-Host "Contraseña: $sqlPassword"

#Instanciar Conexión a la base de datos
$connectionStringSQL = "Server=$sqlInstance;Database=$databaseName;User Id=$sqlUser;Password=$sqlPassword;"


# Conectar a SQL Server y extraer datos de ContingenciaResolucion
$registrosContingencia = @()

try {
    $connection = New-Object System.Data.SqlClient.SqlConnection
    $connection.ConnectionString = $connectionStringSQL
    $connection.Open()

    $queryContingencia = @"
SELECT TOP (1000) 
    [IdConsecutivo],
    [nombre],
    [direccion],
    [telefono],
    [marca],
    [numResolucion],
    [razonSocial],
    [prefijo],
    [fechaFacIni],
    [fechaFacFin],
    [rangoInicial],
    [rangoFinal],
    [Folio],
    [Tipo],
    [NumCertificado]
FROM [FE].[dbo].[ContingenciaResolucion]
"@

    $command = $connection.CreateCommand()
    $command.CommandText = $queryContingencia

    $reader = $command.ExecuteReader()
    while ($reader.Read()) {
        $registro = @{
            IdConsecutivo = $reader["IdConsecutivo"]
            nombre = $reader["nombre"]
            direccion = $reader["direccion"]
            telefono = $reader["telefono"]
            marca = $reader["marca"]
            numResolucion = $reader["numResolucion"]
            razonSocial = $reader["razonSocial"]
            prefijo = $reader["prefijo"]
            fechaFacIni = $reader["fechaFacIni"]
            fechaFacFin = $reader["fechaFacFin"]
            rangoInicial = $reader["rangoInicial"]
            rangoFinal = $reader["rangoFinal"]
            Folio = $reader["Folio"]
            Tipo = $reader["Tipo"]
            NumCertificado = $reader["NumCertificado"]
        }
        $registrosContingencia += $registro
    }
    $reader.Close()
    $connection.Close()
} catch {
    Write-Host "Error al consultar la tabla ContingenciaResolucion: $_" -ForegroundColor Red
    exit
}

# Extraer credenciales de autenticación
$credenciales = @{}
$credenciales["username"] = ($xmlCredenciales.configuration.appSettings.add | Where-Object { $_.key -eq "username" }).value
$credenciales["password"] = ($xmlCredenciales.configuration.appSettings.add | Where-Object { $_.key -eq "password" }).value
$credenciales["plataform"] = ($xmlCredenciales.configuration.appSettings.add | Where-Object { $_.key -eq "plataform" }).value

# Convertir credenciales a JSON
$credencialesAPIJson = $credenciales | ConvertTo-Json -Depth 10

# URL del endpoint con XDEBUG_TRIGGER para forzar la depuración
$authUrl = "http://127.0.0.1:8000/api/get-softlogymicro-token?XDEBUG_TRIGGER=VSCODE"

# Crear sesión de solicitud para manejar cookies correctamente
$session = New-Object Microsoft.PowerShell.Commands.WebRequestSession
$session.Cookies.Add((New-Object System.Net.Cookie("XDEBUG_SESSION", "VSCODE", "/", "127.0.0.1")))

# Encabezados de la solicitud
$authHeaders = @{ 
    "Cookie" = "XDEBUG_SESSION=VSCODE; XDEBUG_TRIGGER=VSCODE"
    "Content-Type" = "application/json"
}

try {
    # Realizar la petición con la sesión de cookies
    $authResponse = Invoke-RestMethod -Uri $authUrl -Method Post -Headers $authHeaders -WebSession $session -Body $credencialesAPIJson -ContentType "application/json"    
    $token = $authResponse.access_token
    Write-Host "Token obtenido exitosamente." -ForegroundColor Green
} catch {
    Write-Host "Error al obtener el token: $_" -ForegroundColor Red
    exit
}

# Estructurar el JSON final para envío POST
$jsonFinal = @(
    @{ FeConfig = $jsonFEData.data },
    @{ SQLConfig = $registrosContingencia }
)

# Convertir a string JSON
$jsonFinalString = $jsonFinal | ConvertTo-Json -Depth 10

# Definir los encabezados para la solicitud REST final
$headers = @{
    "Cookie" = "XDEBUG_SESSION=VSCODE"
    "Authorization" = "Bearer $token"
    "Content-Type" = "application/json"
}

# URL del servicio REST final (modifícala según sea necesario)
$apiReportUrl = "http://127.0.0.1:8000/api/report-status-store"

# Enviar la solicitud POST con los datos y el token obtenido
try {
    $responseReport = Invoke-RestMethod -Uri $apiReportUrl -Method Post -Headers $headers -WebSession $session -Body $jsonFinalString -ContentType "application/json"
    Write-Host "Respuesta del servidor: $(ConvertTo-Json $responseReport -Depth 10)" -ForegroundColor Magenta
} catch {
    Write-Host "Error al enviar la solicitud: $_" -ForegroundColor Red
}
