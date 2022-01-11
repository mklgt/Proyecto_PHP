<?php
class ValidadorForm
{

    private $errores = [];
    //private $reglasValidacion = null;
    private $valido = false;

    public function __construct()
    {
    }

    public function validar($fuente, $reglasValidacion)
    {

        foreach ($reglasValidacion as $campo => $valores) {
            if (isset($fuente[$campo])) {
                $campoAValidar = $fuente[$campo];
                foreach ($valores as $regla => $valorRegla) {

                    switch ($regla) {
                        case 'required':

                            if (is_null($campoAValidar) || empty($campoAValidar)) {
                                $this->addError($campo, "Es requerido");
                            }
                            break;

                        case 'min':
                            if (strtotime($campoAValidar)) {
                                if (strtotime($campoAValidar) < strtotime($valorRegla)) {
                                    $this->addError($campo, "Debe ser mayor que " . $valorRegla);
                                }
                            } else {
                                if (strlen($campoAValidar) < $valorRegla) {
                                    $this->addError($campo, "Debe ser mayor que " . $valorRegla);
                                }
                            }
                            break;

                        case 'max':
                            if (strtotime($campoAValidar)) {
                                if (strtotime($campoAValidar) > strtotime($valorRegla)) {
                                    echo "$campoAValidar > $valorRegla";
                                    $this->addError($campo, "Debe ser menor que " . $valorRegla);
                                }
                            } else {
                                if (strlen($campoAValidar) > $valorRegla) {
                                    $this->addError($campo, "Debe ser menor que " . $valorRegla);
                                }
                            }
                            break;

                        case 'value':
                            if (!($campoAValidar == $valorRegla)) {
                                $this->addError($campo, "Debe tener más de " . $valorRegla . " carácteres");
                            }
                            break;
                    }

                    /*if ($regla == "min") {
                        if (!($campoAValidar >= $valorRegla)) {
                            $this->addError($campo, "tiene que tener más de 8 carácteres");
                        }
                    }*/
                }
            } else {
                $this->addError($campo, "Es requerido");
            }
        }
    }

    public function addError($nombreCampo, $error)
    {
        //$this->errores[] = "El campo " . $nombreCampo . " : " . $error;
        $this->errores[] = "El campo " . $nombreCampo . " : " . $error;
    }

    public function getErrores()
    {
        return $this->errores;
    }

    public function getMensajeError($campo)
    {
        foreach ($this->errores as $campoError => $error) {
            if ($campoError == $campo) {
                echo $error;
            }
        }

        /*
        if (isset($this->errores[$campo])) {
            return $this->errores[$campo];
        }*/
    }

    public function esValido()
    {
        if (empty($this->errores)) {
            $this->valido = true;
        } else {
            $this->valido = false;
        }
        return $this->valido;
    }
}
