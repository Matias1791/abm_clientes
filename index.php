<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(file_exists("archivo.txt")){
    $strJson = file_get_contents("archivo.txt");
    $aClientes = json_decode($strJson, true);
} else {
    $aClientes = array();
}

$id = isset($_GET["id"])? $_GET["id"] : "";

if(isset($_GET["do"]) && $_GET["do"] == "eliminar"){

    if(file_exists("imagenes/" . $aClientes[$id]["imagen"])){
        unlink("imagenes/" . $aClientes[$id]["imagen"]);     
    }


    unset($aClientes[$id]);
    $strJson = json_encode($aClientes);
    file_put_contents("archivo.txt", $strJson);
    header("refresh:2; url=index.php");
}

if($_POST){
    $dni = trim($_POST["txtDni"]);
    $nombre = trim($_POST["txtNombre"]);
    $telefono = trim($_POST["txtTelefono"]);
    $correo = trim($_POST["txtCorreo"]);
    $imagen = "";

    if($_FILES["archivo"]["error"] === UPLOAD_ERR_OK){
        if(isset($aClientes[$id]["imagen"]) && $aClientes[$id]["imagen"] != ""){
            if(file_exists("imagenes/" . $aClientes[$id]["imagen"])){
                unlink("imagenes/" . $aClientes[$id]["imagen"]);
            }
            if ($_FILES["archivo"]["error"] != UPLOAD_ERR_OK) {
                $imagen = "";
            }
        }
        $nombreAleatorio = date("Ymdhmsi");
        $archivo_tmp = $_FILES["archivo"]["tmp_name"];
        $nombreArchivo = $_FILES["archivo"]["name"];
        $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $imagen = "$nombreAleatorio.$extension";

        if($extension == "jpg" || $extension == "jpeg" || $extension == "png"){
            move_uploaded_file($archivo_tmp, "imagenes/$imagen");
        }        
    } else {
        
        if($id >= 0){
            $imagen = $aClientes[$id]["imagen"];
        } else {
            $imagen = "";
        }
    }

    if($id >= 0){
        $aClientes[$id] = array("dni" => $dni,
                            "nombre" => $nombre,
                            "telefono" => $telefono,
                            "correo" => $correo,
                            "imagen" => $imagen);
    } else {
        $aClientes[] = array("dni" => $dni,
                            "nombre" => $nombre,
                            "telefono" => $telefono,
                            "correo" => $correo,
                            "imagen" => $imagen);
    }
    
    $strJson = json_encode($aClientes);
    file_put_contents("archivo.txt", $strJson);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABM Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/f4e4c23b90.js" crossorigin="anonymous"></script>
</head>
<body>
    <main class="container">
        <?php if($_POST){ ?>
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                </svg>
                    <div class="ps-2">¡Guardado con éxito!</div>
            </div>
        <?php } ?>
        <?php if(isset($_GET["do"]) && $_GET["do"] == "eliminar"){ ?>
            <div class="alert alert-danger d-flex align-items-center" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
            </svg>
                    <div class="ps-2">Borrado correctamente.</div>
            </div>
        <?php } ?>
        <div class="row">
            <div class="col-12 text-center my-5">
                <h1>Registro de clientes</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <form action="" method="post" enctype="multipart/form-data"> 
                    <div>
                        <label for="txtDni">DNI: *</label>
                        <input type="text" name="txtDni" id="txtDni" class="form-control" required value="<?php echo isset($aClientes[$id]["dni"])? $aClientes[$id]["dni"] : ""; ?>">
                    </div>
                    <div>
                        <label for="txtNombre">Nombre: *</label>
                        <input type="text" name="txtNombre" id="txtNombre" class="form-control" required value="<?php echo isset($aClientes[$id]["nombre"])? $aClientes[$id]["nombre"] : ""; ?>">
                    </div>
                    <div>
                        <label for="txtTelefono">Telefono: </label>
                        <input type="text" name="txtTelefono" id="txtTelefono" class="form-control" value="<?php echo isset($aClientes[$id]["telefono"])? $aClientes[$id]["telefono"] : ""; ?>">
                    </div>
                    <div>
                        <label for="txtCorreo">Correo: *</label>
                        <input type="text" name="txtCorreo" id="txtCorreo" class="form-control" required value="<?php echo isset($aClientes[$id]["correo"])? $aClientes[$id]["correo"] : ""; ?>">
                    </div>
                    <div>
                        <label for="archivo">Archivo adjunto:</label>
                        <input type="file" name="archivo" id="archivo" accept=".jpg, .jpeg, .png">
                        <small class="d-block">Archivos admitidos: .jpg, .jpeg, .png</small>
                    </div>
                    <div>
                        <button type="submit" name="btnGuardar" class="btn btn-primary my-2">Guardar</button>
                        <a href="index.php" class="btn btn-secondary my-2">Nuevo</a>
                    </div>
                </form>
            </div>
            <div class="col-6 mt-2">
                <table class="table table-hover border">
                        <tr>
                            <th>Imagen</th>
                            <th>DNI</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Acciones</th>
                        </tr>
                        <?php foreach($aClientes as $pos => $cliente): ?>
                            <tr>
                                <td><img src="imagenes/<?php echo $cliente["imagen"]; ?>" class="img-thumbnail"></td>
                                <td><?php echo $cliente["dni"]; ?></td>
                                <td><?php echo $cliente["nombre"]; ?></td>
                                <td><?php echo $cliente["correo"]; ?></td>
                                <td>
                                    <a href="?id=<?php echo $pos; ?>" data-toggle="tooltip" title="Editar"><i class="fa-solid fa-pen-to-square"></i></i></a>
                                    <a href="?id=<?php echo $pos; ?>&do=eliminar" data-toggle="tooltip" title="Eliminar"><i class="fa-solid fa-trash-can"></i></a>
                                    <a href="index.php" data-toggle="tooltip" title="Confirmar cambios"><i class="fa-solid fa-check-double"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                </table>

            </div>
        </div>
    </main>
</body>
</html>