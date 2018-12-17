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
        text-align: center;
        font-size: 60px;
      }
      #menu
      {
        width: 500px;
        height: 480px;
        margin: auto;
        text-align: center;
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
    <?php
    session_start();
    $score = $_GET["score"];
    ?>
    <div id = "title">
      <h1>Thank you for playing!</h1>
      <form id="menu" method = "post" action = "SubmitScore2.php">
        Please enter your name to save your score: <input type = "text" name = "name"><br>
		Your score: <br><?php echo $score;?><br>
        <input type = "hidden" name = "score" value = '<?php echo $score;?>'>
        <input type = "submit" name = "submit" value = "Submit Score">
      </form>
    </div>
  </body>
</html>
