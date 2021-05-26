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
        "password" =>md5( $_POST["password"]),
        "nombre"=> $_POST["nombre"],
        "apellidos"=>$_POST["apellidos"],
        "telefono" => $_POST["telefono"],
        "fechaNacimiento"=>$_POST["fechaNacimiento"],
        "correo"=>$_POST["correo"]];
}
function datosInicioSesion(): array
{
    return $usuario = ["username" => $_POST["usernameCliente"],
        "password" =>md5($_POST["passwordCliente"])];
}
function SubirFotoCoche($nameImagen, $fileTmpPath, $extension, $matriculaCoche){
    $miConexion = mCreaConexionbd();

    if ($extension === "jpeg" || $extension === "jpg"){
        $origen = imagecreatefromjpeg($fileTmpPath);
    }
    else if($extension === "png"){
        $origen = imagecreatefrompng($fileTmpPath);
    }
    else{
        return -1;
    }
    $imagenGrande = imagescale( $origen, 1296, 864 );
    $imagenMediana = imagescale( $origen, 972, 648 );
    $imagenPequenia = imagescale( $origen, 240, 160 );
    if ($extension === "jpeg" || $extension === "jpg"){
        imagejpeg($imagenPequenia, "./uploadImages/small/".$nameImagen);
        imagejpeg($imagenGrande, "./uploadImages/big/".$nameImagen);
        imagejpeg($imagenMediana, "./uploadImages/medium/".$nameImagen);
    }
    else if($extension === "png"){
        imagepng($imagenPequenia, "./uploadImages/small/".$nameImagen);
        imagepng($imagenGrande, "./uploadImages/big/".$nameImagen);
        imagepng($imagenMediana, "./uploadImages/medium/".$nameImagen);
    }
    $consulta2 = "INSERT INTO final_imagenes(matricula,imagen) 
                      VALUES('$matriculaCoche','$nameImagen')";
    if($miConexion->query($consulta2)) {
        return 1;
    }
    return 0;

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
    $miConexion = mCreaConexionbd();
    $usuario = datosRegistroUsuario();
    $id = $_POST["id"];
    $rol = $_POST["rol"];
    $consulta = "update final_usuario 
    			 set idUsuario = '$usuario[username]', nombre = '$usuario[nombre]', apellidos = '$usuario[apellidos]', correo = '$usuario[correo]', fechaNacimiento = '$usuario[fechaNacimiento]', telefono = '$usuario[telefono]', contrasena = '$usuario[password]',rol = '$rol' 
    			 where id = '$id'";

    if ($resultado = $miConexion->query($consulta)){
        $_SESSION["contrasena"] = $usuario["password"];
        return 1;
    }

    else{
        return -1;
    }
}
function mEliminarPerfil():int
{
    $miConexion = mCreaConexionbd();
    $id = $_POST["id"];
    $consulta = "DELETE FROM final_usuario WHERE '$id' = id ";
    if ($resultado = $miConexion->query($consulta)){
        mCerrarSesion();
        return 1;
    }
    else{
        return -1;
    }
}
function mDatosUnaPersona()
{
    $miConexion = mCreaConexionbd();
    $id = $_SESSION["id"];
    $consulta = "select * from final_usuario where id = '$id'";

    if($resultado = $miConexion->query($consulta)){
        return $resultado;

    }else{
        return -1;
    }
}
function mDatosTodasPersonas(){
    $miConexion = mCreaConexionbd();
    $id = $_SESSION["id"];
    $consulta = "select * from final_usuario where rol = 'user'";

    if($resultado = $miConexion->query($consulta)){
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
        $miConexion = mCreaConexionbd();
        $id = $_GET["oidUsuario"];
        $consulta = "update final_usuario 
    			 set rol = 'admin' 
    			 where id = '$id'";

        if ($resultado = $miConexion->query($consulta)){
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
        $miConexion = mCreaConexionbd();
        $id = $_GET["oidUsuario"];
        $consulta = "DELETE FROM final_usuario WHERE id = '$id'";
        if ($resultado = $miConexion->query($consulta)){
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
                $contrasena = md5($linea[6]);
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
function mSubirCoche()
{
    $miConexion = mCreaConexionbd();
    $idPropietario = $_SESSION["idUsuario"];
    $marcaCoche = $_POST["marcaCoche"];
    $modeloCoche = $_POST["modeloCoche"];
    $matriculaCoche = $_POST["matriculaCoche"];
    $matriculaCoche = strtoupper($matriculaCoche);
    $descripcionCoche = $_POST["descripcionCoche"];
    $precioCoche = $_POST["precioCoche"];
    $fileTmpPath = $_FILES['fotoPrincipalCoche']['tmp_name'];
    $fileName = $_FILES['fotoPrincipalCoche']['name'];
    $extension = pathinfo($fileName,PATHINFO_EXTENSION);
    $nameImagen= uniqid().".".$extension;

    $consulta1 = "INSERT INTO final_vehiculo(matricula,idPropietario,marca,modelo,foto,precio,descripcion) 
                      VALUES('$matriculaCoche','$idPropietario','$marcaCoche','$modeloCoche','$nameImagen','$precioCoche','$descripcionCoche')";

    if($miConexion->query($consulta1)) {
        if($resultado = SubirFotoCoche($nameImagen,$fileTmpPath,$extension,$matriculaCoche)) {
            $_SESSION["matricula"] = $matriculaCoche;
            return 1;
        }
        return -1;
    }
    return -1;

}
function mSubirDropzone()
{
    $matricula = $_SESSION["matricula"];
    foreach (glob("./dropzone/images/" . '*') as $filename) {
        $name = basename($filename);
        $str = ".";
        $extensionarray = explode($str,$name);
        $extension = end($extensionarray);
        $nameImagen= uniqid().".".$extension;
        if (SubirFotoCoche($nameImagen,$filename, $extension, $matricula)===1) {
            if(!unlink($filename)){
                return-1;
            }
        }
        else{
            return-1;
        }
    }

    return 1;

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
function mInfoVehiculo ()
{
    $miConexion = mCreaConexionbd();
    $matricula = $_GET["matricula"];
    $consulta = "SELECT * FROM final_vehiculo,final_usuario where matricula = '$matricula' AND final_vehiculo.idPropietario = final_usuario.idUsuario";
    if ($resultado = $miConexion->query($consulta)){
        $datos = $miConexion->query($consulta);
        return $datos;
    }
    return -1;                  
}
function mObtenerComentarios()
{
    $miConexion = mCreaConexionbd();
    $matricula = $_GET["matricula"];
    $consulta = "SELECT * FROM final_comentario where matricula = '$matricula'";
    if ($resultado = $miConexion->query($consulta)){
        $datos = $miConexion->query($consulta);
        return $datos;
    }
    return -1;                  
}
/*******************************Funciones de Administrador**************/
function mAnadirComentario(){
    $miConexion = mCreaConexionbd();
    $idUsuario = $_SESSION["idUsuario"];
    $comentario = $_POST["comentarios"];
    $matricula= $_GET["matricula"];
    if (empty($comentario)) {
            return -2;
    }

    else{
        $consulta = "INSERT INTO final_comentario (id, matricula, idUsuario,comentario) 
                        VALUES (NULL, '$matricula', '$idUsuario', '$comentario')";
    }
    if($resultado = $miConexion->query($consulta)){
        return 1;
    }

    echo $consulta;
    return -1;

}
function mObtenerFotos(){
    $miConexion = mCreaConexionbd();
    $matricula = $_GET["matricula"];
    $consulta = "SELECT * FROM final_imagenes where matricula = '$matricula'";
    if ($resultado = $miConexion->query($consulta)){
        $datos = $miConexion->query($consulta);
        return $datos;
    }
    return -1;
}
function mBorrarComentario(){
    if (mGetRol() === "admin"){
        $miConexion = mCreaConexionbd();
        $idComentario = $_GET["idComentario"];
        $consulta = "DELETE FROM final_comentario where id = '$idComentario'";
    }
    if ( $miConexion->query($consulta)){

        return 2;
    }
    return -1;
}
function mEnviarSolicitudVenta(){
    $miConexion = mCreaConexionbd();
    $idPropietario = $_SESSION["idUsuario"];
    $marcaCoche = $_POST["marcaCoche"];
    $modeloCoche = $_POST["modeloCoche"];
    $matriculaCoche = $_POST["matriculaCoche"];
    $matriculaCoche = strtoupper($matriculaCoche);
    $descripcionCoche = $_POST["descripcionCoche"];
    $precioCoche = $_POST["precioCoche"];
    $consulta = "SELECT correo FROM final_usuario where idUsuario = '$idPropietario'";
    if ( $resultado = $miConexion->query($consulta)){
        $datos=$resultado->fetch_assoc();
        $para      = 'idoate.inigo@gmail.com';
        $titulo    = 'Solicitud venta Vehiculo';
        $mensaje   = 'Marca del coche: '.$marcaCoche."\r\n".
                     "Modelo del coche: ".$modeloCoche."\r\n".
                     "Matricula  del coche: ".$matriculaCoche."\r\n".
                     "Precio del coche".$precioCoche."\r\n".
                     "Descripcion del coche: ".$descripcionCoche;
        $cabeceras = 'From: ivanidoate@wildmotors.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();


        if(mail($para, $titulo, $mensaje, $cabeceras)){
            $mensajeuser = 'Has realizado tu solicitud para subir el vehiculo correctamente'."\r\n".$mensaje;
             $para = $datos["correo"];
            if( mail($para, $titulo, $mensajeuser, $cabeceras)) {
               return 1;
           }
     }
     return-1;

    }


}
function mEnviarSolicitudCompra(){
    $miConexion = mCreaConexionbd();
    $matricula = $_GET["matricula"];
    $usuarioInteresado = $_SESSION["idUsuario"];
    $consulta = "SELECT * FROM final_vehiculo v ,final_usuario u where v.matricula = '$matricula' AND u.idUsuario='$usuarioInteresado'";
    if ($resultado = $miConexion->query($consulta)){
        $datos = $resultado->fetch_assoc();
        $marcaCoche = $datos["marca"];
        $modeloCoche = $datos["modelo"];
        $matriculaCoche = $datos["matricula"];
        $descripcionCoche = $datos["descripcion"];
        $precioCoche = $datos["precio"];
        $mailUsuarioInteresado = $datos["correo"];
        $nombre = $datos["nombre"];
        $apellidos = $datos["apellidos"];
        $para      = 'idoate.inigo@gmail.com';
        $titulo    = 'Solicitud compra Vehiculo';
        $mensaje   = 'El cliente '.$nombre." ".$apellidos." cuyo usuario es ".$usuarioInteresado.
            'Esta interesado en el siguiente coche'."\r\n".
            'Marca del coche: '.$marcaCoche."\r\n".
            "Modelo del coche: ".$modeloCoche."\r\n".
            "Matricula  del coche: ".$matriculaCoche."\r\n".
            "Precio del coche".$precioCoche."\r\n".
            "Descripcion del coche: ".$descripcionCoche;
        $cabeceras = 'From: ivanidoate@wildmotors.com' ."\r\n" .
            'X-Mailer: PHP/' . phpversion();

        if(mail($para, $titulo, $mensaje, $cabeceras)){
            $mensajeUser = 'Has realizado tu solicitud de compra correctamente :'."\r\n".$mensaje;
            $para = $mailUsuarioInteresado;
            if( mail($para, $titulo, $mensajeUser, $cabeceras)) {
                return 1;
            }
            return -1;
        }
        return -1;
    }
    return -1;

}
function mvalidarPDF(){
    require('./templates/fpdf/fpdf.php');

    class PDF extends FPDF{
        // Cabecera de página
        function Header()
        {
            // Arial bold 15
            $this->SetFont('Arial','B',15);
            // Movernos a la derecha
            $this->Cell(65);
            // Título
            $this->Cell(60,10,'Listado de usuarios',1,0,'C');
            // Salto de línea
            $this->Ln(20);
        }

        // Pie de página
        function Footer()
        {
            // Posición: a 1,5 cm del final
            $this->SetY(-15);
            // Arial italic 8
            $this->SetFont('Arial','I',8);
            // Número de página
            $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
        }
    }

    $miConexion = mCreaConexionbd();
    $consulta = "select * from final_usuario";
    $resultado = $miConexion->query($consulta);
    
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(1,6,"", 0, 1,'C');
        $pdf->Cell(8, 6, 'id', 1 , 0,'C');
        $pdf->Cell(20,6,'Usuario', 1, 0,'C');
        $pdf->Cell(20,6,'Nombre', 1, 0,'C');
        $pdf->Cell(40,6,'Apellidos', 1, 0,'C');
        $pdf->Cell(55 ,6,'Correo', 1, 0,'C');
        $pdf->Cell(25,6,'fNacimiento', 1, 0,'C');
        $pdf->Cell(22,6,'tlfn', 1, 0,'C');
    while($row = $resultado->fetch_assoc()){
        $pdf->Cell(1,6,"", 0, 1,'C');
        $pdf->Cell(8, 6, $row['id'], 1 , 0,'C');
        $pdf->Cell(20,6,$row['idUsuario'], 1, 0,'C');
        $pdf->Cell(20,6,$row['nombre'], 1, 0,'C');
        $pdf->Cell(40,6,$row['apellidos'], 1, 0,'C');
        $pdf->Cell(55 ,6,$row['correo'], 1, 0,'C');
        $pdf->Cell(25,6,$row['fechaNacimiento'], 1, 0,'C');
        $pdf->Cell(22,6,$row['telefono'], 1, 0,'C');
    }
    $pdf->Output();
}

