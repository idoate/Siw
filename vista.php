<?php
function aniadirPlantilla($seccion, $urlPlantilla,$nombreEtiqueta):string{
    $plantilla = file_get_contents($urlPlantilla);
    return (str_replace($nombreEtiqueta,$plantilla,$seccion));
}
function obtenerCabecera($rolUsuario){
    $cabecera = file_get_contents("./templates/cabecera/cabecera.html");
    if ($rolUsuario === "user") {
        $cabecera = aniadirPlantilla($cabecera, "./templates/cabecera/seccionCabeceraLogueado.html", "##seccionLogueado##");
        $cabecera = aniadirPlantilla($cabecera, "./templates/cabecera/botonCabeceraLogueado.html", "##botonCabecera##");
    }
    else if ($rolUsuario === "admin"){
        $cabecera = aniadirPlantilla($cabecera, "./templates/cabecera/seccionCabeceraAdmin.html", "##seccionLogueado##");
        $cabecera = aniadirPlantilla($cabecera, "./templates/cabecera/botonCabeceraLogueado.html", "##botonCabecera##");
    }
    else{
        $cabecera = str_replace("##seccionLogueado##","",$cabecera);
        $cabecera = aniadirPlantilla($cabecera, "./templates/cabecera/botonCabeceraNoLogueado.html", "##botonCabecera##");
    }
    return $cabecera;
}
/***********************Funciones auxiliares***************************/
function vMostrarHome($rolUsuario){
    $page = file_get_contents("./templates/default_template.html");
    $cabecera = obtenerCabecera($rolUsuario);
    $seccion = file_get_contents("./templates/secciones/home.html");
    $slices = explode("##CONTENT##", $page);
    $page = $slices[0] .$cabecera .$seccion.$slices[1];
    $page = str_replace("##TITLE##","Home",$page);
    echo($page);
}
function vMostrarOfertas($rolUsuario){
    $page = file_get_contents("./templates/default_template.html");
    $cabecera = obtenerCabecera($rolUsuario);
    $seccion = file_get_contents("./templates/secciones/ofertas.html");
    $slices = explode("##CONTENT##", $page);
    $page = $slices[0] .$cabecera .$seccion.$slices[1];
    $page = str_replace("##TITLE##","Ofertas",$page);
    echo($page);
}
function vMostrarModelos($rolUsuario){
    $page = file_get_contents("./templates/default_template.html");
    $cabecera = obtenerCabecera($rolUsuario);
    $seccion = file_get_contents("./templates/secciones/modelos.html");
    $slices = explode("##CONTENT##", $page);
    $page = $slices[0] .$cabecera .$seccion.$slices[1];
    $page = str_replace("##TITLE##","Modelos",$page);
    echo($page);
}
function vMostrarServicios($rolUsuario){
    $page = file_get_contents("./templates/default_template.html");
    $cabecera = obtenerCabecera($rolUsuario);
    $seccion = file_get_contents("./templates/secciones/servicios.html");
    $slices = explode("##CONTENT##", $page);
    $page = $slices[0] .$cabecera .$seccion.$slices[1];
    $page = str_replace("##TITLE##","Servicios",$page);
    echo($page);
}
function vMostrarInicioSesion($rolUsuario){
    $page = file_get_contents("./templates/default_template.html");
    $cabecera = obtenerCabecera($rolUsuario);
    $seccion = file_get_contents("./templates/formularios/iniciarSesion.html");
    $slices = explode("##CONTENT##", $page);
    $page = $slices[0] .$cabecera .$seccion.$slices[1];
    $page = str_replace("##TITLE##","Iniciar Sesion",$page);
    echo($page);
}
function vMostrarRegistro($rolUsuario)
{
    $page = file_get_contents("./templates/default_template.html");
    $page = str_replace("##TITLE##", "Registrarse", $page);
    $slices = explode("##CONTENT##", $page);
    $cabecera = obtenerCabecera($rolUsuario);
    $seccion = file_get_contents("./templates/formularios/registrarse.html");
    if ($rolUsuario === "admin") {
        $seccion = str_replace("##registrar##", "./index.php?seccion=6&accion=altaUsuario&id=2", $seccion);
    } else {
        $seccion = str_replace("##registrar##", "./index.php?seccion=5&accion=registrarse&id=2", $seccion);
    }
    $page = $slices[0] . $cabecera . $seccion . $slices[1];
    echo($page);
}
function vMostrarPerfil($resultado,$tipo,$rolUsuario){
    $page = file_get_contents("./templates/default_template.html");
    $page = str_replace("##TITLE##","Perfil",$page);
    $cabecera = obtenerCabecera($rolUsuario);
    $slices = explode("##CONTENT##", $page);
    if(!is_object($resultado)){
        echo("Visualizacion de persona". "Se ha producido un error, vuelve a intentarlo mas tarde.");
    }
    else{
        $datos = $resultado -> fetch_assoc();
        if($tipo ==="visualizar"){
            $seccion = file_get_contents("./templates/secciones/verPerfil.html");
        }
        elseif ($tipo === "modificar"){
            $seccion = file_get_contents("./templates/formularios/modificarPerfil.html");
        }
        elseif ($tipo === "modificarPassword"){
            $seccion = file_get_contents("./templates/formularios/modificarPassword.html");
        }
        else{
            $seccion = file_get_contents("./templates/formularios/eliminarPerfil.html");
        }
        $seccion = str_replace("##oidUsuarios##",$datos["id"],$seccion);
        $seccion = str_replace("##idUsuario##", $datos["idUsuario"], $seccion);
        $seccion = str_replace("##nombre##", $datos["nombre"], $seccion);
        $seccion = str_replace("##apellidos##", $datos["apellidos"], $seccion);
        $seccion = str_replace("##correo##", $datos["correo"], $seccion);
        $seccion = str_replace("##fechaNacimiento##", $datos["fechaNacimiento"], $seccion);
        $seccion = str_replace("##telefono##", $datos["telefono"], $seccion);
        $seccion = str_replace("##password##", $datos["contrasena"], $seccion);
        $seccion = str_replace("##rol##",$datos["rol"],$seccion);

        $page = $slices[0] .$cabecera .$seccion.$slices[1];
        echo($page);
    }
}
function vMostrarAdmin($sesionIniciada, $rolUsuario){
    $page = file_get_contents("./templates/default_template.html");
    $page = str_replace("##TITLE##","Administrador",$page);
    $cabecera = obtenerCabecera($rolUsuario);
    $slices = explode("##CONTENT##", $page);
    if ($sesionIniciada === 1){
        $seccion = file_get_contents("./templates/secciones/admin.html");
    }
    else{
        vMostrarHome($rolUsuario);
    }
    $page = $slices[0] .$cabecera .$seccion.$slices[1];
    echo($page);
}
function vMostrarVentas($sesionIniciada, $rolUsuario){
    if($sesionIniciada === 1){
        $page = file_get_contents("./templates/default_template.html");
        $page = str_replace("##TITLE##","Ventas",$page);
        $cabecera = obtenerCabecera($rolUsuario);
        $slices = explode("##CONTENT##", $page);
        $seccion = file_get_contents("./templates/secciones/venderVehiculo.html");
        $page = $slices[0] .$cabecera .$seccion.$slices[1];
        echo($page);
    }
    else{
        vMostrarHome("Anonimo");
    }
}
function vMostrarReparacion($sesionIniciada, $rolUsuario){
    if($sesionIniciada === 1){
        $page = file_get_contents("./templates/default_template.html");
        $cabecera = obtenerCabecera($rolUsuario);
        $page = str_replace("##TITLE##","Reparacion",$page);
        $slices = explode("##CONTENT##", $page);
        $seccion = file_get_contents("./templates/secciones/repararVehiculo.html");
        $page = $slices[0] .$cabecera .$seccion.$slices[1];
        echo($page);
    }
    else{
        vMostrarHome("Anonimo");
    }
}
/***********************MostrarSecciones**************************/
function vMostrarResultadoInicioSesion($resultado,$rolUsuario){
    $page = file_get_contents("./templates/default_template.html");
    $page = str_replace("##TITLE##","Wild Motors",$page);
    $slices = explode("##CONTENT##", $page);
    $cabecera = obtenerCabecera($rolUsuario);
    if($resultado === 1){  //login correcto
        $seccion = file_get_contents("./templates/secciones/home.html");
        $userAlert = file_get_contents("./templates/userAlert/succes.html");
        $userAlert = str_replace("##mensaje##","Ha iniciado sesion correctamente", $userAlert);
    }
    else{
        $seccion = file_get_contents("./templates/formularios/iniciarSesion.html");
        $userAlert = file_get_contents("./templates/userAlert/error.html");
        if($resultado == -1) {  //login incorrecto
            $userAlert = str_replace("##mensaje##","Parametros incorrectos", $userAlert);
        }
        else if($resultado == -2){//fallo en la base de datos
            $userAlert = str_replace("##mensaje##","Fallo en la base de datos", $userAlert);
        }
    }
    $page = $slices[0] .$cabecera .$userAlert.$seccion.$slices[1];
    echo($page);
}
function vMostrarResultadoRegistro($resultado,$rolUsuario){
    $page = file_get_contents("./templates/default_template.html");
    $slices = explode("##CONTENT##", $page);
    $page = str_replace("##TITLE##","Wild Motors",$page);
    $cabecera = obtenerCabecera($rolUsuario);
    if ($resultado == 1) {  //registro correcto;
        $seccion = file_get_contents("./templates/secciones/home.html");
        $userAlert = file_get_contents( "./templates/userAlert/succes.html");
        $userAlert = str_replace("##mensaje##", "Se ha registrado correctamente",$userAlert);
    } else {
        $seccion = file_get_contents("./templates/formularios/registrarse.html");
        $userAlert = file_get_contents( "./templates/userAlert/error.html");
        if ($resultado == -1) {  //usuario Repetido
            $userAlert = str_replace("##mensaje##", "El usuario con el que se intenta registrar ya existe, por favor utilice otro nombre.",$userAlert);
        } else if ($resultado == -2) {//fallo en la base de datos
            $userAlert = str_replace("##mensaje##", "Ha habido un fallo con la base de datos, por favor intentelo mas tarde.", $userAlert);
        } else {//parametros incorrectos
            $userAlert = str_replace("##mensaje##", "Parametros introducidos incorrectos", $userAlert);
        }

    }
    $page = $slices[0] .$cabecera.$userAlert .$seccion.$slices[1];
    echo($page);
}
/***********************Acciones Login**************************/
function vMostrarResultadoModificarPerfil($resultado,$rolUsuario){
    $page = file_get_contents("./templates/default_template.html");
    $page = str_replace("##TITLE##","Wild Motors",$page);
    $cabecera = obtenerCabecera($rolUsuario);
    $slices = explode("##CONTENT##", $page);
    if($resultado === 1){
        $seccion = file_get_contents("./templates/secciones/home.html");
        $userAlert = file_get_contents("./templates/userAlert/succes.html");
        $userAlert = str_replace("##mensaje##","Se han modificado los datos correctamente",$userAlert);
    }
    else{
        $userAlert = file_get_contents("./templates/userAlert/error.html");
        $userAlert = str_replace("##mensaje##","ha habido un error modificando los datos, intentelo mas tarde",$userAlert);
        $seccion = file_get_contents("./templates/secciones/home.html");
    }
    $page = $slices[0] .$cabecera.$userAlert.$seccion.$slices[1];
    echo $page;
}
function vMostrarResultadoEliminarPerfil($resultado,$rolUsuario){
    $page = file_get_contents("./templates/default_template.html");
    $page = str_replace("##TITLE##","Wild Motors",$page);
    $cabecera = obtenerCabecera($rolUsuario);
    $slices = explode("##CONTENT##", $page);
    $seccion = file_get_contents("./templates/secciones/home.html");
    if($resultado === 1){
        $userAlert = file_get_contents("./templates/userAlert/succes.html");
        $userAlert = str_replace("##mensaje##","se ha eliminado correctamente el perfil",$userAlert);


    }
    else{
        $userAlert = file_get_contents("./templates/userAlert/error.html");
        $userAlert = str_replace("##mensaje##","No se ha podido eliminar el perfil , por favor intentelo mas tarde",$userAlert);
    }
    $page = $slices[0] .$cabecera.$userAlert .$seccion.$slices[1];
    echo($page);
}
/***********************Acciones Editar perfil**************************/
function vMostrarAltaPersona($sesionIniciada, $rolUsuario){
    if ($sesionIniciada === 1 ){
        vMostrarRegistro($rolUsuario);

    }
    else{
        vMostrarHome($rolUsuario);
    }
}
function vMostrarResultadoAltaPersona($sesionIniciada, $resultado, $rolUsuario){
    $page = file_get_contents("./templates/default_template.html");
    $cabecera = obtenerCabecera($rolUsuario);
    $page = str_replace("##TITLE##","Wild Motors",$page);
    $slices = explode("##CONTENT##", $page);
    if($sesionIniciada === 1){
        if ($resultado == 1){  //registro correcto;
            $seccion = file_get_contents("./templates/secciones/admin.html");
            $userAlert = file_get_contents("./templates/userAlert/succes.html");
            $userAlert = str_replace("##mensaje##", "Ha registrado al usuario correctamente", $userAlert);
        }
        else{
            $seccion = file_get_contents("./templates/formularios/registrarse.html");
            $userAlert = file_get_contents("./templates/userAlert/error.html");
            if ($resultado == -1) {  //usuario Repetido
                $userAlert = str_replace("##mensaje##", "El usuario con el que se intenta registrar ya existe, por favor utilice otro nombre.", $userAlert);
            } else if ($resultado == -2) {//fallo en la base de datos
                $userAlert = str_replace("##mensaje##", "Ha habido un fallo con la base de datos, por favor intentelo mas tarde.", $userAlert);
            } else {//parametros incorrectos
                $userAlert = str_replace("##mensaje##", "Parametros introducidos incorrectos", $userAlert);
            }

        }
    }
    $page = $slices[0] .$cabecera.$userAlert .$seccion.$slices[1];
    echo($page);
}
function vMostrarSeleccionUsuario($sesionIniciada,$rolUsuario){
    if ($sesionIniciada  === 1){
        if ($rolUsuario === "admin") {
            $page = file_get_contents("./templates/default_template.html");
            $cabecera = obtenerCabecera($rolUsuario);
            $page = str_replace("##TITLE##","Administrador",$page);
            $slices = explode("##CONTENT##", $page);
            $seccion = file_get_contents("./templates/formularios/editarUsuario.html");
            $page = $slices[0] .$cabecera .$seccion.$slices[1];
            echo($page);
        }
        else{
            vMostrarHome($rolUsuario);
        }
    }
    else{
        vMostrarHome($rolUsuario);
    }
}
function vMostrarResultadoSeleccionUsuario($sesionIniciada,$resultado,$rolUsuario){
    if ($sesionIniciada  === 1){
        $page = file_get_contents("./templates/default_template.html");
        $page = str_replace("##TITLE##","Wild Motors",$page);
        $cabecera = obtenerCabecera($rolUsuario);
        $slices = explode("##CONTENT##", $page);
           if($resultado === -3) {
               $seccion = file_get_contents("./templates/formularios/editarUsuario.html");
               $error = file_get_contents("./templates/userAlert/error.html");
               $error = str_replace("##mensaje##", "ha Habido un fallo en la consulta",$error);
               $page = $slices[0] .$cabecera.$error .$seccion.$slices[1];
               echo($page);
           }
           elseif($resultado === -2){
                $seccion = file_get_contents("./templates/formularios/editarUsuario.html");
                $error = file_get_contents("./templates/userAlert/error.html");
                $error = str_replace("##mensaje##", "El usuario existe pero es administrador",$error);
                $page = $slices[0] .$cabecera.$error .$seccion.$slices[1];
                echo($page);}
           elseif ($resultado === -1){
               $seccion = file_get_contents("./templates/formularios/editarUsuario.html");
               $error = file_get_contents("./templates/userAlert/error.html");
               $error = str_replace("##mensaje##", "no existe el usuario introducido",$error);
               $page = $slices[0] .$cabecera.$error.$seccion.$slices[1];
               echo($page);

           }
           else{
               $seccion = file_get_contents("./templates/secciones/resultadoEditarUsuario.html");
               $seccion = str_replace("##usuario##",$resultado->idUsuario,$seccion);
               $seccion = str_replace("##oidUsuario##",$resultado->id,$seccion);
               $page = $slices[0] .$cabecera .$seccion.$slices[1];
               echo($page);
           }
        }
    else{
        vMostrarHome($rolUsuario);
    }
}
function vMostrarResultadoNuevoAdministrador($resultado,$rolUsuario){
    $page = file_get_contents("./templates/default_template.html");
    $page = str_replace("##TITLE##","Wild Motors",$page);
    $cabecera = obtenerCabecera($rolUsuario);
    $slices = explode("##CONTENT##", $page);
    $seccion = file_get_contents("./templates/secciones/admin.html");
    if ($resultado == 1){
        $userAlert = file_get_contents("./templates/userAlert/succes.html");
        $userAlert = str_replace("##mensaje##", "Enhorabuena, has actualizado el rol del usuario correctamente",$userAlert);
    }
    else{
        $userAlert = file_get_contents("./templates/userAlert/error.html");
        $userAlert = str_replace("##mensaje##", "No se ha podido actualizar el rol del usuario",$userAlert);
    }
    $page = $slices[0] .$cabecera.$userAlert.$seccion.$slices[1];
    echo($page);
}
function vMostrarResultadoBorrarAdministrador($resultado,$rolUsuario){
    $page = file_get_contents("./templates/default_template.html");
    $page = str_replace("##TITLE##","Wild Motors",$page);
    $cabecera = obtenerCabecera($rolUsuario);
    $slices = explode("##CONTENT##", $page);
    $seccion = file_get_contents("./templates/secciones/admin.html");
    if ($resultado == 1){
        $userAlert = file_get_contents("./templates/userAlert/succes.html");
        $userAlert = str_replace("##mensaje##", "Enhorabuena, has eliminado el  usuario correctamente",$userAlert);
    }
    else{
        $userAlert = file_get_contents("./templates/userAlert/error.html");
        $userAlert = str_replace("##mensaje##", "No se ha podido eliminar el usuario, por favor, intentelo mas tarde",$userAlert);
    }
    $page = $slices[0] .$cabecera.$userAlert .$seccion.$slices[1];
    echo($page);
}
/***********************Acciones Administrador**************************/