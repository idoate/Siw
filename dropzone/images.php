<?php
function showFiles($path) {
    $dir = opendir($path);
    $files = array();
    while ($current = readdir($dir)){
        if( $current != "." && $current != "..") {
            if(is_dir($path.$current)) {
                showFiles($path.$current.'/');
            }
            else {
                $files[] = $current;
            }
        }
    }
    echo '<h2>'.$path.'</h2>';
    echo '<div class="row">';
    for ($i=0; $i<count( $files ); $i++) {
        echo '<div class="col-xs-12 col-lg-2 text-center" style="margin-bottom:10px;">';
        echo '<span class="preview" style="width:160px;height: 160px;margin-bottom:7px;"> ';
        echo '<img src="images/'.$files[$i].'" style="max-width:160px;max-height: 160px;" />';
        echo '</span>';
        echo '<a class="btn btn-primary" href="images.php?remove='.$files[$i].'">Eliminar</a>';
        echo '</div>';
    }
    echo '</div>';
}

if (isset($_GET['remove'])) {
    if (file_exists('images/'.$_GET['remove'])) {
        unlink('images/'.$_GET['remove']);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dropzone Bootstrap Demo - Imágenes guardadas</title>
<meta name="description" content="Dropzone Bootstrap Demo - Imágenes guardadas."/>
<meta name="author" content="Jose Aguilar">
<link rel="shortcut icon" href="https://www.jose-aguilar.com/blog/wp-content/themes/jaconsulting/favicon.ico" />
<link rel="stylesheet" href="css/font-awesome.min.css">
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link rel="stylesheet" href="css/styles.css">
<script src="https://code.jquery.com/jquery-3.2.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</head>

<body>
<nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark">
    <a class="navbar-brand" href="https://www.jose-aguilar.com/">
        <img src="https://www.jose-aguilar.com/blog/wp-content/themes/jaconsulting/images/jose-aguilar.png" width="30" height="30" alt="Jose Aguilar">
      </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            <a class="nav-item nav-link active" href="index.php">Demo <span class="sr-only">(current)</span></a>
            <a class="nav-item nav-link" href="dropzone-multiple.zip">Descargar</a>
            <a class="nav-item nav-link" href="https://www.jose-aguilar.com/blog/dropzone-multiple-subida-de-archivos-con-bootstrap-y-php/">Tutorial</a>
            <a class="nav-item nav-link" href="https://www.jose-aguilar.com/">&copy; Jose Aguilar</a>
        </div>
    </div>
</nav>
<div class="container">
    <h1>Dropzone Bootstrap Imágenes guardadas</h1>
    <h2 class="lead">Uploaded images</h2>
    
    <nav aria-label="breadcrumb">
		<ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="https://www.jose-aguilar.com/blog/">Blog</a></li>
          <li class="breadcrumb-item"><a href="https://www.jose-aguilar.com/blog/dropzone-multiple-subida-de-archivos-con-bootstrap-y-php/">Dropzone múltiple subida de archivos con Bootstrap y PHP</a></li>
          <li class="breadcrumb-item" aria-current="page"><a href="index.php">Demo</a></li>
		  <li class="breadcrumb-item active" aria-current="page">Dropzone Bootstrap Imágenes guardadas</li>
        </ol>
    </nav>
    
    <div class="row">
        <div id="content" class="col-lg-12">
            <?php showFiles('images/'); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Bloque de anuncios adaptable -->
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-6676636635558550"
                 data-ad-slot="8523024962"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
    </div>
    
    <div class="card">
        <h5 class="card-header">Comparte en las redes sociales</h5>
        <div class="card-body">
            <h5 class="card-title">¿Te ha servido este ejemplo? Comparte con tus amigos</h5>
            <!-- Go to www.addthis.com/dashboard to customize your tools -->
            <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4ecc1a47193e29e4" async="async"></script>
            <!-- Go to www.addthis.com/dashboard to customize your tools -->
            <div class="addthis_sharing_toolbox"></div>
        </div>
    </div>

    <div class="footer-content row">
        <div class="col-lg-12">
            <div class="pull-right">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fa fa-reply"></i> Volver a la demo
                </a>
                <a href="dropzone-multiple.zip" class="btn btn-primary">
                    <i class="fa fa-download"></i> Descargar
                </a>
            </div>
        </div>
    </div>
    
</div>
<footer class="footer bg-dark">
    <div class="container">
        <span class="text-muted"><a href="https://www.jose-aguilar.com/">&copy; Jose Aguilar</a></span>
    </div>
</footer>
</body>
</html>
