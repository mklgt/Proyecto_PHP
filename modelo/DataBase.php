<?php
include "IDataBase.php";
include 'config/config.php';
/**
 * @method void conectar()
 * @method void desconectar()
 * @method void createDb()
 * @method void createTables()
 * @method void ejecutarSql()
 * @method void ejecutarSqlActualizacion()
 * @method void insertarInformacion()
 * @method void consultarInformacion()
 */
class DataBase implements IDataBase
{
    private $conexion;

    //Array que guarda las tablas que se van a crear en la base de datos
    private $tables = array(
        "ASIGF" => array('ID', 'ASIG', 'AULA', 'GRUP', 'PROF', 'HORASEM'),
        "NOMASIGF" => array('ID', 'ABREV', 'NOMBRE', 'ACTIV'),
        "PROFF" => array('ID', 'ABREV', 'NOMBRE', 'DEPART', 'EMAIL'),
        "GRUPF" => array('ID', 'ABREV', 'CURSO', 'GRUPO', 'MAXALUM', 'NIVEL', 'TURNO', 'DESCRIP'),
        "AULAF" => array('ID', 'ABREV', 'NOMBRE', 'EDIFICIO', 'MAXALUM'),
        "MARCOSF" => array('ID', 'HORA', 'INICIO', 'MINUTOS', 'MARCO'),
        "SOLUCF" => array('ASIG', 'DIA', 'GRUPO', 'HORA', 'NIVEL', 'PROF', 'SESIONES', 'TAREA', 'TURNO', 'MARCO')
    );

    //Crea una conexión sin base de datos, ejecuta la función que la crea y añade las tablas. Con control de errores y la API PDO
    public function conectar()
    {
        try {
            $this->conexion = new PDO('mysql:host=' . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $this->conexion->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            $this->conexion->exec('SET names utf8');

            //$this->createDb($this->conexion, DB_NAME);
            //$this->createTables($this->conexion, $this->tables);
            //$this->consultarInformacion();
        } catch (Exception $ex) {
            //Añadir este error en el array de errores
            //$error = "Error:no se pudó conectar a la base de datos;";
            echo "Error al conectar en la base de datos" . $ex->getMessage();
            exit();
        }
    }

    //Asigna null a la conexión para desconectar de la base de datos
    public function desconectar()
    {
        $this->conexion = null;
    }

    /**
     * Se le pasa como parámetros la conexion creada y el nombre de la base de datos a crear.
     * @param  mixed $conexion
     * @param  object $db_name
     */
    //Función que crea la base de datos, recibe la conexión creada y el nombre de esta
    public function createDb($conexion, $db_name)
    {
        $create = "CREATE DATABASE IF NOT EXISTS $db_name";
        $conexion->exec($create);
        $use = "USE $db_name";
        $conexion->exec($use);
    }

    /**
     * Llama al procedimiento almacenado en la base de datos 'aulasReservadas' y este nos devuelve 
     * un 0 si es que NO hay ninguna clase en el dia y horas que se indican
     * @param  Reserva $reserva
     * @return boolean true si existe una reserva ya y false si no hay reserva;
     */
    //Función que crea las tablas definidas anteriormente
    public function createTables($conexion, $tables)
    {
        foreach ($tables as $table => $campos) {
            foreach ($campos as $campo) {
                if ($campo == "ID") {
                    $conexion->query("CREATE TABLE IF NOT EXISTS $table ( $campo VARCHAR(4), PRIMARY KEY($campo) )");
                } else if (($campo == "ASIG" && $table == "SOLUCF")) {
                    $conexion->query("CREATE TABLE IF NOT EXISTS $table ( $campo VARCHAR(9) )");
                } else {
                    try {
                        $conexion->query("ALTER TABLE $table ADD $campo VARCHAR(40)");
                    } catch (Exception $ex) {
                        return;
                    }
                }
            }
        }
    }



    /**
     * Llama al procedimiento almacenado en la base de datos 'aulasReservadas' y este nos devuelve 
     * un 0 si es que NO hay ninguna clase en el dia y horas que se indican
     * @param  Reserva $reserva
     * @return boolean true si existe una reserva ya y false si no hay reserva;
     */
    public function ejecutarSql($sql)
    {
        $resul = $this->conexion->query($sql);
        return $resul;
    }

    /**
     * Llama al procedimiento almacenado en la base de datos 'aulasReservadas' y este nos devuelve 
     * un 0 si es que NO hay ninguna clase en el dia y horas que se indican
     * @param  Reserva $reserva
     * @return mixed true si existe una reserva ya y false si no hay reserva;
     */
    public function ejecutarSqlActualizacion($sql, $args)
    {
        try {
            $resul = $this->conexion->prepare($sql);
            $resul->execute($args);
            //
            // if (!$resul) {
            //     echo "<p>Error en la consulta.</p>";
            // } else {
            //     return $resul;
            // }

            return $resul;
        } catch (Exception $e) {
            echo "<p>Error: " . $e->getMessage() . "</p>\n";
        }
    }

    /**
     * Llama al procedimiento almacenado en la base de datos 'aulasReservadas' y este nos devuelve 
     * un 0 si es que NO hay ninguna clase en el dia y horas que se indican
     * @param  Reserva $reserva
     * @return boolean true si existe una reserva ya y false si no hay reserva;
     */
    //Función que se ejecuta un sola vez al crearse la base de datos e introducir los datos a consultar
    public function insertarInformacion($args, $tablaInsercion)
    {
        try {
            switch ($tablaInsercion) {
                case 'ASIGT':
                    $sql = "INSERT INTO asigf (ID, ASIG, AULA, GRUP, PROF, HORASEM) VALUES (:id, :asig, :aula, :grup, :prof, :horasem)";
                    break;
                case 'NOMASIGT':
                    $sql = "INSERT INTO nomasigf (ID, ABREV, NOMBRE, ACTIV) VALUES (:id, :abrev, :nombre, :activ)";
                    break;
                case 'PROFT':
                    $sql = "INSERT INTO proff (ID, ABREV, NOMBRE, DEPART, EMAIL) VALUES (:id, :abrev, :nombre, :depart, :email)";
                    break;
                case 'GRUPT':
                    $sql = "INSERT INTO grupf (ID, ABREV, CURSO, GRUPO, MAXALUM, NIVEL, TURNO, DESCRIP) VALUES (:id, :abrev, :curso, :grupo, :maxalum, :nivel, :turno, :descrip)";
                    break;
                case 'AULAT':
                    $sql = "INSERT INTO aulaf (ID, ABREV, NOMBRE, EDIFICIO, MAXALUM) VALUES (:id, :abrev, :nombre, :edificio, :maxalum)";
                    break;
                case 'MARCOST':
                    $sql = "INSERT INTO marcosf (ID, HORA, INICIO, MINUTOS, MARCO) VALUES (:id, :hora, :inicio, :minutos, :marco)";
                    break;
                case 'SOLUCT':
                    $sql = "INSERT INTO solucf (ASIG, DIA, GRUPO, HORA, NIVEL, PROF, SESIONES, TAREA, TURNO, MARCO) VALUES (:asig, :dia, :grupo, :hora, :nivel, :prof, :sesiones, :tarea, :turno, :marco)";
                    break;

                default:
                    break;
            }

            $resul = $this->conexion->prepare($sql);
            foreach ($args as $arg => $valor) {
                $resul->bindParam($arg, $valor, PDO::PARAM_STR, 50);
                // switch ($arg) {
                //     case ':id':
                //         $resul->bindParam(':id', $valor, PDO::PARAM_INT, 2);
                //         break;
                //     case ':asig':
                //         $resul->bindParam(':asig', $valor, PDO::PARAM_STR, 4);
                //         break;
                //     case ':aula':
                //         $resul->bindParam(':aula', $valor, PDO::PARAM_STR, 4);
                //         break;

                //     default:

                //         break;
                // }
            }
            $resul->execute($args);
            return $resul;
        } catch (Exception $e) {
            $sql = "UPDATE asigf SET ASIG = :asig, AULA = :aula, GRUP = :grup, PROF = :prof, HORASEM = :horasem WHERE ID = :id";
            $resul = $this->conexion->prepare($sql);
            foreach ($args as $arg => $valor) {
                $resul->bindParam($arg, $valor, PDO::PARAM_STR, 50);
            }
            $resul->execute($args);
        }
    }

    /**
     * Llama al procedimiento almacenado en la base de datos 'aulasReservadas' y este nos devuelve 
     * un 0 si es que NO hay ninguna clase en el dia y horas que se indican
     * @param  Reserva $reserva
     * @return boolean true si existe una reserva ya y false si no hay reserva;
     */
    public function consultarInformacion()
    {
        if (file_exists('bbdd/test.xml')) {
            $xml = simplexml_load_file('bbdd/test.xml');
            $campos_registro = array();
            //$datos = ($xml->ASIGT);
            //$registros = ['ASIGF', 'NOMASIGF', 'PROFF', 'GRUPF', 'AULAF', 'MARCOSF', 'SOLUCF'];
            // echo "<pre>";
            // print_r($registros);
            // echo "</pre>";
            $xmla = get_object_vars($xml);



            foreach ($xmla as $coleccion => $valores) {
                foreach ($valores as $campo => $value) {
                    if ($campo != 'autor' && $campo != 'fecha' && $campo != 'modulo') {
                        if (!(in_array($campo, $campos_registro))) {
                            $campos_registro[] = $campo;
                        }
                    }
                }
                foreach ($campos_registro as $campos) {
                    if ($coleccion != '@attributes') {
                        $datos = ($xml->$coleccion);
                        foreach ($datos->$campos as $dato) {
                            foreach ($dato->attributes() as $key => $value) {
                                //echo "$key -> $value <br>";
                                switch ($key) {
                                    case 'ID':
                                        $id = strval($value);
                                        break;

                                    case 'ASIG':
                                        $asig = strval($value);
                                        break;

                                    case 'AULA':
                                        $aula = strval($value);
                                        break;

                                    case 'GRUP':
                                        $grup = strval($value);
                                        break;

                                    case 'PROF':
                                        $prof = strval($value);
                                        break;

                                    case 'HORASEM':
                                        $horasem = strval($value);
                                        break;

                                    case 'ABREV':
                                        $abrev = strval($value);
                                        break;

                                    case 'NOMBRE':
                                        $nombre = strval($value);
                                        break;

                                    case 'ACTIV':
                                        $activ = strval($value);
                                        break;

                                    case 'DEPART':
                                        $depart = strval($value);
                                        break;

                                    case 'EMAIL':
                                        $email = strval($value);
                                        break;

                                    case 'CURSO':
                                        $curso = strval($value);
                                        break;

                                    case 'MAXALUM':
                                        $maxalum = strval($value);
                                        break;

                                    case 'NIVEL':
                                        $nivel = strval($value);
                                        break;

                                    case 'TURNO':
                                        $turno = strval($value);
                                        break;

                                    case 'DESCRIP':
                                        $descrip = strval($value);
                                        break;

                                    case 'EDIFICIO':
                                        $edificio = strval($value);
                                        break;

                                    case 'HORA':
                                        $hora = strval($value);
                                        break;

                                    case 'INICIO':
                                        $inicio = strval($value);
                                        break;

                                    case 'MINUTOS':
                                        $minutos = strval($value);
                                        break;

                                    case 'MARCO':
                                        $marco = strval($value);
                                        break;

                                    case 'DIA':
                                        $dia = strval($value);
                                        break;

                                    case 'GRUPO':
                                        $grupo = strval($value);
                                        break;

                                    case 'SESIONES':
                                        $sesiones = strval($value);
                                        break;

                                    case 'TAREA':
                                        $tarea = strval($value);
                                        break;

                                    case 'TURNO':
                                        $turno = strval($value);
                                        break;

                                    default:
                                        break;
                                }
                            }

                            switch ($coleccion) {
                                case 'ASIGT':
                                    $args = array(':id' => $id, ':asig' => $asig, ':aula' => $aula, ':grup' => $grup, ':prof' => $prof, ':horasem' => $horasem);
                                    break;
                                case 'NOMASIGT':
                                    $args = array(':id' => $id, ':abrev' => $abrev, ':nombre' => $nombre, ':activ' => $activ);
                                    break;
                                case 'PROFT':
                                    $args = array(':id' => $id, ':abrev' => $abrev, ':nombre' => $nombre, ':depart' => $depart, ':email' => $email);
                                    break;
                                case 'GRUPT':
                                    $args = array(':id' => $id, ':abrev' => $abrev, ':curso' => $curso, ':grupo' => $grupo, ':maxalum' => $maxalum, ':nivel' => $nivel, ':turno' => $turno, ':descrip' => $descrip);
                                    break;
                                case 'AULAT':
                                    $args = array(':id' => $id, ':abrev' => $abrev, ':nombre' => $nombre, ':edificio' => $edificio, ':maxalum' => $maxalum);
                                    break;
                                case 'MARCOST':
                                    $args = array(':id' => $id, ':hora' => $hora, ':inicio' => $inicio, ':minutos' => $minutos, ':marco' => $marco);
                                    break;
                                case 'SOLUCT':
                                    $args = array(':asig' => $asig, ':dia' => $dia, ':grupo' => $grupo, ':hora' => $hora, ':nivel' => $nivel, ':prof' => $prof, ':sesiones' => $sesiones, ':tarea' => $tarea, ':turno' => $turno, ':marco' => $marco);
                                    break;

                                default:
                                    break;
                            }
                            $tablaInsert = strval($coleccion);
                            //$args = array(':id' => $id, ':asig' => $asig, ':aula' => $aula, ':grup' => $grup, ':prof' => $prof, ':horasem' => $horasem);
                            /*Ejecutar*/
                            $this->insertarInformacion($args, $tablaInsert);
                        }
                    }
                }
            }

            // foreach ($datos->ASIGF->attributes() as $dato => $valor) {
            //     echo "$dato -> $valor <br>";   
            // }

            //$args = array(':id' => 12, ':asig' => 'ASIR', ':aula' => 'DOBA' ); 
            //$consulta = "INSERT INTO asigf2 (ID, ASIG, AULA) VALUES (:id, :asig, :aula)";

        } else {
            exit('Error abriendo test.xml.');
        }
    }
    /*public function ejecutarSqlActualizacionPRUEBA($sql, $args)
{
try {

$resul = $this->conexion->prepare($sql);
foreach ($args as $arg => $valor) {
$resul->bindParam($arg, $valor, PDO::PARAM_STR, 50);
}
$resul->execute();
//
// if (!$resul) {
// echo "

//Error en la consulta.
";
// } else {
// return $resul;
// }
var_dump($resul);
return $resul;
} catch (Exception $e) {
echo "

Error: " . $e->getMessage() . "
\n";
}
}*/
}
