<?php

/**
 * @method string verificarSelect($valor, $valormenu)
 */
class Utilidades
{

    /**
     * Comprueba y recupera los valores de los controles introducidos correctamente
     * @param  string $valor
     * @param  string $valormenu
     * @return string echo 'selected="selected"';
     */
    public static function verificarSelect($valor, $valormenu)
    {
        if ($valor === $valormenu) {
            echo 'selected="selected"';
        }
    }
    /*
* Este método se utilizará para verificar el valor de 'usuario'
*
public static function verificarUsuario($valor, $valormenu)
{
    if ($valor === $valormenu) {
        echo 'value=$valor';
    }
}
*/
}
