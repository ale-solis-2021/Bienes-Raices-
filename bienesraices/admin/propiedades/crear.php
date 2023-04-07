<?php

require '../../includes/funciones.php';
$autenticado = estaAutenticado();

if(!$autenticado) {
    header('Location: /');
}





//base de dato
  require '../../includes/config/database.php';
  $db = conectarDB();

  //consultar para obtener los vendedores
  $consulta = "SELECT * FROM vendedores";
  $resultado = mysqli_query($db,$consulta);

  //arreglos con msj de errores
  $errores = [];

  
  $titulo ='';
  $precio = '';
  $descripcion = '';
  $habitaciones = '';
  $wc = '';
  $estacionamiento = '';
  $vendedores_id = '';

  //ejecutar el codigo despues de que el usuario envie el formulario

  if($_SERVER['REQUEST_METHOD'] === 'POST'){
    

    // echo "<pre>";
    // var_dump($_POST);
    // echo "</pre>";
    
    // echo "<pre>";
    // var_dump($_FILES);
    // echo "</pre>";

    

    $titulo = mysqli_real_escape_string( $db,  $_POST['titulo']);
    $precio = mysqli_real_escape_string( $db,  $_POST['precio']);
    $descripcion = mysqli_real_escape_string( $db,  $_POST['descripcion']);
    $habitaciones = mysqli_real_escape_string( $db,  $_POST['habitaciones']);
    $wc = mysqli_real_escape_string( $db,  $_POST['wc']);
    $estacionamiento = mysqli_real_escape_string( $db,  $_POST['estacionamiento']);
    $vendedores_id = mysqli_real_escape_string( $db,  $_POST['vendedor']);
    $creado = date('Y/m/d');

    
    //asignar files hacia una variable
    $imagen = $_FILES['imagen'];
    

    if(!$titulo){
        $errores[] = "Debes de añadir un título";
    }

    if(!$precio){
        $errores[] = "El precio es obligatorio";
    }

    
    if(strlen($descripcion) < 50){
        $errores[] = "La descripción es obligatoria y debe de tener al menos 50 caracteres";
    }

    
    if(!$habitaciones){
        $errores[] = "El número de habitaciones es obligatorio";
    }

    
    if(!$wc){
        $errores[] = "El número de baños es obligatorio";
    }

    
    if(!$estacionamiento){
        $errores[] = "El número de lugares de estacionamientos es obligatorio";
    }

    
    if(!$vendedores_id){
        $errores[] = "Elige un vendedor";
    }

    if(!$imagen['name'] || $imagen['error']){
        $errores[] = 'la imagen es oblitoria';
    }

    //validar tamaño de imagen(1mb máximo)
   
    $medida  = 1000 * 1000;

    if($imagen['size'] > $medida){
        $errores[] = 'La imagen es muy pesada';
    }

    

    
    // echo "<pre>";
    // var_dump($errores);
    // echo "</pre>";

      
    //revisar que el array de errores este vacio

    if(empty($errores)){

        /** SUBIDA DE ARCHIVOS */
        //Crear carpeta
        $carpetaImagenes = '../../imagenes/';
        if(!is_dir($carpetaImagenes)){
            mkdir($carpetaImagenes);
        }

        //generar un nombre unico 
        $nombreImagen = md5(uniqid( rand(), true ) ) . ".jpg";
        

        //subir la imagen
        move_uploaded_file($imagen['tmp_name'],$carpetaImagenes . $nombreImagen );
        
        
        //insertar en la base de datos
        $query = " INSERT INTO propiedades (titulo, precio, imagen, descripcion,habitaciones,wc,estacionamiento,creado,
        vendedores_id) VALUES('$titulo','$precio','$nombreImagen','$descripcion','$habitaciones','$wc','$estacionamiento','$creado',
        '$vendedores_id')";
        
        //echo $query;
        
        $resultado = mysqli_query($db,$query);
        if($resultado){
            //redireccionar al usuario

            header('Location: /admin?resultado=1');
        }
    }

    

  }

  
  
 
  incluirtemplates('header');
 ?>

    <main class="contenedor seccion">
        <h1>Crear</h1>
        
        <a href="/admin" class="boton boton-verde">Volver</a>


        <?php foreach($errores as $error):?>
         <div class="alerta error">
             <?php echo $error;?>
         </div>   
            
        <?php endforeach;?>

        <form class="formulario" method="POST" action="/admin/propiedades/crear.php" enctype="multipart/form-data" >
            <fieldset>
                <legend>Informacion General</legend>

                <label for="titulo">Titulo:</label>
                <input type="text" id="titulo" name="titulo" placeholder="Titulo Propiedad" value="<?php echo $titulo;?>">

                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" placeholder="Precio Propiedad" value="<?php echo $precio;?>">

                <label for="imagen">Imagen:</label>
                <input type="file" id="imagen" accept="image/jpeg,image/png" name="imagen">

                <label for="descripcion">Descripcion:</label>
                <textarea id="descripcion" name="descripcion"><?php echo $descripcion; ?></textarea>
          
          </fieldset>

          <fieldset>
              <legend>Informacion de la Propiedad</legend>
              
              <label for="habitaciones">Habitaciones:</label>
              <input type="number" id="habitaciones" name="habitaciones" placeholder="ej:3" min="1" max="10" value="<?php echo $habitaciones;?>">

              <label for="wc">Baños:</label>
              <input type="number" id="wc" name="wc" placeholder="ej:3" min="1" max="10" value="<?php echo $wc;?>" >
              
              <label for="garage">Garage:</label>
              <input type="number" id="estacionamiento" name="estacionamiento" Garageholder="ej:3" min="0" max="10" value="<?php echo $estacionamiento;?>">


          </fieldset>

          <fieldset>
              <legend>Vendedor</legend>
              <select name="vendedor">
                  <option value="">--Selecciones--</option>
                  <?php while($vendedor = mysqli_fetch_assoc($resultado) ): ?>
                    <option <?php echo $vendedores_id === $vendedor['id'] ? 'selected': ''; ?> value="<?php echo $vendedor['id']; ?>">
                    <?php echo $vendedor['nombre'] . " " . $vendedor['apellido']; ?> </option>
                  <?php endwhile; ?>  
              </select>
          </fieldset>

          <input type="submit" value="Crear Propiedad" class="boton boton-verde">
        </form>

    </main>

    
<?php

incluirtemplates('footer');
?>