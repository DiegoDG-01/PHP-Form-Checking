<?php

#####################################################
#####################################################
##                                                 ##
##        ___ _                       ___  ___     ##
##       /   (_) ___  __ _  ___      /   \/ _ \    ##
##      / /\ / |/ _ \/ _` |/ _ \    / /\ / /_\/    ##
##     / /_//| |  __/ (_| | (_) |  / /_// /_\\     ##
##    /___,' |_|\___|\__, |\___/  /___,'\____/     ##
##                    |___/                        ##
##                                                 ##
#####################################################
#####################################################

# >>>>>>>>>>>>>>>>>>>>>>>
# >> PHP-Form-Checking >>
# >>>>>>>>>>>>>>>>>>>>>>>

// Created original version by: DiegoDG
// Created forked version by: DiegoDG
// Last update: 18/02/2021

// FORKED LIBRARY FOR SANITIZE FORMS
// FORK Version 2.0
//
// O = Original
// F = Fork
//
// O - V 1.0   20/01/2020
// O - v 1.1   16/07/2020
// O - v 1.2   21/08/2020
// F - v 2.0   16/02/2021
// F - v 2.0.1 18/02/2021

// => ##############################################################
// => # FOR GET MORE INFO TO LIBRARY, VISIT: github.com/DiegoDG-01 #
// => ##############################################################

// Changelog v2.0
//
// * Se realizo un cambio en la estructura que debe tener el arreglo para ser valido, esto con la idea de obtener
//   una mayor eficiencia de los recursos, evitando la redundancia ocasionada en la estructura anterior
//
//   Estructura anterior: 
//
//      1.- Array principal numerico
//      2.- Arrays secundarion que contendran el tipo de valor que sera validado asi como los datos a validar
//
//          Array( 0=>array('type'=>$VALOR, 'data'=>DATA),
//                 1=>array('type'=>$VALOR, 'data'=>DATA),)
//               );
//
//   Nueva estructura:
//
//      1.- Dos array principales Type (Se encargara de contener el tipo de dato a validadr)
//          y Data (este array contendra dentro de si un listado de arrays numericos los cuales tendran los datos a validar)
//
//         Array( Type=>('Int', 'Str', 'Email'),
//                Data=>(0=>array(1, Diego, ddg@gmail.com),
//                       n=>array(n, n, n))
//               );
//
//   NOTA: El cambio de estructura genera una inconpatibilidad de versiones, favor validar si es viable la actualizacion a esta version.
//
// * Admite pasar argumentos al ejecutar la validadcion que permite elegir el tipo de validacion
//   ya sea solo validacion de caracters, si no se encuentran campos vacios o ambas.
//
// * Integracion de caracteristica experimental para debug de la libreria.
//
// * Integracion de caracterisita experimental para permitir configurar cierto parametros de la libreria
//


// Changelog v2.0.1
//
// * Se Intercambio la estructura if por una estructura swtich en la validacion de que tipo de analisis que se reaalizara
//
// * Se agrego comentarios 
//

// => ##############################################################
// => # FOR GET MORE INFO TO LIBRARY, VISIT: github.com/DiegoDG-01 #
// => ##############################################################

// Generacion de la clase sanitize
class Sanitize
{

    // Variables que contendran toda la informacion del array que sera validado
    private $DataSize;
    private $DataCount;
    private $Empty = NULL;
    private $Character = NULL;
    private $Data = array('Type' => array(), 'Data' => array());

    // Funcion experimental, aun sin funcionamiento
    private $Debug = array('empty' => array(), 'character' => array());

    // Funcion para ejecutar la validcion de los datos
    public function Validate($AllData, $TypeVal = 'all', $Debugger = false) {

        // Almacena todos los datos necesarios para llevar a cabo la validadcion
        $this->Data['Type'] = $AllData['Type'];
        $this->Data['Data'] = $AllData['Data'];
        $this->DataSize = sizeof($AllData['Data']);
        $this->DataCount = sizeof($AllData['Data'][0]);

        // Elimina la varaiable que contiene los datos enviados por el usuario, esto para mantener los recursos controlados
        unset($AllData);

        // Valida el tipo de analasis que se realizara, por defecto ejecutara ambos analisis a no ser que el usuario modifique el comportamiento
        switch ($TypeVal) {
            case 'all':
                $response = $this->FullValidate();
            break;

            case 'empty':
                $response = $this->empty();
            break;

            case 'character':
                $response = $this->character();
            break;

            // Si ocurre un error en la comparacion retorna false para evitar fallos de seguridad
            default:
                return false;
            break;
        }

        // Funcion experimental, aun sin funcionamiento
        if($Debugger == TRUE) {
            var_dump($this->Debug);
            var_dump($this->Empty);
            var_dump($this->Character);
        }

        // Regresa la respuesta de la validacion
        return $response;
    }

    // Funcion experimental, aun sin funcionamiento
    public function configure(Type $var = null) {
        # code...
    }

    // Funcion para valida que los datos no se encuentren vacios
    function empty() {
        
        // Variable de control, determinana cuantos datos fueron validados exitosamente
        $EmptyValidation = 0;

        # Hace un recorrido por el primer nivel de arreglo, en este nivel se hace el recorrido por todo el listado que almacenan los datos que seran guardados
        for ($i = 0; $i < $this->DataSize; $i++) {

            # En este bucle que accede a la lista la cual contiene los datos que van a ser insertados a la Base de datos y por ende tienen que ser analizados
            for ($a=0; $a < $this->DataCount ; $a++) {

                # Compara que el dato enviado por el usuario no se encuentre vacio, ademas se realizar una segunda verificacion si es que por defecto
                # el sistema cuenta con un campo nulo o vacio no sea interpretado como error del ususraio
                if (!empty($this->Data['Data'][$i][$a]) | $this->Data['Type'][$a] == 'System') {

                    // Añade un punto positivo si no existen datos vacios o no son datos generados por el desarollador
                    $EmptyValidation += 1;
                } else {

                    // Detiene el bucle si encuentra datos vacios para evitar hacer uso de recursos inncesarios
                    break;
                }
            }
        }

        // Valida que los datos validos sean igual a la cantidad de datos que existen en el arreglo y regresa una respuesta
        if ($EmptyValidation == $this->DataCount) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function character()
    {
        // Variable de control, determinana cuantos datos fueron validados exitosamente
        $CharacterValidation = 0;

        // Funcion experimantal, este apartado aun se encuentra en desarrollo
        $ArrayDebugger = array();

        # Hace un recorrido por el primer nivel de arreglo, en este nivel se hace el recorrido por todo el listado que almacenan los datos que seran guardados
        for ($i = 0; $i < $this->DataSize; $i++) {

            # En este bucle que accede a la lista la cual contiene los datos que van a ser insertados a la Base de datos y por ende tienen que ser analizados
            for ($a=0; $a < $this->DataCount; $a++) { 

                // Se compara el tipo de valor a analizar mediante una estructura switch
                switch ($this->Data['Type'][$a]) {

                    // Valida que la estructura de una cadena contenga solo Strings o espacios
                    case 'Str':
                        // Expresion regular que valida que la cadena solo contenfa letras mayusculas o minusculas y espacios
                        $regex = '/^[a-zA-Z ]+$/i';

                        // Ejecuta la funcion para compara la expresion regular con el dato a validar
                        $result = preg_match_all($regex, $this->Data['Data'][$i][$a]);

                        // Compara el resultado de la comparacion de la expresion regular
                        if ($result == true) {
                            // Si el resultado es positivo se le asigna un punto
                            $CharacterValidation += 1;
                        } else {

                            // Si el resultado es negativo asigna la posicion del error en un array
                            array_push($ArrayDebugger, array('list'=>$i, 'value'=>$a));
                        }
    
                    break;

                    // Valida que la estructura de una cadena contenga solo Strings o espacios
                    case 'Int':
                        $regex = '/^[0-9]+$/';
    
                        $result = preg_match_all($regex, $this->Data['Data'][$i][$a]);
    
                        if ($result == true) {
                            $CharacterValidation += 1;
                        } else {
                            array_push($ArrayDebugger, array('list'=>$i, 'value'=>$a));
                        }
                    break;
                    
                    // Valida que la estructura de una cadena contenga solo Strings o espacios
                    case 'Str&Int':
                        $regex = '/^[a-zA-Z0-9 ]+$/';
    
                        $result = preg_match_all($regex, $this->Data['Data'][$i][$a]);
    
                        if ($result == true) {
                            $CharacterValidation += 1;
                        } else {
                            array_push($ArrayDebugger, array('list'=>$i, 'value'=>$a));
                        }
                    break;
    
                    case 'Email':
                        $regex = '/[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,3}$/';
    
                        $result = preg_match_all($regex, $this->Data['Data'][$i][$a]);
    
                        if ($result == true) {
                            $CharacterValidation += 1;
                        } else {
                            array_push($ArrayDebugger, array('list'=>$i, 'value'=>$a));
                        }
                    break;

                    case 'Pass':
                        # Se valida que la estrucutra de la contraseña contenga de 8-16 caracteres, Al menos una minuscula y una mayuscula
                        $regex = '/^(?=\w*[a-z])(?=\w*[A-Z])(?=\w*[0-9])\S{8,16}$/';
    
                        $result = preg_match_all($regex, $this->Data['Data'][$i][$a]);
    
                        if ($result == true) {
                            $CharacterValidation += 1;
                        } else {
                            array_push($ArrayDebugger, array('list'=>$i, 'value'=>$a));
                        }
                    break;
    
                    case 'Date':
                        # la estructura de la fecha a validar es: dd/mm/yyyy
                        $regex = '/^([0-3]{1}[0-9]{1})(\/|-)([0-1]{1}[0-9]{1})(\/|-)(\d{2,4})$/';
    
                        $result = preg_match_all($regex, $this->Data['Data'][$i][$a]);
    
                        if ($result == true) {
                            $CharacterValidation += 1;
                        } else {
                            array_push($ArrayDebugger, array('list'=>$i, 'value'=>$a));
                        }
                    break;
    
                    case 'RTT':
                        $regex = '/[a-zA-Z ]+/';
    
                        $result = preg_match_all($regex, $this->Data['Data'][$i][$a]);
    
                        if ($result == true) {
                            $CharacterValidation += 1;
                        } else {
                            array_push($ArrayDebugger, array('list'=>$i, 'value'=>$a));
                        }
                    break;
    
                    case 'Address':
                        $regex = '/[a-zA-Z ]+/';
    
                        $result = preg_match_all($regex, $this->Data['Data'][$i][$a]);
    
                        if ($result == true) {
                            $CharacterValidation += 1;
                        } else {
                            array_push($ArrayDebugger, array('list'=>$i, 'value'=>$a));
                        }
                    break;

                    // Si el desarollador necesita un campo vacio o nulo o agregar caracteres especiales por uno mismo esta funcion evita errores al validar esos datos
                    case 'System':
                        $CharacterValidation += 1;
                    break;

                    // Si ocurre un error en la comparacion retorna false para evitar fallos de seguridad
                    default:
                        return false;
                    break;
                }
            }
        }

        // Valida que los datos validos sean igual a la cantidad de datos que existen en el arreglo y regresa una respuesta
        if ($CharacterValidation == $this->DataCount) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function GetEmpty() {

        $this->Empty = $this->empty();

        if ($this->Empty == TRUE) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function GetCharacter() {

        $this->Character = $this->character();

        if ($this->Character == TRUE) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function FullValidate()
    {

        $this->Empty = $this->empty();
        $this->Character = $this->character();

        if ($this->Empty == TRUE && $this->Character == TRUE) {
            return TRUE;
        } else {
            return FALSE;
        }

    }

    private function Debugger(Type $var = null)
    {
        # code...
    }
}
