<?php

/**
 * this class is used to upload file
 */
class upload
{
  function __construct()
  {
  }
  private static $allowTypes = array('jpg', 'png', 'jpeg', 'gif', "pdf", "docx", 'txt');
  public static function uploadImages($files, $inputName = "file", $targetDir = "../assets/img/")
  {
    $insertValuesSQL = '';
    foreach ($files[$inputName]['name'] as $key => $val) {
      if ($files[$inputName]['size'] == 0 && $files[$inputName]['error'] == 0) {
        $insertValuesSQL .= "user.png,";
      } else {
        $fileName = date('Ymdhisu') . $key . '.' . pathinfo($files[$inputName]['name'][$key], PATHINFO_EXTENSION);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        if (in_array(strtolower($fileType), self::$allowTypes)) {
          // Upload file to server
          if (move_uploaded_file($files[$inputName]["tmp_name"][$key], $targetFilePath)) {
            // Image db insert sql
            $insertValuesSQL .= $fileName . ',';
          } else {
            $insertValuesSQL .= "user.png,";
          }
        } else {
          $insertValuesSQL .= "user.png,";
        }
      }
    }
    return explode(",", trim($insertValuesSQL, ','));
  }
  public static function Images($files, $inputName = "file", $targetDir = "../assets/img/")
  {
    $statusMsg = $errorMsg = $insertValuesSQL = $errorUpload = $errorUploadType = '';
    if (!empty(array_filter($files[$inputName]['name']))) {
      foreach ($files[$inputName]['name'] as $key => $val) {
        // File upload path
        //$fileName = date('Ymdhisu').basename($files[$name]['name'][$key]);
        $fileName = date('Ymdhisu') . $key . '.' . pathinfo($files[$inputName]['name'][$key], PATHINFO_EXTENSION);
        $targetFilePath = $targetDir . $fileName;
        // Check whether file type is valid
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        if (in_array(strtolower($fileType), self::$allowTypes)) {
          // Upload file to server
          if (move_uploaded_file($files[$inputName]["tmp_name"][$key], $targetFilePath)) {
            // Image db insert sql
            $insertValuesSQL .= $fileName . ',';
          } else {

            $errorUpload .= $files[$inputName]['name'][$key] . ', ';
          }
        } else {
          $errorUploadType .= $files[$inputName]['name'][$key] . ', ';
        }
      }
      if (!empty($insertValuesSQL)) {
        $insertValuesSQL = trim($insertValuesSQL, ',');
        $errorUpload = !empty($errorUpload) ? 'Upload Error: ' . $errorUpload : '';
        $errorUploadType = !empty($errorUploadType) ? 'File Type Error: ' . $errorUploadType : '';
        $errorMsg = !empty($errorUpload) ? '<br/>' . $errorUpload . '<br/>' . $errorUploadType : '<br/>' . $errorUploadType;
        $statusMsg = "200";
        //Files are uploaded successfully.
      } else {
        $errorUpload = !empty($errorUpload) ? 'Upload Error: ' . $errorUpload : '';
        $errorUploadType = !empty($errorUploadType) ? 'File Type Error: ' . $errorUploadType : '';
        $errorMsg = !empty($errorUpload) ? '<br/>' . $errorUpload . '<br/>' . $errorUploadType : '<br/>' . $errorUploadType;
        $statusMsg = '404';

        //'Please select a file to upload.
      }
    } else {
      $statusMsg = '404';
    }
    return json_encode(['statusMsg' => $statusMsg, 'errorMsg' => $errorMsg, 'sqlValue' => $insertValuesSQL]);
  }
  public static function Image($files, $inputName = "file", $targetDir = "../assets/img/")
  {
    $statusMsg = $errorMsg = $insertValuesSQL = $errorUpload = $errorUploadType = '';
    if (!empty($files[$inputName]['name'])) {
      $fileName = $files[$inputName]['name'] . '-' . date('Ymdhisu') . '.' . pathinfo($files[$inputName]['name'], PATHINFO_EXTENSION);
      $targetFilePath = $targetDir . $fileName;
      // Check whether file type is valid
      $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
      if (in_array(strtolower($fileType), self::$allowTypes)) {
        // Upload file to server
        if (move_uploaded_file($files[$inputName]["tmp_name"], $targetFilePath)) {
          // Image db insert sql
          $insertValuesSQL = $fileName;
        } else {
          $errorUpload .= $files[$inputName]['name'] . ', ';
        }
      } else {
        $errorUploadType .= $files[$inputName]['name'] . ', ';
      }
      if (!empty($insertValuesSQL)) {
        $insertValuesSQL = trim($insertValuesSQL, ',');
        $errorUpload = !empty($errorUpload) ? 'Upload Error: ' . $errorUpload : '';
        $errorUploadType = !empty($errorUploadType) ? 'File Type Error: ' . $errorUploadType : '';
        $errorMsg = !empty($errorUpload) ? '<br/>' . $errorUpload . '<br/>' . $errorUploadType : '<br/>' . $errorUploadType;
        $statusMsg = "200";
        //Files are uploaded successfully.
      } else {
        $errorUpload = !empty($errorUpload) ? 'Upload Error: ' . $errorUpload : '';
        $errorUploadType = !empty($errorUploadType) ? 'File Type Error: ' . $errorUploadType : '';
        $errorMsg = !empty($errorUpload) ? '<br/>' . $errorUpload . '<br/>' . $errorUploadType : '<br/>' . $errorUploadType;
        $statusMsg = '404';
      }
    } else {
      $statusMsg = '404';
    }
    return json_encode(['statusMsg' => $statusMsg, 'errorMsg' => $errorMsg, 'sqlValue' => $insertValuesSQL]);
  }

  public static function removeUploadedImage($fileName = "file", $targetDir = "../assets/img/")
  {
    if (file_exists($targetDir . $fileName)) {
      unlink($targetDir . $fileName);
      return true;
    } else {
      return false;
    }
  }
}
