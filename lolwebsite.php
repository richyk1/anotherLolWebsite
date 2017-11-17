<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    You typed in <?php echo $_GET["summonerName"]; ?>
    <br>
    Region is <?php echo $_GET["regionList"]; ?>

    <?php
      $curl = curl_init();
      $API = "RGAPI-11c9915b-0d34-405a-867b-2c5142056a86";
      $link = "https://euw1.api.riotgames.com/lol/summoner/v3/summoners/by-name/RichyFTW";


      curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $link,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => array(
          'api_key' => "RGAPI-11c9915b-0d34-405a-867b-2c5142056a86"
        )
      ));

      $result = curl_exec($curl);
      echo $result;

      curl_close($curl);

    ?>

</body>
</html>
