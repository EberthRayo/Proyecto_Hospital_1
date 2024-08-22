<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include_once __DIR__ . '/../Modelo/conexion.php';
require_once __DIR__ . '/../../EnvioCorreo/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../../EnvioCorreo/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../EnvioCorreo/PHPMailer/src/SMTP.php';




class CitaController
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function registrar_paciente($datos_paciente)
    {
        // Verificar si el ID de la EPS proporcionado existe en la tabla `entidades_promotoras_salud`
        $stmt_check_eps = $this->conexion->prepare("SELECT COUNT(*) FROM entidades_promotoras_salud WHERE ID = ?");
        $stmt_check_eps->bind_param("i", $datos_paciente['eps']);  // Cambiado a "i" para el tipo de dato integer
        $stmt_check_eps->execute();
        $stmt_check_eps->bind_result($count);
        $stmt_check_eps->fetch();
        $stmt_check_eps->close();

        if ($count == 0) {
            die('Invalid ID_Eps provided. The ID does not exist in the `entidades_promotoras_salud` table.');
        }

        // Preparar la sentencia SQL para insertar un nuevo paciente
        $stmt = $this->conexion->prepare(
            "INSERT INTO pacientes (ID_Paciente, Tipo_documento, Nombres, Fecha_Nacimiento, Edad, Genero, Direccion_Residencia, Numero_Telefono, Correo_Electronico, ID_Eps) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($this->conexion->error));
        }

        // Bind parameters
        $stmt->bind_param(
            "sssssssssi",  // Cambiado a "i" para el tipo de dato integer
            $datos_paciente['documento'],  // ID_Paciente
            $datos_paciente['tdocumento'],  // Tipo_documento
            $datos_paciente['nombre'],      // Nombres
            $datos_paciente['nacimiento'],  // Fecha_Nacimiento
            $datos_paciente['edad'],        // Edad
            $datos_paciente['genero'],      // Genero
            $datos_paciente['direccion'],   // Direccion_Residencia
            $datos_paciente['telefono'],    // Numero_Telefono
            $datos_paciente['correo'],      // Correo_Electronico
            $datos_paciente['eps']          // ID_Eps
        );

        // Ejecutar la sentencia
        $result = $stmt->execute();

        // Verificar errores
        if ($result === false) {
            die('Execute failed: ' . htmlspecialchars($stmt->error));
        }

        // Cerrar la sentencia
        $stmt->close();

        return $result;
    }


    public function registrar_medico($datos_medico)
    {
        if (!is_array($datos_medico)) {
            throw new Exception('Invalid data format. Array expected.');
        }

        foreach ($datos_medico as $key => $value) {
            if (empty($value)) {
                throw new Exception("El campo $key no puede ser nulo o vacío.");
            } else {
                echo "Clave: $key, Valor: $value\n";
            }
        }
        $stmt = $this->conexion->prepare("INSERT INTO medicos (ID_Medico, Nombres, Apellidos, ID_Especialidad_M, Horario_trabajo, Teléfono_contacto, Correo_electrónico, Consultorio, Estado_disponibilidad) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "sssssssss",
            $datos_medico['identificacion'],
            $datos_medico['nombres'],
            $datos_medico['apellidos'],
            $datos_medico['especialidad'],
            $datos_medico['horario_trabajo'],
            $datos_medico['telefono'],
            $datos_medico['email'],
            $datos_medico['consultorio'],
            $datos_medico['estado_disponibilidad']
        );
        $result = $stmt->execute();
        if (!$result) {
            throw new Exception("Error al insertar los datos: " . $stmt->error);
        }
        $stmt->close();
        return $result;
    }
    public function registrar_cita($datos_cita)
    {
        // Verificar si el paciente puede agendar una cita
        if (!$this->puede_agendar_cita($datos_cita['documento'])) {
            $_SESSION['error'] = "No puedes agendar una cita hasta después de " . $this->obtener_fecha_restriccion($datos_cita['documento']) . " o ya tienes el número máximo de citas activas.";
            return false;
        }

        // Prepare the SQL statement
        $stmt = $this->conexion->prepare(
            "INSERT INTO citas (ID_Paciente, ID_Disponibilidad_fecha, Id_Especialidad_M, ID_Medico, Motivo, Estado_Cita) VALUES (?, ?, ?, ?, ?, 'Confirmada')"
        );

        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($this->conexion->error));
        }

        // Bind parameters
        $stmt->bind_param(
            "iisss",
            $datos_cita['documento'],      // ID_Paciente
            $datos_cita['horafecha'],      // ID_Disponibilidad_fecha
            $datos_cita['especialidad'],   // Id_Especialidad_M
            $datos_cita['medico'],         // ID_Medico
            $datos_cita['motivo']          // Motivo
        );

        // Execute the statement
        $result = $stmt->execute();

        // Check for errors
        if ($result === false) {
            die('Execute failed: ' . htmlspecialchars($stmt->error));
        }

        // Close the statement
        $stmt->close();

        // Mark the date as occupied if the appointment was successfully created
        if ($result) {
            $this->marcarFechaComoOcupada($datos_cita['horafecha']);
        }

        return $result;
    }
    private function marcarFechaComoOcupada($id_disponibilidad_fecha)
    {
        // Prepare the SQL statement to mark the date and time as occupied
        $stmt = $this->conexion->prepare(
            "UPDATE fechahora_citas SET Disponible = 'Ocupada' WHERE ID_Disponibilidad_fecha = ?"
        );

        if ($stmt === false) {
            die('Error al preparar la sentencia: ' . $this->conexion->error);
        }

        // Bind the parameter
        $stmt->bind_param("i", $id_disponibilidad_fecha);

        // Execute the statement
        $result = $stmt->execute();

        if ($result === false) {
            die('Error al marcar la fecha como ocupada: ' . $stmt->error);
        }

        // Close the statement
        $stmt->close();
        return $result;
    }




    public function registrar_fechahora_disponible($datos_fechahora_disponible)
    {
        if (!is_array($datos_fechahora_disponible)) {
            throw new Exception("El parámetro debe ser un array");
        }

        $stmt = $this->conexion->prepare("INSERT INTO fechahora_citas (ID_Disponibilidad_fecha, Fecha_hora, Disponible) VALUES (?,?,?)");
        if ($stmt === false) {
            throw new Exception("Falló la preparación de la declaración: " . $this->conexion->error);
        }

        $stmt->bind_param(
            "iss",
            $datos_fechahora_disponible['ID_DISPONIBLE_FECHA'],
            $datos_fechahora_disponible['fecha_hora'],
            $datos_fechahora_disponible['disponible']
        );

        $result = $stmt->execute();
        if ($result === false) {
            throw new Exception("Falló la ejecución de la declaración: " . $stmt->error);
        }

        $stmt->close();

        return $result;
    }
    public function registrar_usuario($datos_usuario)
    {
        $stmt = $this->conexion->prepare("INSERT INTO usuarios (nombre, correo, contrasena, Descripcion_profesional, foto_perfil, horario_trabajo, especialidad, tipo_usuario, token, reset_token, token_expiry) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "sssssssssss",
            $datos_usuario['nombre'],
            $datos_usuario['correo'],
            password_hash($datos_usuario['contrasena'], PASSWORD_BCRYPT),
            $datos_usuario['descripcion_profesional'],
            $datos_usuario['foto_perfil'],
            $datos_usuario['horario_trabajo'],
            $datos_usuario['especialidad'],
            $datos_usuario['tipo_usuario'],
            $datos_usuario['token'],
            $datos_usuario['reset_token'],
            $datos_usuario['token_expiry']
        );

        $result = $stmt->execute();
        $stmt->close();

        if ($result) {

            header("Location: ../../../../Vista/html/paneles/admind/usuarios.php");
            exit;
        } else {
            echo "Error al registrar el usuario.";
        }
    }





    // Método para buscar el pacientes, citas, fechas disponibles, consultorios y especialides ya sea para cancelar o busqueda de datos
    public function listar_pacientes()
    {
        $query = "SELECT * FROM pacientes";
        $result = mysqli_query($this->conexion, $query);
        if (!$result) {
            die('Consulta fallida: ' . mysqli_error($this->conexion));
        }
        $datos_pacientes = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_free_result($result);

        return $datos_pacientes;
    }
    public function listar_usuarios()
    {
        $query = "SELECT * FROM usuarios";
        $result = mysqli_query($this->conexion, $query);
        if (!$result) {
            die('Consulta fallida: ' . mysqli_error($this->conexion));
        }
        $datos_usuarios = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_free_result($result);

        return $datos_usuarios;
    }

    public function listar_fechahora()
    {
        $query = "SELECT * FROM fechahora_citas";
        $result = mysqli_query($this->conexion, $query);
        if (!$result) {
            die('Query failed: ' . mysqli_error($this->conexion));
        }
        $datos_fechaHora = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_free_result($result);
        return $datos_fechaHora;
    }

    public function listar_medicos()
    {
        $sql = "SELECT medicos.ID_Medico, medicos.Nombres, medicos.Apellidos, medicos.ID_Especialidad_M, medicos.Horario_trabajo, medicos.Teléfono_contacto, medicos.Correo_electrónico, medicos.ID_Consultorio_M, medicos.Estado_disponibilidad, especialidad_medico.Nombre_Especialidad, consultorio.Nombre AS Nombre
                FROM medicos
                LEFT JOIN especialidad_medico ON medicos.ID_Especialidad_M = especialidad_medico.ID_Especialidad_M
                LEFT JOIN consultorio ON medicos.ID_Consultorio_M = consultorio.ID_Consultorio_M";

        $result = $this->conexion->query($sql);
        $medicos = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $medicos[] = $row;
            }
        }

        return $medicos;
    }
    public function listar_consultorios()
    {
        $sql = "SELECT * FROM consultorio";
        $result = $this->conexion->query($sql);
        $consultorios = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $consultorios[] = $row;
            }
        }
        return $consultorios;
    }
    public function listar_especialidad()
    {
        $sql = "SELECT ID_Especialidad_M, Nombre_Especialidad, Descripcion FROM especialidad_medico";
        $result = mysqli_query($this->conexion, $sql);
        $especialidades = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $especialidades;
    }

    public function buscarCita()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar'])) {
            $documento = trim($_POST['buscar']);

            if (empty($documento)) {
                $_SESSION['error'] = 'Por favor, ingrese un número de documento.';
            } elseif (!is_numeric($documento)) {
                $_SESSION['error'] = 'El número de documento debe ser numérico.';
            } else {
                $stmt = $this->conexion->prepare("SELECT pacientes.*, citas.Fecha_hora, citas.Motivo FROM pacientes LEFT JOIN citas ON pacientes.ID_Paciente = citas.ID_Paciente WHERE pacientes.ID_Paciente = ?");
                $stmt->bind_param("s", $documento);
                $stmt->execute();
                $resultado = $stmt->get_result();

                if ($resultado->num_rows > 0) {
                    $resultados = [];
                    while ($fila = $resultado->fetch_assoc()) {
                        $resultados[] = $fila;
                    }
                    $_SESSION['resultados'] = $resultados;
                    $_SESSION['mensaje'] = 'Citas encontradas exitosamente.';
                } else {
                    $_SESSION['error'] = 'No se encontraron registros para el documento proporcionado.';
                }
                $stmt->close();
            }
        } else {
            $_SESSION['error'] = 'Por favor, ingrese un número de documento.';
        }

        header('Location: ../../Vista/html/interfazdeusuario/FormularioCita.php#consultar');
        exit();
    }


    public function buscar_cancelarCita($documento)
    {
        $mensaje = '';
        $resultados = [];
        $success = false; // Inicializa el estado de éxito

        if (empty($documento)) {
            $mensaje = 'Por favor, ingrese un número de documento.';
        } elseif (!is_numeric($documento)) {
            $mensaje = 'El número de documento debe ser numérico.';
        } else {
            // Consulta para obtener citas con detalles de fecha y hora
            $stmt = $this->conexion->prepare("
            SELECT pacientes.*, citas.ID_Disponibilidad_fecha, citas.Motivo, citas.Estado_Cita, citas.ID_cita, 
                   fechahora_citas.Fecha_Hora
            FROM pacientes
            LEFT JOIN citas ON pacientes.ID_Paciente = citas.ID_Paciente
            LEFT JOIN fechahora_citas ON citas.ID_Disponibilidad_fecha = fechahora_citas.ID_Disponibilidad_fecha
            WHERE pacientes.ID_Paciente = ?
        ");
            $stmt->bind_param("s", $documento);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows > 0) {
                while ($fila = $resultado->fetch_assoc()) {
                    // Añadir el resultado al array
                    $resultados[] = [
                        'ID_Paciente' => $fila['ID_Paciente'],
                        'Nombres' => $fila['Nombres'],
                        'Fecha_Hora' => $fila['Fecha_Hora'], // Asegúrate de que este campo está correctamente recuperado
                        'Motivo' => $fila['Motivo'],
                        'Estado_Cita' => $fila['Estado_Cita'],
                        'ID_cita' => $fila['ID_cita']
                    ];
                }
                $mensaje = 'Citas encontradas exitosamente para cancelación.';
                $success = true;
            } else {
                $mensaje = 'No se encontraron registros para el documento proporcionado.';
                $success = false;
            }
            $stmt->close();
        }

        return [
            'success' => $success,
            'message' => $mensaje,
            'data' => $resultados
        ];
    }


    public function listar_citas()
    {

        $query = "SELECT * FROM citas";
        $result = $this->conexion->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }



    public function buscarPacientes($query)
    {
        $query = "%" . $query . "%"; // Para usar en LIKE
        $sql = "SELECT * FROM pacientes WHERE Nombres LIKE ? OR ID_Paciente LIKE ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('ss', $query, $query);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }



    // Método para cancelar cita
    public function cancelarCita($id_cita, $documento, $Motivocan)
    {
        if (!empty($id_cita) && !empty($documento) && !empty($Motivocan)) {
            // Paso 1: Registrar el motivo de cancelación
            $datos_cancelar = [
                'documento' => $documento,
                'Motivocan' => $Motivocan
            ];

            if (!$this->motivo_cancelar($datos_cancelar)) {
                return ['success' => false, 'message' => 'Error al registrar el motivo de cancelación.'];
            }

            // Paso 2: Obtener el ID_Disponibilidad_fecha de la cita
            $stmt = $this->conexion->prepare("SELECT ID_Disponibilidad_fecha FROM citas WHERE ID_cita = ?");
            if ($stmt === false) {
                return ['success' => false, 'message' => 'Error al preparar la sentencia para obtener la disponibilidad.'];
            }

            $stmt->bind_param("i", $id_cita);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $stmt->close();
                return ['success' => false, 'message' => 'No se encontró la cita.'];
            }

            $cita = $result->fetch_assoc();
            $id_disponibilidad_fecha = $cita['ID_Disponibilidad_fecha'];
            $stmt->close();

            // Paso 3: Marcar la fecha y hora como disponible
            if (!$this->marcarFechaComoDisponible($id_disponibilidad_fecha)) {
                return ['success' => false, 'message' => 'Error al marcar la fecha como disponible.'];
            }

            // Paso 4: Actualizar el estado de la cita a 'Cancelada'
            $stmt = $this->conexion->prepare("UPDATE citas SET Estado_Cita = 'Cancelada' WHERE ID_cita = ?");
            if ($stmt === false) {
                return ['success' => false, 'message' => 'Error al preparar la sentencia para cancelar la cita.'];
            }

            $stmt->bind_param("i", $id_cita);
            $result = $stmt->execute();
            $stmt->close();

            if ($result) {
                return ['success' => true, 'message' => 'Cita cancelada exitosamente.'];
            } else {
                return ['success' => false, 'message' => 'Error al actualizar la cita.'];
            }
        } else {
            return ['success' => false, 'message' => 'Datos faltantes.'];
        }
    }
    private function marcarFechaComoDisponible($id_disponibilidad_fecha)
    {
        // Prepare the SQL statement to mark the date and time as available
        $stmt = $this->conexion->prepare(
            "UPDATE fechahora_citas SET Disponible = '1' WHERE ID_Disponibilidad_fecha = ?"
        );

        if ($stmt === false) {
            die('Error al preparar la sentencia: ' . $this->conexion->error);
        }

        // Bind the parameter
        $stmt->bind_param("i", $id_disponibilidad_fecha);

        // Execute the statement
        $result = $stmt->execute();

        if ($result === false) {
            die('Error al marcar la fecha como disponible: ' . $stmt->error);
        }

        // Close the statement
        $stmt->close();
        return $result;
    }


    public function puede_cancelar_cita($fecha_cita, $hora_cita, $fecha_actual, $hora_actual)
    {
        $fechaHoraCita = new DateTime("$fecha_cita $hora_cita");
        $horaLimiteCancelacion = new DateTime("$fecha_cita 12:00 PM");
        $fechaHoraActual = new DateTime("$fecha_actual $hora_actual");

        // Verifica si la fecha y hora actuales son anteriores a la hora límite
        return $fechaHoraActual < $horaLimiteCancelacion;
    }

    public function getCitaDetails($id_cita)
    {
        $cita = [];
        $success = false;

        if (is_numeric($id_cita)) {
            $stmt = $this->conexion->prepare("
                SELECT citas.*, pacientes.Nombres, fechahora_citas.Fecha_Hora
                FROM citas
                LEFT JOIN pacientes ON citas.ID_Paciente = pacientes.ID_Paciente
                LEFT JOIN fechahora_citas ON citas.ID_Disponibilidad_fecha = fechahora_citas.ID_Disponibilidad_fecha
                WHERE citas.ID_cita = ?
            ");
            $stmt->bind_param("i", $id_cita);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows > 0) {
                $cita = $resultado->fetch_assoc();
                $success = true;
            } else {
                $cita['error'] = 'No se encontró la cita.';
            }

            $stmt->close();
        } else {
            $cita['error'] = 'ID de cita no válido.';
        }

        return [
            'success' => $success,
            'data' => $cita
        ];
    }

    public function eliminar_paciente($id_paciente)
    {
        $this->conexion->autocommit(FALSE);
        try {
            // Eliminar citas asociadas al paciente
            $stmt_citas = $this->conexion->prepare("DELETE FROM citas WHERE ID_Paciente = ?");
            $stmt_citas->bind_param("s", $id_paciente);
            $stmt_citas->execute();
            $stmt_citas->close();

            // Eliminar el paciente
            $stmt_paciente = $this->conexion->prepare("DELETE FROM pacientes WHERE ID_Paciente = ?");
            $stmt_paciente->bind_param("s", $id_paciente);
            $stmt_paciente->execute();
            $stmt_paciente->close();

            $this->conexion->commit();
            return true;
        } catch (Exception $e) {
            $this->conexion->rollback();
            return false;
        }
    }

    public function eliminar_FechaHora($FechaHora)
    {
        $stmt = $this->conexion->prepare("DELETE FROM fechahora_citas WHERE ID_Disponibilidad_fecha= ?");
        $stmt->bind_param("i", $FechaHora);
        return $stmt->execute();
    }

    public function eliminar_medico($id_medico)
    {
        $stmt = $this->conexion->prepare("DELETE FROM medicos WHERE ID_Medico = ?");
        $stmt->bind_param("i", $id_medico);
        return $stmt->execute();
    }

    public function obtenerEps()
    {
        $query = "SELECT ID, Nombre_eps FROM entidades_promotoras_salud";
        $result = $this->conexion->query($query);

        if (!$result) {
            die("Error en la consulta: " . $this->conexion->error);
        }

        $epsOptions = [];
        while ($row = $result->fetch_assoc()) {
            $epsOptions[] = [
                'id' => $row['ID'],
                'nombre' => $row['Nombre_eps']
            ];
        }

        return $epsOptions;
    }

    public function obtener_medico_por_id($id_medico)
    {
        $stmt = $this->conexion->prepare("SELECT * FROM medicos WHERE ID_Medico = ?");
        $stmt->bind_param("i", $id_medico);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function motivo_cancelar($datos_cancelar)
    {
        $stmt = $this->conexion->prepare("INSERT INTO cancelacioncita (ID_Paciente, Motivo) VALUES (?, ?)");
        $stmt->bind_param(
            "ss",
            $datos_cancelar['documento'],
            $datos_cancelar['Motivocan']
        );

        if ($stmt === false) {
            die('Prepare failed: ' . $this->conexion->error);
        }

        $result = $stmt->execute();

        if ($result === false) {
            die('Execute failed: ' . $stmt->error);
        }

        $stmt->close();
        return $result;
    }


    public function paciente_existe($documento)
    {
        $stmt = $this->conexion->prepare("SELECT * FROM pacientes WHERE ID_Paciente = ?");
        $stmt->bind_param("s", $documento);
        $stmt->execute();
        $result = $stmt->get_result();
        $existe = $result->num_rows > 0;
        $stmt->close();
        return $existe;
    }
    public function buscar_paciente($documento)
    {
        $stmt = $this->conexion->prepare("SELECT pacientes.*, citas.Fecha_hora, citas.Motivo FROM pacientes JOIN citas ON pacientes.ID_Paciente = citas.ID_Paciente WHERE pacientes.ID_Paciente = ?");
        $stmt->bind_param("s", $documento);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function obtener_cita_por_id($id_cita)
    {
        $query = "SELECT * FROM citas WHERE ID_cita = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param('i', $id_cita);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function obtener_fechas_disponibles()
    {
        $fechas = [];
        $fecha_actual = date('Y-m-d H:i:s'); // Obtener la fecha y hora actual
        $sql = "SELECT ID_Disponibilidad_fecha, Fecha_hora 
            FROM fechahora_citas 
            WHERE Disponible = '1' 
            AND Fecha_hora >= ?"; // Filtrar las fechas y horas pasadas

        if (isset($_SESSION['fechas_seleccionadas']) && is_array($_SESSION['fechas_seleccionadas']) && !empty($_SESSION['fechas_seleccionadas'])) {
            $fechas_seleccionadas = $_SESSION['fechas_seleccionadas'];
            $placeholders = implode(',', array_fill(0, count($fechas_seleccionadas), '?'));
            $sql .= " AND Fecha_hora NOT IN ($placeholders)";
            $stmt = $this->conexion->prepare($sql);

            $param_types = str_repeat('s', count($fechas_seleccionadas) + 1);
            $stmt->bind_param($param_types, $fecha_actual, ...$fechas_seleccionadas);
        } else {
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param('s', $fecha_actual);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $fechas[] = [
                    'ID_Disponibilidad_fecha' => $row['ID_Disponibilidad_fecha'],
                    'Fecha_hora' => $row['Fecha_hora']
                ];
            }
            $result->free();
        }
        $stmt->close();

        return $fechas;
    }

    // Dentro de CitaController

    public function obtenerMedicosPorEspecialidad($especialidadId)
    {
        $query = "SELECT ID_Medico, Nombres, Apellidos FROM medicos WHERE ID_Especialidad_M = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $especialidadId);
        $stmt->execute();
        $result = $stmt->get_result();

        $medicos = [];
        while ($row = $result->fetch_assoc()) {
            $medicos[] = $row;
        }

        $stmt->close();
        return $medicos;
    }


    public function obtener_ultima_cita($id_paciente)
    {
        // Obtener el inicio y fin de la semana actual
        $inicio_semana = date('Y-m-d 00:00:00', strtotime('monday this week'));
        $fin_semana = date('Y-m-d 23:59:59', strtotime('sunday this week'));

        error_log("Inicio de la semana: $inicio_semana, Fin de la semana: $fin_semana");

        // Consulta para obtener la última cita del paciente en la semana actual
        $sql = "SELECT c.ID_cita, c.ID_Paciente, c.ID_Disponibilidad_fecha, c.Id_Especialidad_M, c.ID_Medico, c.Motivo, c.Estado_Cita, c.Asistencia, f.Fecha_hora
            FROM citas c
            INNER JOIN fechahora_citas f ON c.ID_Disponibilidad_fecha = f.ID_Disponibilidad_fecha
            WHERE c.ID_Paciente = ? 
            AND f.Fecha_hora BETWEEN ? AND ? 
            ORDER BY f.Fecha_hora DESC 
            LIMIT 1";

        if ($stmt = $this->conexion->prepare($sql)) {
            $stmt->bind_param('iss', $id_paciente, $inicio_semana, $fin_semana);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $cita = $result->fetch_assoc();
                error_log("Cita encontrada: " . print_r($cita, true));
                return $cita;
            } else {
                error_log("No se encontró ninguna cita para el ID de paciente: $id_paciente en la semana actual.");
                return null;
            }
        } else {
            throw new Exception('Error en la consulta de base de datos.');
        }
    }




    public function obtener_fecha_hora_por_id($idDisponibilidadFecha)
    {
        $sql = "SELECT Fecha_hora FROM fechahora_citas WHERE ID_Disponibilidad_fecha = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $idDisponibilidadFecha);
        $stmt->execute();
        $result = $stmt->get_result();
        $fechaHora = $result->fetch_assoc();
        $stmt->close();
        return $fechaHora;
    }
    public function obtener_paciente_por_id($id_paciente)
    {
        $query = "SELECT * FROM pacientes WHERE ID_Paciente = ?";
        $stmt = $this->conexion->prepare($query);

        if ($stmt === false) {
            die('Prepare failed: ' . $this->conexion->error);
        }

        // 'i' indica que el parámetro es un entero. Cambia si es necesario.
        $stmt->bind_param('i', $id_paciente);

        if (!$stmt->execute()) {
            die('Execute failed: ' . $stmt->error);
        }

        $result = $stmt->get_result();

        if ($result === false) {
            die('Get result failed: ' . $stmt->error);
        }

        return $result->fetch_assoc();
    }


    public function obtener_consultorio()
    {
        $consultorios = [];
        $sql = "SELECT ID_Consultorio_M, Nombre FROM consultorio";

        $result = $this->conexion->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $consultorios[] = $row;
            }
        }
        return $consultorios;
    }
    public function obtener_especialidad()
    {
        $especialidades = [];
        $sql = "SELECT ID_Especialidad_M, Nombre_Especialidad FROM especialidad_medico";

        $result = $this->conexion->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $especialidades[] = $row;
            }
        }
        return $especialidades;
    }

    // Método para procesar y validar los campos de los formularios de ingreso de datos

    public function procesarFormularioCitaPanel()
    {
        if (!empty($_POST['documento']) && !empty($_POST['tdocumento']) && !empty($_POST['nombre']) && !empty($_POST['nacimiento']) && !empty($_POST['genero']) && !empty($_POST['direccion']) && !empty($_POST['telefono']) && !empty($_POST['correo']) && !empty($_POST['eps']) && !empty($_POST['cobertura']) && !empty($_POST['horafecha']) && !empty($_POST['motivo']) && !empty($_POST['area'])) {
            $datos_paciente = [
                'documento' => $_POST['documento'],
                'tdocumento' => $_POST['tdocumento'],
                'nombre' => $_POST['nombre'],
                'nacimiento' => $_POST['nacimiento'],
                'genero' => $_POST['genero'],
                'direccion' => $_POST['direccion'],
                'telefono' => $_POST['telefono'],
                'correo' => $_POST['correo'],
                'eps' => $_POST['eps'],
                'cobertura' => $_POST['cobertura']
            ];

            $datos_cita = [
                'documento' => $_POST['documento'],
                'horafecha' => $_POST['horafecha'],
                'motivo' => $_POST['motivo'],
                'area' => $_POST['area']
            ];
            if (!$this->paciente_existe($datos_paciente['documento'])) {
                if ($this->registrar_paciente($datos_paciente)) {
                    if ($this->registrar_cita($datos_cita)) {
                        $this->marcarFechaComoOcupada($datos_cita['horafecha']);
                        header('Location: ../../../../Vista/html/paneles/admind/pacientes.php?mensaje=¡El paciente ha sido registrado exitosamente!');
                    } else {
                        header('Location: ../../Vista/html/interfazdeusuario/FormularioCita.php?error=Error al registrar la cita');
                    }
                } else {
                    header('Location: ../../Vista/html/interfazdeusuario/FormularioCita.php?error=Error al registrar el paciente');
                }
            } else {
                if ($this->registrar_cita($datos_cita)) {
                    $this->marcarFechaComoOcupada($datos_cita['horafecha']);
                } else {
                    header('Location: ../../Vista/html/interfazdeusuario/FormularioCita.php?error=Error al registrar la cita');
                }
            }
        } else {
            header('Location: ../../Vista/html/interfazdeusuario/FormularioCita.php?error=Por favor, complete todos los campos');
        }
        exit();
    }


    public function procesarFormularioCitaEPS() {
        $response = ['success' => false, 'message' => 'No se ha procesado nada.'];
    
        try {
            require_once 'D:/Xampp/htdocs/Proyecto_Hospital_1/proyecto_hospital_php/Vista/fpdf/PruebaV.php';

            if (!class_exists('PDF')) {
                $response['message'] = 'La clase PDF no se encontró. Revisa la ruta y el contenido del archivo PDF.php.';
                header('Content-Type: application/json');
                echo json_encode($response);
                ob_end_clean(); // Limpiar el buffer de salida
                exit;
            }

            if (
                !empty(trim($_POST['documento'])) &&
                !empty(trim($_POST['especialidad'])) &&
                !empty(trim($_POST['medico'])) &&
                !empty(trim($_POST['horafecha'])) &&
                !empty(trim($_POST['motivo']))
            ) {
                $datos_cita = [
                    'documento' => trim($_POST['documento']),
                    'especialidad' => trim($_POST['especialidad']),
                    'medico' => trim($_POST['medico']),
                    'horafecha' => trim($_POST['horafecha']),
                    'motivo' => trim($_POST['motivo'])
                ];
    
                $paciente = $this->obtener_paciente_por_id($datos_cita['documento']);
    
                if ($paciente) {
                    $id_paciente = $paciente['ID_Paciente'];
                    $restriccion_hasta = $this->obtener_fecha_restriccion($id_paciente);
    
                    // Verificar si hay una cita activa y vencida
                    $cita_activa_vencida = $this->contar_citas_activas($id_paciente);
                    $citas_perdidas = $this->contar_citas_perdidas($id_paciente, date('Y-m-d', strtotime('-2 months')));
    
                    // Verificar restricciones
                    if ($restriccion_hasta !== NULL && date('Y-m-d') <= $restriccion_hasta) {
                        $response['message'] = 'El paciente no puede agendar la cita debido a restricciones vigentes.';
                    } elseif ($cita_activa_vencida > 0) {
                        $response['message'] = 'El paciente ya tiene una cita activa o vencida.';
                    } elseif ($citas_perdidas > 2) {
                        $this->establecer_restriccion($id_paciente);
                        $response['message'] = 'El paciente tiene más de dos citas perdidas y no puede agendar una nueva cita por dos meses.';
                    } else {
                        // Verificar si el paciente ha creado una cita en la última semana
                        $fecha_actual = date('Y-m-d');
                        $fecha_inicio_semana = date('Y-m-d', strtotime($fecha_actual . ' - 6 days'));
                        $citas_ultima_semana = $this->contar_citas_activas_en_rango($id_paciente, $fecha_inicio_semana, $fecha_actual);
                        if ($citas_ultima_semana > 0) {
                            $response['message'] = 'El paciente ya ha creado una cita médica en la última semana y no puede crear otra.';
                        } else {
                            // Generar el PDF
                            $pdf = new PDF();
                            $pdf->AddPage();
                            $pdf->SetTitle('Confirmación de Cita Médica');
    
                            $estado_cita = 'Confirmada'; 
                            $pdf->ReportData($paciente, $datos_cita, $estado_cita);
    
                            $pdfFilePath = 'D:/Xampp/htdocs/Proyecto_Hospital_1/proyecto_hospital_php/Controlador/alertas/reportes_citas/Cita_Medica_' . $datos_cita['documento'] . '.pdf';
                            $dir = dirname($pdfFilePath);
                            if (!is_dir($dir)) {
                                mkdir($dir, 0777, true);
                            }
                            $pdf->Output('F', $pdfFilePath);
    
                            // Enviar el correo electrónico
                            if ($this->enviarCorreoConfirmacion($pdfFilePath, $paciente['Correo_Electronico'])) {
                                // Registrar los datos en la base de datos
                                $registroCitaExito = $this->registrar_Cita($datos_cita, $paciente['ID_Paciente']);
    
                                if ($registroCitaExito) {
                                    $response['success'] = true;
                                    $response['message'] = 'La cita ha sido registrada y se ha enviado la confirmación por correo electrónico, Puedes revisar tu bandeja de entrada.';
                                } else {
                                    $response['message'] = 'Se ha registrado la cita, pero no se pudo guardar en la base de datos.';
                                }
                            } else {
                                $response['message'] = 'Se ha registrado la cita, pero no se pudo enviar el correo electrónico.';
                            }
                        }
                    }
                } else {
                    $response['message'] = 'El número de documento ingresado no corresponde a ningún paciente.';
                }
            } else {
                $response['message'] = 'Faltan datos necesarios para procesar la cita.';
            }
        } catch (Exception $e) {
            $response['message'] = 'Ocurrió un error: ' . $e->getMessage();
        }
    
        return $response;
    }
    
private function enviarCorreoConfirmacion($pdfFilePath, $toEmail)
{

    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = 0; 
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'danicrg05@gmail.com';
            $mail->Password = 'oqbi utlj zkjp xgsg'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

        // Remitente y destinatario
        $mail->setFrom('your_email@example.com', 'Hospital Serafín Montaña Cuellar');
        $mail->addAddress($toEmail);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Confirmacion de Cita Medica';
        $mail->Body    = 'Adjunto encontrará la confirmación de su cita médica.';
        $mail->AltBody = 'Adjunto encontrará la confirmación de su cita médica.';

        // Adjuntar el archivo PDF
        $mail->addAttachment($pdfFilePath);

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }

}

    private function obtener_nombre_eps_por_documento($documento_paciente)
    {
        $query = "SELECT Nombre_Eps FROM entidades_promotoras_salud WHERE documento_paciente = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("s", $documento_paciente);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row['Nombre_Eps'];
        }

        return 'No especificada';
    }



    public function procesarFromularioCitaParticular()
    {
        if (!empty($_POST['']));
    }

    public function procesarFormularioFechaHora()
    {
        if (!empty($_POST['fecha_hora']) && !empty($_POST['disponible'])) {
            $datos_fechaHora = [
                'fecha_hora' => $_POST['fecha_hora'],
                'disponible' => $_POST['disponible']
            ];
            if ($this->registrar_fechahora_disponible($datos_fechaHora)) {
                $_SESSION['mensaje'] = "La fecha y hora han sido registrados correctamente";
                header('Location: ../../../../Vista/html/paneles/admind/disponibilidad.php');
                exit();
            }
        }
    }
    public function procesarFormularioMedico()
    {
        if (!empty($_POST['identificacion']) && !empty($_POST['nombres']) && !empty($_POST['apellidos']) && !empty($_POST['especialidad']) && !empty($_POST['horario_trabajo']) && !empty($_POST['telefono']) && !empty($_POST['correo']) && !empty($_POST['consultorio']) && !empty($_POST['estado_disponibilidad'])) {
            $datos_medico = [
                'identificacion' => $_POST['identificacion'],
                'nombres' => $_POST['nombres'],
                'apellidos' => $_POST['apellidos'],
                'especialidad' => $_POST['especialidad'],
                'horario_trabajo' => $_POST['horario_trabajo'],
                'telefono' => $_POST['telefono'],
                'email' => $_POST['email'],
                'consultorio' => $_POST['consultorio'],
                'estado_disponibilidad' => $_POST['estado_disponibilidad']
            ];

            if ($this->registrar_medico($datos_medico)) {
                $_SESSION['alerta'] = 'El médico ha sido registrado correctamente';
            } else {
                $_SESSION['alerta'] = 'Error al registrar el médico';
            }

            header('Location: ../../../../Vista/html/paneles/admind/medicos.php');
            exit();
        }
    }


    //metodos para editar o actualizar datos
    public function editar_estado_cita($datos_cita)
    {
        $stmt = $this->conexion->prepare(
            "UPDATE citas 
             SET ID_Paciente = ?, 
                 ID_Disponibilidad_fecha = ?, 
                 Id_Especialidad_M = ?, 
                 Motivo = ?, 
                 Estado_cita = ?, 
                 Asistencia = ? 
             WHERE ID_cita = ?"
        );

        if (!$stmt) {
            die('Error de preparación: ' . $this->conexion->error);
        }

        $stmt->bind_param(
            "iissssi",
            $datos_cita['ID_Paciente'],
            $datos_cita['ID_Disponibilidad_fecha'],
            $datos_cita['Id_Especialidad_M'],
            $datos_cita['Motivo'],
            $datos_cita['Estado_cita'],
            $datos_cita['Asistencia'],
            $datos_cita['ID_Cita']
        );

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function editar_paciente($datos_paciente)
    {
        $stmt = $this->conexion->prepare("UPDATE pacientes SET Tipo_documento = ?, Nombres = ?, Fecha_Nacimiento = ?, Genero = ?, Direccion_Residencia = ?, Numero_Telefono = ?, Correo_Electronico = ?, Eps = ?, Cobertura = ? WHERE ID_Paciente = ?");
        $stmt->bind_param(
            "sssssssssi",
            $datos_paciente['Tipo_documento'],
            $datos_paciente['Nombres'],
            $datos_paciente['Fecha_Nacimiento'],
            $datos_paciente['Genero'],
            $datos_paciente['Direccion_Residencia'],
            $datos_paciente['Numero_Telefono'],
            $datos_paciente['Correo_Electronico'],
            $datos_paciente['Eps'],
            $datos_paciente['Cobertura'],
            $datos_paciente['ID_Paciente']
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    public function actualizar_medico($datos_medico)
    {
        $stmt = $this->conexion->prepare("UPDATE medicos SET Nombres = ?, Apellidos = ?, ID_Especialidad_M = ?, Horario_trabajo = ?, Teléfono_contacto = ?, Correo_electrónico = ?, Consultorio = ?, Estado_disponibilidad = ? WHERE ID_Medico = ?");
        if ($stmt === false) {
            echo "Error en la preparación de la consulta: " . $this->conexion->error;
            return false;
        }
        $stmt->bind_param(
            "sssssssii",
            $datos_medico['nombres'],
            $datos_medico['apellidos'],
            $datos_medico['especialidad'],
            $datos_medico['horario_trabajo'],
            $datos_medico['telefono'],
            $datos_medico['correo'],
            $datos_medico['consultorio'],
            $datos_medico['estado_disponibilidad'],
            $datos_medico['ID_Medico']
        );
        if ($stmt->execute()) {
            return true;
        } else {
            echo "Error al ejecutar la consulta: " . $stmt->error;
            return false;
        }
    }


    //metodo para restricciones
    public function contar_citas_perdidas($id_paciente, $fecha_limite)
    {
        $stmt = $this->conexion->prepare("
        SELECT COUNT(*) 
        FROM citas 
        WHERE ID_Paciente = ? 
          AND ID_Disponibilidad_fecha < ? 
          AND Estado_cita = 'No asistio'
    ");
        $stmt->bind_param("is", $id_paciente, $fecha_limite);
        $stmt->execute();
        $stmt->bind_result($numero_citas_perdidas);
        $stmt->fetch();
        $stmt->close();
        return $numero_citas_perdidas;
    }
    public function contar_citas_activas($id_paciente)
    {
        $stmt = $this->conexion->prepare("
        SELECT COUNT(*) 
        FROM citas 
        WHERE ID_Paciente = ? 
          AND Estado_cita != 'Cancelada'
    ");
        $stmt->bind_param("i", $id_paciente);
        $stmt->execute();
        $stmt->bind_result($numero_citas);
        $stmt->fetch();
        $stmt->close();
        return $numero_citas;
    }
    public function contar_citas_activas_en_rango($id_paciente, $fecha_inicio, $fecha_fin)
{
    $stmt = $this->conexion->prepare("
        SELECT COUNT(*) 
        FROM citas AS c
        INNER JOIN fechahora_citas AS df ON c.ID_Disponibilidad_fecha = df.ID_Disponibilidad_fecha
        WHERE c.ID_Paciente = ? 
          AND c.Estado_cita != 'Cancelada'
          AND df.Fecha_Hora BETWEEN ? AND ?
    ");
    $stmt->bind_param("iss", $id_paciente, $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $stmt->bind_result($numero_citas);
    $stmt->fetch();
    $stmt->close();
    return $numero_citas;
}



    public function puede_agendar_cita($id_paciente)
    {
        // Verificar si hay una restricción activa
        $stmt = $this->conexion->prepare("SELECT Restriccion_hasta FROM pacientes WHERE ID_Paciente = ?");
        $stmt->bind_param("i", $id_paciente);
        $stmt->execute();
        $stmt->bind_result($restriccion_hasta);
        $stmt->fetch();
        $stmt->close();

        // Si existe una restricción, verificar si la fecha actual está dentro del período de restricción
        if ($restriccion_hasta !== NULL) {
            $fecha_actual = date('Y-m-d');
            if ($fecha_actual <= $restriccion_hasta) {
                return false;
            } else {
                $this->eliminar_restriccion($id_paciente);
            }
        }
        $numero_citas_activas = $this->contar_citas_activas($id_paciente);
        if ($numero_citas_activas >= 1) {
            return false;
        }

        $fecha_limite = date('Y-m-d', strtotime('-2 months'));
        $citas_perdidas = $this->contar_citas_perdidas($id_paciente, $fecha_limite);
        if ($citas_perdidas > 0) {
            return false;
        }

        return true;
    }


    public function eliminar_restriccion($id_paciente)
    {
        $stmt = $this->conexion->prepare("UPDATE pacientes SET Restriccion_hasta = NULL WHERE ID_Paciente = ?");
        $stmt->bind_param("i", $id_paciente);
        $stmt->execute();
        $stmt->close();
    }
    public function obtener_fecha_restriccion($id_paciente)
    {
        $stmt = $this->conexion->prepare("SELECT Restriccion_hasta FROM pacientes WHERE ID_Paciente = ?");
        $stmt->bind_param("i", $id_paciente);
        $stmt->execute();
        $stmt->bind_result($restriccion_hasta);
        $stmt->fetch();
        $stmt->close();
        return $restriccion_hasta;
    }
    public function establecer_restriccion($id_paciente)
    {
        $fecha_actual = date('Y-m-d');
        $fecha_restriccion = date('Y-m-d', strtotime($fecha_actual . ' + 2 months'));

        $stmt = $this->conexion->prepare("UPDATE pacientes SET Restriccion_hasta = ? WHERE ID_Paciente = ?");
        $stmt->bind_param("si", $fecha_restriccion, $id_paciente);
        $stmt->execute();
        $stmt->close();
    }
}




//envio de correos 
