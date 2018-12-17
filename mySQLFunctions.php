<?php
function connectDB(){
  $servername = "localhost";
  $username = "jpelletierwebtech";
  $password = "";
  $dbname = "my_jpelletierwebtech";
  $conn = mysqli_connect($servername, $username, $password, $dbname);
  if(!$conn)
  {
    die("Connection to DB failed:" .mysqli_connect_error(). "<br>");
  }
  return $conn;
}

function exeSQL($conn, $sql)
{
  if($result = mysqli_query($conn, $sql))
  {
    //echo "SQL is done successfully.<br>";
  }
  else
  {
    echo "Error in running sql: " .$sql. " with error: " .mysqli_error($conn). ".<br>";
  }
  return $result;
}
function showResults($result)
{
  if(mysqli_num_rows($result) > 0)
  {
    echo "<table border = 1>";
    echo "<tr>";
    while ($fieldInfo = mysqli_fetch_field($result))
    {
      if ($fieldInfo-> name != "passwd")
      {
        echo "<th>$fieldInfo->name</th>"; //this symbol -> means for each object get this attribute
      }
    }
    echo "</tr>";
    while ($row = mysqli_fetch_assoc($result))
    {
      echo "<tr>";
      foreach($row as $key => $value)
      {
        if ($key == "passwd")
        {
          continue;
        }
        if ($key == "photo")
        {
          echo "<td><img src = '". $value ."' width = 100px height = 100px></td>";
        }
        else
        {
          echo "<td>$value</td>";
        }
      }
      echo "</tr>";
    }
    echo "</table>";
  }
  else 
  {
    echo "No results found.";
  }
}
function uploadFile($fName, $format, $size)
{
  $dir = "Upload/";
  $file = $dir.basename($_FILES[$fName]["name"]);
  $fileType = pathinfo($file, PATHINFO_EXTENSION);
  $fileSize = $_FILES[$fName]["size"];
  if ($fileSize > $size)
  {
    echo "File is too large<br>";
    return false;
  }
  if (stristr($format, $fileType) == false)
  {
    echo "File format is not correct<br>";
    return false;
  }
  /*if (file_exists($file))
  {
    echo "File already exists!<br>";
    return false;
  }*/
  if (!move_uploaded_file($_FILES[$fName]["tmp_name"], $file))
  {
    return false;
  }
  return $file;
}
?>
