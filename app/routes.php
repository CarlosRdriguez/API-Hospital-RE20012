<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
require __DIR__ . '/../src/db_sistemaHospital.php'; 

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    // Endpoints para la tabla de Doctores

    // Metodo POST para poder agregar un nuevo Doctor a la base de datos
    $app->post('/api/doctores/nuevo', function (Request $request, Response $response) {
        // Codigo para poder recuperar los datos enviados a la petición
        $parsedBody = $request->getParsedBody();
        
        $idDoctor = $parsedBody['IdDoctor'] ?? '';
        $nombres = $parsedBody['Nombres_Doctor'] ?? '';
        $apellidos = $parsedBody['Apellidos_Doctor'] ?? '';
        $especialidad = $parsedBody['Especialidad'] ?? '';
        $turno = $parsedBody['TurnoAtencion'] ?? '';
        $pacientes = $parsedBody['PacientesMinDiarios'] ?? 0;
        $sueldo = $parsedBody['NSueldo'] ?? 0.0;
        $idHospital = $parsedBody['IdHospital'] ?? ''; // Llave foránea

        $sql = "INSERT INTO doctores (IdDoctor, Nombres_Doctor, Apellidos_Doctor, Especialidad, TurnoAtencion, PacientesMinDiarios, NSueldo, IdHospital) 
                VALUES (:IdDoctor, :Nombres_Doctor, :Apellidos_Doctor, :Especialidad, :TurnoAtencion, :PacientesMinDiarios, :NSueldo, :IdHospital)";

        try {
            // Se instancia la conexión a la base de datos utilizando la clase db_sistemaHospital
            $db = new db_sistemaHospital();
            $conn = $db->conectDB();
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':IdDoctor', $idDoctor);
            $stmt->bindParam(':Nombres_Doctor', $nombres);
            $stmt->bindParam(':Apellidos_Doctor', $apellidos);
            $stmt->bindParam(':Especialidad', $especialidad);
            $stmt->bindParam(':TurnoAtencion', $turno);
            $stmt->bindParam(':PacientesMinDiarios', $pacientes);
            $stmt->bindParam(':NSueldo', $sueldo);
            $stmt->bindParam(':IdHospital', $idHospital);
            
            $stmt->execute();
            
            // Se cierra la conexión a la base de datos
            $conn = null;
            
            // Debe retornar una respuesta indicando que el Doctor fue agregado exitosamente
            $response->getBody()->write(json_encode(["mensaje" => "Doctor agregado exitosamente"]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);
                
        } catch (PDOException $e) {
            $error = array("error" => ["text" => $e->getMessage()]);
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    });

    //Metodo GET para obetener la lista de Doctores registrados en la base de datos
    $app->get('/api/doctores', function (Request $request, Response $response) {
        $sql = "SELECT * FROM doctores";

        try {
            $db = new db_sistemaHospital();
            $conn = $db->conectDB();
            $stmt = $conn->query($sql);
            $doctores = $stmt->fetchAll(PDO::FETCH_OBJ);
            $conn = null;

            $response->getBody()->write(json_encode($doctores));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
                
        } catch (PDOException $e) {
            $error = array("error" => ["text" => $e->getMessage()]);
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    });

    // Endpoints para la tabla de Hospitales

    // Metodo POST para agregar un nuevo Hospital a la base de datos
    $app->post('/api/hospitales/nuevo', function (Request $request, Response $response) {
        $parsedBody = $request->getParsedBody();
        
        $idHospital = $parsedBody['IdHospital'] ?? '';
        $nombre = $parsedBody['NomHospital'] ?? '';
        $capacidad = $parsedBody['CapacidadAtencion'] ?? '';
        $especialidades = $parsedBody['Especialidades'] ?? '';

        $sql = "INSERT INTO hospitales (IdHospital, NomHospital, CapacidadAtencion, Especialidades) 
                VALUES (:IdHospital, :NomHospital, :CapacidadAtencion, :Especialidades)";

        try {
            $db = new db_sistemaHospital();
            $conn = $db->conectDB();
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':IdHospital', $idHospital);
            $stmt->bindParam(':NomHospital', $nombre);
            $stmt->bindParam(':CapacidadAtencion', $capacidad);
            $stmt->bindParam(':Especialidades', $especialidades);
            
            $stmt->execute();
            $conn = null;
            
            $response->getBody()->write(json_encode(["mensaje" => "Hospital agregado exitosamente"]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);
                
        } catch (PDOException $e) {
            $error = array("error" => ["text" => $e->getMessage()]);
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    });

    //Metodo GET para obtener un Hospital en específico de la base de datos
    $app->get('/api/hospitales/{id}', function (Request $request, Response $response, array $args) {
        $idHospital = $args['id'];
        $sql = "SELECT * FROM hospitales WHERE IdHospital = :id";

        try {
            $db = new db_sistemaHospital();
            $conn = $db->conectDB();
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $idHospital);
            $stmt->execute();
            
            $hospital = $stmt->fetch(PDO::FETCH_OBJ);
            $conn = null;

            if ($hospital) {
                $response->getBody()->write(json_encode($hospital));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            } else {
                $response->getBody()->write(json_encode(["mensaje" => "Hospital no encontrado"]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);
            }
                
        } catch (PDOException $e) {
            $error = array("error" => ["text" => $e->getMessage()]);
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    });
};
