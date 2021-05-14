<?php
function comprobarDatosValidos($usuario):bool
{
    $allowedUser = array(".", "_");
    $allowedPassword = array(".", "_", " ");
    if(!ctype_alnum(str_replace($allowedUser, '', $usuario["username"] ))) {
        echo("username");
        return false;
    }
    if(!ctype_alnum(str_replace($allowedPassword, '', $usuario["password"] ))) {
        echo("password");
        return false;
    }
    return true;
}
function datosRegistroUsuario(): array
{
    return $nuevoUsuario =["username" =>  $_POST["usuario"],
        "password" => $_POST["password"],
        "nombre"=> $_POST["nombre"],
        "apellidos"=>$_POST["apellidos"],
        "telefono" => $_POST["telefono"],
        "fechaNacimiento"=>$_POST["fechaNacimiento"],
        "correo"=>$_POST["correo"]];
}
function datosInicioSesion(): array
{
    return $usuario = ["username" => $_POST["usernameCliente"],
        "password" =>$_POST["passwordCliente"]];
}
/******************************Funciones Auxiliaress********************/
function mCreaConexionbd()
{
    $db_host = 'localhost';
    $db_user = 'root';
    $db_password = "root";
    $db_db = "db_grupo33";
    /*
    $db_host = "dbserver";
    $db_user = "grupo33";
    $db_password = "aethahs5Le";
    $db_db = "db_grupo33";
    $db_port = 8889;
    */
    $miConexion = mysqli_connect($db_host,$db_user,$db_password, $db_db);
    if ($miConexion->connect_errno) {
        echo "Failed to connect to MySQL: " . $miConexion->connect_error;
        exit();
    }
    else{
        return $miConexion;
    }
}
function mGetRol(): string
{
    if(!isset($_SESSION["rol"])){
        $_SESSION["rol"]= "anonimo";
    }
    return $_SESSION["rol"];
}
function mSesionIniciada(): int
{
    $miConexion = mCreaConexionbd();
    if (isset($_SESSION["idUsuario"])) {
        $idUsuario = $_SESSION["idUsuario"];
        if (isset($_SESSION["contrasena"])) {
            $contrasena = $_SESSION["contrasena"];
            if (isset($_SESSION["rol"])) {
                $rol = $_SESSION["rol"];
                $consulta = "SELECT id, idUsuario, contrasena, rol  FROM final_usuario WHERE idUsuario = '".$idUsuario."'";
                if ($resultado = $miConexion->query($consulta)) {
                    if ($datosBBDD = $resultado->fetch_assoc()) {
                        if ($contrasena === $datosBBDD["contrasena"]) {
                            if ($rol === $datosBBDD["rol"]) {
                                return 1;
                            }
                            return -7;
                        }
                        return -6;
                    }
                    return-5;
                }
            return-4;
            }
        return -3;
        }
    return-2;
    }
return-1;
}
function mConectarUsuario(): int
{
    $usuario = datosInicioSesion();
    if(comprobarDatosValidos($usuario)) {
        $miConexion = mCreaConexionbd();
        $consulta = "SELECT id, idUsuario, contrasena, rol  FROM final_usuario WHERE idUsuario = '".$usuario["username"]."'";
        if (!$miConexion->query($consulta)){
            echo("Error description: " . $miConexion->error);
            return -2;//fallo en la consulta
        }
        else{
            $sql = $miConexion->query($consulta);
            $userData = mysqli_fetch_object($sql);
            if ($userData == FALSE){
                return -1;//BAD  Username
            }
            if ($userData->contrasena == $usuario["password"]){
                $_SESSION["id"] = $userData->id;
                $_SESSION["idUsuario"] = $usuario["username"];
                $_SESSION["contrasena"] = $usuario["password"];
                $_SESSION["rol"] = $userData->rol;
                return 1;//USERNAME Y PASSWORD OK

            }
            return -1;//fallo password
        }
    }
    else{
        return -1;//fallo alfanumerico
    }
}
function mRegistrarse(): int
{
    $nuevoUsuario = datosRegistroUsuario();
    if (comprobarDatosValidos($nuevoUsuario)) {
        $miConexion = mCreaConexionbd();
        $sql = $miConexion->query("SELECT idUsuario  FROM final_usuario WHERE idUsuario = '" . $nuevoUsuario["username"] . "'");
        $userData = mysqli_fetch_object($sql);
        if ($userData == FALSE) {
            if (!$miConexion->query("INSERT INTO final_usuario(idUsuario,nombre,apellidos,correo,fechaNacimiento,telefono,contrasena) VALUES ('$nuevoUsuario[username]','$nuevoUsuario[nombre]','$nuevoUsuario[apellidos]','$nuevoUsuario[correo]','$nuevoUsuario[fechaNacimiento]','$nuevoUsuario[telefono]','$nuevoUsuario[password]')")) {
                echo("Error description: " . $miConexion->error);//fallo en la consulta
                $miConexion->close();
                return -2;//fallo BBDD
            } else {
                $miConexion->close();
                return 1;//login correcto
            }
        }
        else{
            return-1;//usuario repetido
        }
    }
    else{
        return -3;//datos incorrectos
    }
}
function mCerrarSesion()
{
    session_unset ();
}
/*****************************Funciones de Sesion**********************/
function mModificarPerfil(): int
{
    $miconexion = mCreaConexionbd();
    $usuario = datosRegistroUsuario();
    $id = $_POST["id"];
    $rol = $_POST["rol"];
    $consulta = "update final_usuario 
    			 set idUsuario = '$usuario[username]', nombre = '$usuario[nombre]', apellidos = '$usuario[apellidos]', correo = '$usuario[correo]', fechaNacimiento = '$usuario[fechaNacimiento]', telefono = '$usuario[telefono]', contrasena = '$usuario[password]',rol = '$rol' 
    			 where id = '$id'";

    if ($resultado = $miconexion->query($consulta)){
        $_SESSION["contrasena"] = $usuario["password"];
        return 1;
    }

    else{
        return -1;
    }
}
function mEliminarPerfil():int
{
    $miconexion = mCreaConexionbd();
    $id = $_POST["id"];
    $consulta = "DELETE FROM final_usuario WHERE '$id' = id ";
    if ($resultado = $miconexion->query($consulta)){
        mCerrarSesion();
        return 1;
    }
    else{
        return -1;
    }
}
function mDatosUnaPersona()
{
    $miconexion = mCreaConexionbd();
    $id = $_SESSION["id"];
    $consulta = "select * from final_usuario where id = '$id'";

    if($resultado = $miconexion->query($consulta)){
        return $resultado;

    }else{
        return -1;
    }
}
/*******************************Funciones de Usuario*******************/
function mSeleccionarUsuario()
{
    $usuario = $_POST["usernameCliente"];
    $miConexion = mCreaConexionbd();
    $consulta = "SELECT id, idUsuario, rol  FROM final_usuario WHERE idUsuario = '".$usuario."'";
    if (!$miConexion->query($consulta)){
        echo("Error description: " . $miConexion->error);
        return -3;//fallo en la consulta
    }
    else{
        $datos = $miConexion->query($consulta);
        $userData = mysqli_fetch_object($datos);
        if ($userData == FALSE) {
            return -1;//No existe el usuario
        }
        if ($userData->rol === "user"){
            return $userData;//existe el usuario y tiene rol de user
        }
        else{
            return -2;//el usuario existe pero ya es administrador por lo que no puedes editarlo
        }
    }
}
function mHacerAdministrador(): int
{
    if(mSesionIniciada()&&$_SESSION["rol"]==="admin"){
        $miconexion = mCreaConexionbd();
        $id = $_GET["oidUsuario"];
        $consulta = "update final_usuario 
    			 set rol = 'admin' 
    			 where id = '$id'";

        if ($resultado = $miconexion->query($consulta)){
            return 1;
        }
        else{
            return -1;
        }
    }
    else{
        return -1;
    }

}
function mBorrarAdministrador(): int
{
    if(mSesionIniciada() && $_SESSION["rol"]==="admin"){
        $miconexion = mCreaConexionbd();
        $id = $_GET["oidUsuario"];
        $consulta = "DELETE FROM final_usuario WHERE id = '$id'";
        if ($resultado = $miconexion->query($consulta)){
            return 1;
        }
        else{
            return -1;
        }
    }
    else{
        return -1;
    }


}
function mCargaMasivaUsuarios(): int
{
    if(mGetRol() === "admin"){
        $miConexion  = mCreaConexionbd();
        if (isset($_POST["csv"])){
            $fileTmpName = $_FILES["csvUsuarios"]["tmp_name"];
            $file = fopen($fileTmpName,'r');
            while(($linea = fgetcsv($file,1000))!== false) {
                $idUsuario = $linea[0];
                $nombre = $linea[1];
                $apellidos = $linea[2];
                $correo = $linea[3];
                $fechaNacimiento = $linea[4];
                $telefono = $linea[5];
                $contrasena = $linea[6];
                $rol = $linea[7];
                $consulta = "insert into final_usuario (idUsuario, nombre, apellidos, correo, fechaNacimiento, telefono, contrasena, rol)
                         values ('$idUsuario', '$nombre', '$apellidos', '$correo', '$fechaNacimiento', '$telefono', '$contrasena', '$rol')";
                if (!$miConexion->query($consulta)) {//consulta incorrecta
                    fclose($file);
                    return -1;// ha habido un fallo leyendo el csv o  en la base de datos
                }
            }
            fclose($file);
            return 1;
        }
        return -2;// ha habido un fallo obteniendo  el csv
    }
    return -3; //no tienes permisos para hacer eso
}
function mObtenerMarcasDeCoche()
{
    $miConexion = mCreaConexionbd();
    $consulta = "SELECT marca FROM final_marca_coche";
    if ($resultado = $miConexion->query($consulta)){
        $datos = $miConexion->query($consulta);
        return $datos;
    }

}
function mSubirCoche():int
{
    $miConexion = mCreaConexionbd();
    $idPropietario = $_SESSION["idUsuario"];
    $marcaCoche = $_POST["marcaCoche"];
    $modeloCoche = $_POST["modeloCoche"];
    $matriculaCoche = $_POST["matriculaCoche"];
    $descripcionCoche = $_POST["descripcionCoche"];
    $precioCoche = $_POST["precioCoche"];
    $fileTmpPath = $_FILES['fotoPrincipalCoche']['tmp_name'];
    $fileName = $_FILES['fotoPrincipalCoche']['name'];
    $extension = pathinfo($fileName,PATHINFO_EXTENSION);
    $fotoPrincipal = uniqid().".".$extension;
    if(!move_uploaded_file($fileTmpPath,"./uploadImages/".$fotoPrincipal)){
        return -2;
    }
    else{
        $consulta = "INSERT INTO final_vehiculo(matricula,idPropietario,marca,modelo,foto,precio,descripcion) 
                      VALUES('$matriculaCoche','$idPropietario','$marcaCoche','$modeloCoche','$fotoPrincipal','$precioCoche','$descripcionCoche')";
    }
    if($resultado = $miConexion->query($consulta)){
        return 1;
    }
    echo $consulta;
    return -1;
}
function mCatalogoCoches()
{
    $miConexion = mCreaConexionbd();
    $consulta = "SELECT * FROM final_vehiculo ";
    if ($resultado = $miConexion->query($consulta)){
        $datos = $miConexion->query($consulta);
        return $datos;
    }
    return -1;

}
/*******************************Funciones de Administrador**************/

