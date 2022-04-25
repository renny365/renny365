<?php
/*
 * Función personalizada para comprimir y
 * subir una imagen mediante PHP
 */
function compressImage($source, $destination, $quality) {
    // Obtenemos la información de la imagen
    $imgInfo = getimagesize($source);
    //$imgInfo = true;
    //$mime = true;
    if(is_array($imgInfo)){
        $mime = $imgInfo['mime'];
            // Creamos una imagen
            switch($mime){
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($source);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($source);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($source);
                    break;
                default:
                    $image = null;
                    //$image = imagecreatefromjpeg($source); 
                    if (!$image)
                    {
                    $image= imagecreatefromstring(file_get_contents($source));
                    }
            }
            // Guardamos la imagen
            //echo($destination);
            imagejpeg($image, $destination, 20);
            imagedestroy($image);
            // Devolvemos la imagen comprimida
            return $destination; 
    }
}
// Ruta subida
$uploadPath = "uploads/";
// Si el fichero se ha enviado
$status = $statusMsg = '';
if(isset($_POST["submit"])){
    $status = 'error';
    if(!empty($_FILES["image"]["name"])){
        // File info
        $fileName = basename($_FILES["image"]["name"]);
        # Le agregamos una id unica para evitar duplicado
        $fileName = uniqid(microtime()) . $fileName;
        # Con explode() obtenemos la extensión del archivo
        # sta linea genera error por el end en la version de php que no recibe los argumentos de la misma manera
        //$ext = end(explode('.', $fileName));
        # se modifica de esta manera y sigue en la linea $file_extension
        $ext = explode('.', $fileName);
        //$file_extension = end($tmp);
        # Encryptamos el nombre del archivo con md5() para evitar que el archivo tenga otra extensión y acortamos el nombre con substr()
        $fileName = substr(md5($fileName), 0, 10);
        # aca se modifica el end tambien $file_extension
        #se puede colocar la extension estatica o dinamica de como viene ya la imagen

        $file_extension = end($ext);
        //$file_extension_static = 'png';
        
        # Le devolvemos la extensión al archivo
        $fileName = $fileName . '.' . $file_extension;
        $imageUploadPath = $uploadPath . $fileName; 
        $fileType = pathinfo($imageUploadPath, PATHINFO_EXTENSION);
        echo($fileType);
        // Permitimos solo unas extensiones
        $allowTypes = array('jpg','png','jpeg','gif');
        
        if(in_array($fileType, $allowTypes)){
            // Image temp source
            $imageTemp = $_FILES["image"]["tmp_name"];
            // Comprimos el fichero
            $compressedImage = compressImage($imageTemp, $imageUploadPath, 20);
             
            if($compressedImage){
                $status = 'success';
                $statusMsg = "La imagen se ha subido satisfactoriamente.";
            }else{
                $statusMsg = "La compresion de la imagen ha fallado, archivo de formato no admitido";
            }
        }else{
            $statusMsg = 'Lo sentimos, solo se permiten imágenes con estas extensiones: JPG, JPEG, PNG, & GIF.';
        }
    }else{
        $statusMsg = 'Por favor, selecciona una imagen.';
    }
}
// Mostrar el estado de la imagen
echo $statusMsg;
echo('<br><a href="/index.php">Volver</a>');
?>