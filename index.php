<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(file_exists("archivo.txt")){

    $jsonClientes = file_get_contents("archivo.txt");
    $aClientes = json_decode($jsonClientes, true);

  } else {

    $aClientes = [];

  }
  
  $id = isset($_GET["id"])? $_GET["id"] : "";
  
  $aMensaje = ["mensaje" => "", "codigo" => ""];
  
  if(isset($_GET["do"]) && $_GET["do"] == "eliminar"){

    if($aClientes[$id]["imagen"] != ""){

      unlink("imagenes/" . $aClientes[$id]["imagen"]);

    }

   unset($aClientes[$id]);
   $jsonClientes = json_encode($aClientes);
   file_put_contents("archivo.txt", $jsonClientes);
   $id="";
   $aMensaje = ["mensaje" => "Cliente eliminado correctamente", "codigo" => "danger"];
   header("Refresh:2; url=index.php");

  }
  
  if($_POST){
    
    $dni = trim($_POST["txtDni"]);
    $nombre = trim($_POST["txtNombre"]);
    $telefono = trim($_POST["txtTelefono"]);
    $correo = trim($_POST["txtCorreo"]);
    $nombreImagen = "";
  
    if($_FILES["archivo"]["error"] === UPLOAD_ERR_OK){

        $nombreAleatorio = date("Ymdhmsi");
        $archivo_tmp = $_FILES["archivo"]["tmp_name"];
        $nombreArchivo = $_FILES["archivo"]["name"];
        $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $nombreImagen = "$nombreAleatorio.$extension";
        move_uploaded_file($archivo_tmp, "imagenes/$nombreImagen");

      }
  
      if(isset($_GET["id"])){
  
        $imagenAnterior = $aClientes[$id]["imagen"];
  
        if($_FILES["archivo"]["error"] === UPLOAD_ERR_OK){

          if($imagenAnterior != ""){

            unlink("imagenes/$imagenAnterior");

          }

        }  
        
        if($_FILES["archivo"]["error"] !== UPLOAD_ERR_OK){

          $nombreImagen = $imagenAnterior;

        }
        
        $aClientes[$id] = array(
          "dni" => $dni,
          "nombre" => $nombre,
          "telefono" => $telefono,
          "correo" => $correo,
          "imagen" => $nombreImagen
          );

          $aMensaje = ["mensaje" => "Cliente modificado correctamente", "codigo" => "primary"];
          header("Refresh:2; url=index.php");

      } else {

          $aClientes[] = array(
          "dni" => $dni,
          "nombre" => $nombre,
          "telefono" => $telefono,
          "correo" => $correo,
          "imagen" => $nombreImagen
          );

          $aMensaje = ["mensaje" => "¡El cliente ha sido guardado correctamente!", "codigo" => "success"];
          header("Refresh:2; url=index.php");

      }    
  
    $jsonClientes = json_encode($aClientes);
    file_put_contents("archivo.txt", $jsonClientes);
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

    <?php if($aMensaje["mensaje"] != ""): ?>

        <div class="row">
            <div class="col-12">
                <div class="alert alert-<?php echo $aMensaje["codigo"] ?>" role="alert">
                <?php echo $aMensaje["mensaje"]; ?>
                </div>
            </div>
        </div>

    <?php endif ?>

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
                                    <a href="?id=<?php echo $pos; ?>" data-toggle="tooltip" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="?id=<?php echo $pos; ?>&do=eliminar" data-toggle="tooltip" title="Eliminar"><i class="fa-solid fa-trash-can"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                </table>

            </div>
        </div>
    </main>
</body>
</html>