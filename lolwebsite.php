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
                <img src="http://ddragon.leagueoflegends.com/cdn/<?php echo getLatestVersion() ?>/img/profileicon/<?php echo $userData['profileIconId']; ?>.png" id="profileIcon">
                
                <div id="summoner-div-child">
                <div id="most-played">
                    <div id="img-wrap">
                        <img src="http://ddragon.leagueoflegends.com/cdn/<?php echo getLatestVersion() ?>/img/champion/<?php echo getChampionMastery()['championName'] ?>.png" id="img-champion">
                        <p id="img-desc"><?php  print_r(getChampionMastery()['championPoints'])?></p>
                    </div>
                </div>        
                    <?php 
                        $ranks = getRanks();
                        forEach($ranks as $rank) {
                            echo "<div class='rank-div' id='".$rank['queueType']."'>";
                            echo "<p> League: ".$rank['leagueName']. "</p>";
                            
                            echo "<p> Tier: ".$rank['tier'].$rank['rank']."</p>";
                            
                            echo "<p> Queue: ".$rank['queueType']."</p>";
                            
                            echo "<p> Wins: ".$rank['wins']."</p>";
                            
                            echo "<p> Losses: ".$rank['losses']."</p>";
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
<link rel="stylesheet" href="assets/css/extended.css">
<script>
   
    $('div[class^=rank-div]').click(function() {
       // Get the next div[class^=rank-div]
        var $next = $(this).next('div[class^=rank-div]').css({display: "flex"});
        // If there wasn't a next one, go back to the first.
        if( $next.length == 0 ) {
            $next = $(this).prevAll('div[class^=rank-div]').first();
        }
        
        $(this).hide('slow');
        $next.show('slow');
    });

</script>
</html>