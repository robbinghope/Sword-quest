<!DOCTYPE html>
<html>
  <head>
    <title>Submit Score</title> 
    <style>
      body
      {
        background-color: #512815; /*Brown background*/
      }
      #title
      {
        color: #72655f;
        /*text-align: center;*/
        font-size: 25px;
      }
      #menu
      {
        width: 500px;
        height: 480px;
        /*margin: auto;
        text-align: center;*/
        background-color: #72655f;
        color: #512815;
      }

      #menu table
      {
        width: 500px;
        font-size: 80px;
      }
    </style>
  </head>
  <body>

    <div id = "title">
      <h1>Thank you for submitting your score!</h1>
      <?php
      session_start();
      include "mySQLFunctions.php";
      $conn = connectDB();
      $name = $_POST["name"];
      $score = $_POST["score"];
      echo "Name: " . $name . "<br>";
      echo "Score: " . $score . "<br>";
      echo "<hr>";
      $sql = "INSERT INTO Leaderboard (Name, Score) VALUES ('$name', $score)";
      $result = exeSQL($conn, $sql);
      $sql = "SELECT Name, Score FROM Leaderboard ORDER BY score DESC, id DESC;";
      if ($result = exeSQL($conn, $sql))
      {
      	echo "<h1>Leaderboard</h1>";
        showResults($result);
        mysqli_free_result($result);
      }
		
      ?>
    </div>
    <p><a href = "http://jpelletierwebtech.altervista.org/Game/Menu.html" style="color:black">Return to Menu</a></p>
  </body>
</html>
