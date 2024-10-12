<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libros - Carlos Sáez</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <?php
    /**
    * Genera un formulario para insertar los datos de un libro
    * Si son correctos, los muestra por pantalla, pero si no lo son, muestra los errores pertinentes.
    * 
    * @author Kimo
    * @version 1.0
    */

    $title = $_POST['title'] ?? ''; //operador null coalescing (??) para comprobar si existe esa variable en $_POST   •   Si $_POST['title'] está definido y no es null, entonces asigna su valor a $title, de lo contrario, asigna una cadena vacía a $title.
    $author = $_POST['author'] ?? '';
    $publisher = $_POST['publisher'] ?? '';
    $language = $_POST['language'] ?? '';
    $year = $_POST['year'] ?? '2020-01';
    $isbn = $_POST['isbn'] ?? '';
    $sinopsis = $_POST['sinopsis'] ?? '';
    $cover = $_FILES['cover'] ?? '';
    $sample = $_FILES['sample'] ?? '';

    if (isset($_POST['reiniciar'])) {
        limpiar();
    }


    /**
    * HACEMOS LAS VALIDACIONES OPORTUNAS
    */

    $exprtitle = '/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]{2,30}$/';  // ^ y $ marcan el principio y final de la cadena
    $exprauthor = '/^([A-Z]\.\s[a-zA-ZáéíóúÁÉÍÓÚñÑ]+|[a-zA-ZáéíóúÁÉÍÓÚñÑ]+\s[a-zA-ZáéíóúÁÉÍÓÚñÑ]+)$/'; // \s es un espacio y \. un punto (escapado)  •  El * y el + indican la cantida de veces que puede aparecer
    $exprpublisher = '/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ]{2,20}$/';
    $exprisbn = '/^[0-9]{13}$/';
    $exprsinopsis = '/^(\b\w+\b[\s,.;]*){1,50}$/'; // \b\w+\b equivale a una palabra  •  [\s,.;] equivale a signos de puntuación que separan palabras
    
    $errtitle = ''; 
    $errauthor = '';
    $errpublisher = '';
    $errisbn = '';
    $errsinopsis = '';
    $errcover = '';
    $errsample = '';
    
    if(!preg_match($exprtitle, $title)){
        $errtitle = '<span class="error">Introduce un titulo de menos de 30 carácteres</span>';
    };
    
    if(!preg_match($exprauthor, $author)){
        $errauthor = '<span class="error">Introduce nombre y Apellido(s) del autor/a. Puedes utilizar inciales en el nombre</span>';
    };
    
    if(!preg_match($exprpublisher, $publisher)){
        $errpublisher = '<span class="error">Introduce una Editorial de menos de 20 carácteres</span>';
    };
    
    if(!preg_match($exprisbn, $isbn)){
        $errisbn = '<span class="error">El ISBN debe tener 13 dígitos</span>';
    };
    
    if(!preg_match($exprsinopsis, $sinopsis)){
        $errsinopsis = '<span class="error">Introduce una sinopsis de menos de 50 palabras</span>';
    };

    if (isset($_FILES['cover']) && $_FILES['cover']['error'] == UPLOAD_ERR_NO_FILE) {
        $errcover = '<span class="error">Por favor, añade una imagen de portada</span>';
    }

    if (empty($_POST)) {
        $errtitle = ''; 
        $errauthor = '';
        $errpublisher = '';
        $errisbn = '';
        $errsinopsis = '';
        $errcover = '';
    } else{  // COMPROBACIONES DE LA IMAGEN Y LA MUESTRA
        if($_FILES['cover']['error'] != UPLOAD_ERR_OK){
            switch ($_FILES['cover']['error']){
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $errcover = '<span class="error">El fichero es demasiado grande</span>';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errcover = '<span class="error">El fichero no se ha podido subir entero</span>';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errcover = '<span class="error">No se ha podido subir el fichero</span>';
                    break;
                default:
                $errcover = '<span class="error">Error indeterminado</span>';
            }
        }

        if($_FILES['cover']['type'] != "image/jpeg" && $_FILES['cover']['type'] != "image/png"){ 
            $errcover = '<span class="error">La portada debe ser en formato jpg o png</span>';
        }
        
        if($_FILES['sample']['error'] != UPLOAD_ERR_OK){
            switch ($_FILES['sample']['error']){
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $errsample = '<span class="error">El fichero es demasiado grande</span>';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errsample = '<span class="error">El fichero no se ha podido subir entero</span>';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errsample = '<span class="error">No se ha podido subir el fichero</span>';
                    break;
                default:
                $errsample = '<span class="error">Error indeterminado</span>';
            }
        }

        if($_FILES['sample']['type'] != "application/pdf"){ 
            $errcover = '<span class="error">La muestra debe ser en formato pdf</span>';
        }

        // SI TODO OK, SUBIMOS LA IMAGEN Y La MUESTRA AL SERVIDOR

        $image = $_FILES['cover']['tmp_name'];

        if (is_uploaded_file($image) == true) {
            if($_FILES['cover']['type'] == "image/png"){ 
                $cover_url = 'book/portada.png';
                $smallImage = imagescale(imagecreatefrompng($image), 200);

            } else { 
                $cover_url = 'book/portada.jpg';
                $smallImage = imagescale(imagecreatefromjpeg($image), 200);
            }
            
            if ($_FILES['cover']['type'] === "image/png") {
                imagepng($smallImage, $cover_url);
            } else {
                imagejpeg($smallImage, $cover_url);
            }
        }

        if (is_uploaded_file($_FILES['sample']['tmp_name']) === true) {
            $sample_url = 'book/muestra.pdf';
            if (!move_uploaded_file($_FILES['sample']['tmp_name'], $sample_url)){
                $errcover = '<span class="error">No se ha podido guardar el pdf en el directorio</span>';
            }
        }
    }


    /**
    * IMPRIMIMOS EL FORMULARIO O EL RESULTADO SEGUN LOS ERRORES
    */

    if (empty($title) || empty($author) || empty($publisher) || empty($isbn) || empty($sinopsis) || empty($cover) || !empty($errtitle) || !empty($errauthor) || !empty($errpublisher) || !empty($errisbn) || !empty($errsinopsis) || !empty($errcover)) {
    
        echo '<form method="post" action="libroCarlosSaez.php" enctype="multipart/form-data">';
        echo '<label for="title">Título:</label><br>';
        echo '<input type="text" id="title" name="title" value="'.$title.'">'.$errtitle.'<br>';

        echo '<label for="author">Autor:</label><br>';
        echo '<input type="text" id="author" name="author" value="'.$author.'">'.$errauthor.'<br>';

        echo '<label for="publisher">Editorial:</label><br>';
        echo '<input type="text" id="publisher" name="publisher" value="'.$publisher.'">'.$errpublisher.'<br>';

        echo '<label for="language">Idioma:</label><br>';
        echo '<select name="language" id="language">';
        echo '<option value="Castellano" '.($language == 'Castellano' ? 'selected' : '').'>Castellano</option>';
        echo '<option value="Valenciano" '.($language == 'Valenciano' ? 'selected' : '').'>Valencià</option>';
        echo '<option value="English" '.($language == 'English' ? 'selected' : '').'>English</option>';
        echo '</select><br>';

        echo '<label for="year">Año de edición:</label><br>';
        echo '<input type="month" id="year" name="year" min="1455-01" max="2024-12" value="'.$year.'" /><br>';

        echo '<label for="isbn">ISBN:</label><br>';
        echo '<input type="text" id="isbn" name="isbn" value="'.$isbn.'">'.$errisbn.'<br>';

        echo '<label for="sinopsis">Sinopsis:</label><br>';
        echo '<textarea id="sinopsis" name="sinopsis" rows="12" cols="29">'.$sinopsis.'</textarea>'.$errsinopsis.'<br>';

        echo '<label for="cover">Portada (JPG o PNG):</label><br>';
        echo '<input type="file" id="cover" name="cover"></input>'.$errcover.'<br>';

        echo '<label for="sample">Muestra (PDF):</label><br>';
        echo '<input type="file" id="sample" name="sample"></input>'.$errsample.'<br>';

        echo '<input type="submit" value="Añadir libro"></input>';
        echo '</form>';

    } else {
        echo '<div class="ficha">';
        // echo '<img src="'.$cover_url.'" alt="Portada del libro"><br><br>';
        echo '<img src="watermark.php?img=logo" alt="Portada del libro"><br><br>';
        echo '<strong>Titulo:</strong> '.$title.'<br>';
        echo '<strong>Autor:</strong> '.$author.'<br><br>';
        echo '<strong>Editorial:</strong> '.$publisher.'<br>';
        echo '<strong>Idioma:</strong> '.$language.'<br>';
        echo '<strong>Año de Edición:</strong> '.substr($year, 0, 4).'<br>';
        echo '<strong>ISBN:</strong> '.$isbn.'<br><br>';
        echo '<strong>Sinopsis:  </strong>'.$sinopsis.'<br><br>';
        echo '<a href="book/muestra.pdf" target="_blank">Descargar una muestra</a>';
        echo '<form class="botonsolo" method="post" action="libroCarlosSaez.php">';
        echo '<button type="submit" name="reiniciar">Reiniciar</button>';
        echo '</form>';
        echo '</div>';
    }

    function limpiar() {
        unlink('book/portada.jpg'); //borra la imagen del directorio
        unlink('book/portada.png');
        unlink('book/muestra.pdf');
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
    
    ?>
</body>
</html>