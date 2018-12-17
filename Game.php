<!DOCTYPE html>
<html>
  <head>
    <title>Sword Quest</title>
    <style>
      body
      {
        background-color: #512815; /*Brown background*/

      }
      canvas 
      {
        border: 1px solid black;
        background-color: lightblue;
        margin: auto;
      }
    </style>
  </head>
  <body onload="startGame()">
    <script>
      var myGamePiece;
      var myBackground;
      var myPlatforms = [];
      var myMonsters = [];
      var mySplats = [];
      var myScore;
      var myFloor;
      var myLives;
      var score = 0;
      var lives = 5;
      var facing;
      var mySplat;
      function startGame() 
      {
        myGamePiece = new component(28, 32, "Images/runThreeR.png", 10, 10, "image");
        myBackground = new component(960,540, "Images/mountain.png", 0,0,"background");
        myScore = new component("30px", "Consolas", "green", 280, 40, "text");
        myLives = new component("30px", "Consolas", "red", 480, 40, "text");
        myFloor = new component(960, 10, "#d86b11", 0, 480, "platform");
        myGameArea.start();
        //myGamePiece = new component(30, 30, "red", 10, 120);
      }

      function component(width, height, color, x, y, type)
      {
        this.type = type;
        if (type == "image" || type == "background" || type == "monster")
        {
          this.image = new Image();
          this.image.src = color;
        }
        this.moveDir = "left";
        this.width = width;
        this.height = height;
        this.color = color;
        this.speedX = 0;
        this.speedY = 0;
        this.x = x;
        this.y = y; 
        this.gravity = 0.25;
        this.gravitySpeed = 0;
        this.update = function ()
        {
          ctx = myGameArea.context;
          if (type == "image" || type == "background" || type == "monster")
          {
            ctx.drawImage(this.image, this.x, this.y, this.width, this.height);
            if (type == "backgound")
            {
              ctx.drawImage(this.image, this.x, this.y, this.width, this.height);
            }
          }
          else if (this.type == "text")
          {
            ctx.font = this.width + " " + this.height;
            ctx.fillStyle = color;
            ctx. fillText(this.text, this.x, this.y);
          }
          else
          {
            ctx.fillStyle = color;
            ctx.fillRect(this.x, this.y, this.width, this.height);
          }
        }
        this.newPos = function() 
        {
          this.gravitySpeed += this.gravity;
          this.x += this.speedX;
          this.y += this.speedY + this.gravitySpeed;
          this.hitBottom();
          if (this.type == "background")
          {
            if (this.x == -(this.width))
            {
              this.x = 0;
            }
          }
        } 
        this.hitBottom = function()
        {
          var rockBottom = myGameArea.canvas.height-this.height;
          if (this.y >rockBottom)
          {
            this.gravitySpeed = 0;
            this.y = rockBottom;
          }
        }
        this.crashWith = function(otherobj) 
        {
          var myleft = this.x;
          var myright = this.x + (this.width);
          var mytop = this.y;
          var mybottom = this.y + (this.height);
          var otherleft = otherobj.x;
          var otherright = otherobj.x + (otherobj.width);
          var othertop = otherobj.y;
          var otherbottom = otherobj.y + (otherobj.height);
          var crash = true;
          var placement = "";           
          if ((mybottom < othertop) || (mytop  > otherbottom) || (myright-10 < otherleft) || (myleft +10 > otherright)) 
          {
            crash = false;
          }

          /*if (otherobj.type == "platform")
          {
            if ((mybottom < othertop) || (mytop -10 > otherbottom) || (myright-10 < otherleft) || (myleft +10> otherright)) 
            {
              crash = false;
            }
          }*/
          return crash;
        }
      }

      var myGameArea = 
          {
            canvas : document.createElement("canvas"),
            start : function() 
            {
              this.canvas.width = 960;
              this.canvas.height = 540;
              this.context = this.canvas.getContext("2d");
              document.body.insertBefore(this.canvas, document.body.childNodes[0]);

              this.frameNo = 0;
              this.interval = setInterval(updateGameArea, 20);
              window.addEventListener('keydown', function (e) 
                                      {
                myGameArea.keys = (myGameArea.keys || []);
                myGameArea.keys[e.keyCode] = true;
              })
              window.addEventListener('keyup', function (e) 
                                      {
                myGameArea.keys[e.keyCode] = false; 
              })
            },
            clear : function()
            {
              this.context.clearRect(0,0,this.canvas.width, this.canvas.height);
            },
            stop : function()
            {
              clearInterval(this.interval);
              this.clear();
            }

          }

      function updateGameArea()
      {

        //*Tests if Game Piece Crashes with obstacles, stops if it does
        crashTestDummy();

        myGameArea.clear();
        //myBackground.speedX = -1;
        myBackground.newPos();
        myBackground.update();
        myFloor.update();
        myGameArea.frameNo += 1;
        //*Generating random obstacles
        addObstacle();
        //*Moving obstacles
        moveObstacle();
        removeSplats();
        //Writing Score on canvas
        myScore.text = "Score: " + Math.round(score);
        myScore.update();
        myLives.text = "Lives: " + Math.round(lives);
        myLives.update();
        //Reseting game piece speed
        myGamePiece.speedX = 0;
        myGamePiece.speedY = 0; 
        movePiece();
        myGamePiece.newPos();
        myGamePiece.update();

        updateSplats();
      }

      function updateSplats()
      {
        for (i = 0; i < mySplats.length; i += 1) 
        {
          mySplats[i].update();
        }
      }
      function addObstacle()
      {
        if (myGameArea.frameNo == 1 || everyInterval(90)) 
        {
          var x, y;
          x = myGameArea.canvas.width;
          y = myGameArea.canvas.height;
          minWidth = 50;
          maxWidth = 200;
          height = 10;
          maxGap = 100;
          minGap = 50;
          minPos = 300;
          maxPos = myGameArea.canvas.height - 100;
          gap = Math.floor(Math.random()*(maxGap-minGap+1)+minGap);
          length = Math.floor(Math.random()*(maxWidth-minWidth+1)+minWidth);
          pos = Math.floor(Math.random()*(maxPos-minPos+1)+minPos);
          myPlatforms.push(new component(length, height, "#6fa5fc", x, pos, "platform"));
          myMonsters.push(new component(21, 24, "Images/monsterLeft.png", x+(length/2), pos-24, "monster"));
        }
      }
      function getScore()
      {
        return score;
      }
      function addSplat(x, y)
      {
        mySplats.push(new component(21, 24, "Images/splat.gif", x, y, "image"));
      }
      function addSplatScore(x, y)
      {
        mySplats.push(new component(21, 24, "Images/splat.gif", x, y, "image"));
        score++;
      }
      function crashTestDummy()
      {
        for (i = 0; i < myPlatforms.length; i += 1) 
        {
          if (myGamePiece.crashWith(myMonsters[i]))
          {
            if (myMonsters[i].type != "dead")
            {
              if (myMonsters[i].y >= myGamePiece.y+myGamePiece.height-15)
              {
                addSplatScore(myMonsters[i].x, myMonsters[i].y);
                myMonsters[i].image.src = "Images/blank.png";
                myMonsters[i].type = "dead";
              }
              else
              {
                if (lives > 0)
                {
                  lives+=-1;
                  myGamePiece.x = 10;
                  myGamePiece.y = 100;
                  myGamePiece.gravitySpeed = 5;
                }
                else
                {
                  myGameArea.stop();
                  sendScore();
                }
              }
            }
          }
          if (myGamePiece.crashWith(myPlatforms[i])) 
          {
            if (myPlatforms[i].y >= myGamePiece.y+myGamePiece.height-10)
            {
              if (myGamePiece.x > 0)
              {
                myGamePiece.x = myPlatforms[i].x + (myGamePiece.x - myPlatforms[i].x) - 2;
              }
              myGamePiece.y = myPlatforms[i].y - myGamePiece.height+1;
              myGamePiece.speedY = 0;
              myGamePiece.speedX = 0;
              myGamePiece.gravitySpeed = 0;
              return;
            }
            else /*(myPlatforms[i].y + 10 <= myGamePiece.y)*/
            {
              myGamePiece.y = myPlatforms[i].y+10;
              myGamePiece.speedY = 0;
              myGamePiece.speedX = 0;
              return;
            }
          } 
        }
        if (myGamePiece.crashWith(myFloor)) 
        {
          if (score > 0)
          {
            score+=-0.02;
          }
          myGamePiece.y = myFloor.y - myGamePiece.height+1;
          myGamePiece.speedY = 0;
          myGamePiece.speedX = 0;
          myGamePiece.gravitySpeed = 0;
          return;
        }
      }
      function moveObstacle()
      {
        for (i = 0; i < myPlatforms.length; i += 1) 
        {
          if (myMonsters[i].x < 0 && myMonsters[i].type != "dead")
          {
            //addSplat(-5, myMonsters[i].y);
            myMonsters[i].image.src = "Images/blank.png";
            myMonsters[i].type = "dead";
          }
          if (myPlatforms[i].x < 0 - myPlatforms[i].width)
          {
            myPlatforms.splice(i,1);
            myMonsters.splice(i,1);
          }
          else
          {
            myPlatforms[i].x += -2;
            myPlatforms[i].update();
            if (myMonsters[i].moveDir == "right")
            {
              if (myMonsters[i].x >= myPlatforms[i].x + myPlatforms[i].width - myMonsters[i].width)
              {
                myMonsters[i].moveDir = "left";
                if (myMonsters[i].type != "dead"){myMonsters[i].image.src = "Images/monsterLeft.png"};
              }
              else
              {
                myMonsters[i].x += 1;
              }
            }
            else
            {
              if (myMonsters[i].x <= myPlatforms[i].x+2)
              {
                myMonsters[i].moveDir = "right";
                if (myMonsters[i].type != "dead"){myMonsters[i].image.src = "Images/monsterRight.png"};
              }
              else 
              {
                myMonsters[i].x += -3.5;
              }
            }
            myMonsters[i].update();
          }
        }
      }

      function movePiece()
      {
        if (facing == "L")
        {         
          myGamePiece.image.src = "Images/runTwoL.png";
        }
        else
        {         
          myGamePiece.image.src = "Images/runTwoR.png";          
        }
        if (myGameArea.keys && myGameArea.keys[37] && myGamePiece.x > -5) {myGamePiece.speedX = -5;  myGamePiece.image.src = "Images/runThreeL.png"; facing = "L";}
        if (myGameArea.keys && myGameArea.keys[39] && myGamePiece.x < 940) {myGamePiece.speedX = 5;  myGamePiece.image.src = "Images/runThreeR.png"; facing = "R";}
        if (myGameArea.keys && myGameArea.keys[38]) {myGamePiece.speedY = -8; }
        //if (myGameArea.keys && myGameArea.keys[40]) {myGamePiece.speedY = 5; }
      }

      function removeSplats()
      {
        if (mySplats.length > 60)
        {
          mySplats.shift();
        }
      }
      function everyInterval(n)
      {
        if ((myGameArea.frameNo / n) % 1 == 0)
        {
          return true;
        }
        return false;
      }

      function moveup() 
      {
        myGamePiece.speedY -= 1; 
      }

      function movedown() 
      {
        myGamePiece.speedY += 1; 
      }

      function moveleft() 
      {
        myGamePiece.speedX -= 1;
      }

      function moveright() 
      {
        myGamePiece.speedX += 1;
      }

      function stopMove() 
      {
        myGamePiece.speedX = 0; 
        myGamePiece.speedY = 0; 
      }
      function sendScore()
      {
        window.location.href = "SubmitScore.php?score=" + Math.round(score);
      }
    </script>
    <br>
    <p>Welcome to my Game!</p>
    <p>Defeat as many monsters as you can while avoinding the orange floor! How high can you get?</p>
    <p>Left Arrow = Move Left, Right Arrow = Move Right, Up Arrow =  Jump!</p>
    <p><a href = "http://jpelletierwebtech.altervista.org/Game/Menu.html" style="color:black">Menu</a>
      <a href = "http://jpelletierwebtech.altervista.org/Game/Game.php" style="color:black">New Game</a>
      <a href = "http://jpelletierwebtech.altervista.org/Game/Leaderboard.php" style="color:black">Leaderboard</a></p>
    <input id = "send" type = "button" value = "Submit Score" onclick="sendScore();">
  </body>
</html>
