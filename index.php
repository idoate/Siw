<?php
include "modelo.php";
include "vista.php";
function asignarValor($variable,$default){
    if (isset ($_GET[$variable])){
        $valorVariable = $_GET[$variable];
    }
    else{
        if (isset ($_POST[$variable])){
            $valorVariable = $_POST[$variable];
        }
        else{
            $valorVariable = $default;
        }
    }
    return $valorVariable;
}
$seccion = asignarValor("seccion",1);
$accion = asignarValor("accion","");
$id = asignarValor("id",1);
session_start();

switch($seccion){
    case 1: // Seccion Home
        vMostrarHome(mGetRol());
        break;
    case 2: // Seccion Ofertas
        vMostrarOfertas(mGetRol());
        break;
    case 3: // Seccion Modelos
        vMostrarCatalogo(mCatalogoCoches(),mGetRol());
        break;
    case 4: // Seccion Servicios
        vMostrarServicios(mGetRol());
        break;
    case 5:  // Seccion Usuario
        if ($accion === "iniciarSesion"){
            switch ($id){
                case 1:
                    vMostrarInicioSesion(mGetRol());
                    break;
                case 2: // mostrarResultado
                    vMostrarResultadoInicioSesion(mConectarUsuario(),mGetRol());
                    break;
            }
        }
        else if ($accion === "registrarse"){
            switch ($id){
                case 1: //mostrar formulario
                    vMostrarRegistro(mGetRol());
                    break;
                case 2: // mostrarResultado
                    vMostrarResultadoRegistro(mRegistrarse(),mGetRol());
                    break;
            }
        }
        else if($accion === "mostrarPerfil"){//mostrarPerfil
            switch ($id){
                case 1:
                    vMostrarPerfil(mDatosUnaPersona(),"visualizar",mGetRol());
                    break;
                case 2: //cerrar sesion
                    mCerrarSesion();
                    vMostrarHome(mGetRol());
                    break;
                case 3://modificar persona
                    vMostrarPerfil(mDatosUnaPersona(),"modificar",mGetRol());
                    break;
                case 4://validad modificacion persona
                    vMostrarResultadoModificarPerfil(mModificarPerfil(),mGetRol());
                    break;
                case 5://eliminar perfil
                    vMostrarPerfil(mDatosUnaPersona(),"eliminar",mGetRol());
                    break;
                case 6://validar eliminacion persona
                    if (mSesionIniciada()){
                        vMostrarResultadoEliminarPerfil(mEliminarPerfil(),mGetRol());
                    }
                    else{
                        vMostrarHome("anonimo");
                    }
                    break;
                case 7://modificar contraseña
                    vMostrarPerfil(mDatosUnaPersona(),"modificarPassword",mGetRol());
                    break;
            }
        }
        break;

    case 6:  // Seccion Administrador
        if ($accion ==="mostrarAdmin"){
            switch ($id) {
                case 1:
                    vMostrarAdmin(mSesionIniciada(),mGetRol());
                    break;
            }
        }
        elseif($accion === "mostrarUsuarios"){
            switch ($id) {
                case 1:
                    # code...
                    vMostrarListadoPersonas(mDatosTodasPersonas(),mSesionIniciada(),    mGetRol());
                    break;
            }

        }
        elseif ($accion === "altaUsuario"){
            switch ($id) {
                case 1:
                    #mostrar el formulario de alta
                    vMostrarAltaPersona(mSesionIniciada(),mGetRol());
                    break;

                case 2:
                    #validar el alta de la persona
                    vMostrarResultadoAltaPersona(mSesionIniciada(),mRegistrarse(),mGetRol());
                    break;
            }
        }
        elseif ($accion === "editarUsuario"){
            switch ($id) {
                case 1:
                    #mostrar el formulario de seleccion de usuario
                    vMostrarSeleccionUsuario(mSesionIniciada(),mGetRol());
                    break;

                case 2:
                    #validar Seleccion usuario para hacerle administrador o elimirarlo
                    vMostrarResultadoSeleccionUsuario(mSesionIniciada(),mSeleccionarUsuario(),mGetRol());
                    break;

                case 3:
                    vMostrarResultadoNuevoAdministrador(mHacerAdministrador(),mGetRol());
                    break;
                case 4:
                    vMostrarResultadoBorrarAdministrador(mBorrarAdministrador(),mGetRol());
                    break;

            }
        }
        elseif($accion === "cargaMasivaUsuarios"){
            switch ($id){
                case 1:
                    vMostrarCargaMasivaUsuarios(mSesionIniciada(),mGetRol());
                    break;
                case 2:
                    vMostrarResultadoCargaMasivaUsuarios(mCargaMasivaUsuarios(),mGetRol());
                    break;
            }
        }
        elseif($accion === "subirModelo"){
            switch ($id){
                case 1:
                    vMostrarSubirModeloCoche(mSesionIniciada(),mObtenerMarcasDeCoche(),mGetRol());
                    break;
                case 2:
                    vMostrarResultadoSubirModeloCoche(mSubirCoche(),mGetRol());
                    break;
                case 3:
                    break;
            }
        }
        break;
    case 7: // Seccion Ventas
        vMostrarVentas(mSesionIniciada(),mGetRol());
        break;
    case 8: // Seccion Reparacion
        vMostrarReparacion(mSesionIniciada(),mGetRol());
        break;
}
