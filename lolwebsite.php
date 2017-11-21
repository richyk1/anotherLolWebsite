<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="assets/css/lolwebsite.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    <title>Document</title>
</head>

<body>
    <?php
      include('assets/php/extended.php'); // Including php in my html 
      ?>
        <div id="wrapper">
            <div id="summoner-div" class="child">
                <img src="http://ddragon.leagueoflegends.com/cdn/<?php echo getLatestVersion() ?>/img/profileicon/<?php echo $userData['profileIconId'] ?>.png" id="profileIcon">
                
                <div id="rank-container">
                    <?php 
                        $ranks = getRanks();
                        forEach($ranks as $rank) {
                        
                            echo "<div class='rank-div' id='".$rank['leagueName']."'>";
                            echo "League: ".$rank['leagueName'];
                            echo "<br>";
                            echo "Tier: ".$rank['tier'].$rank['rank'];
                            echo "<br>";
                            echo "Queue: ".$rank['queueType'];
                            echo "<br>";
                            echo "Wins: ".$rank['wins'];
                            echo "<br>";
                            echo "Losses: ".$rank['losses'];
                            echo "</div>";
                        }
                    
                    ?>
                </div>
            </div>
            <div id="split" class="child">
                <div id="top10-div" class="babies"></div>
                <div id="mostactive-div" class="babies"></div>
            </div>

            <div id="decypher-div" class="child"></div>
        </div>


</body>

</html>