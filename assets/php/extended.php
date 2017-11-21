<?php
class STATIK {
    const API = "RGAPI-6845271e-bfe5-4d8d-a34d-3073f6e99d22";

    const SUMMONER_NAME = "/lol/summoner/v3/summoners/by-name/";
    const VERSIONS = "/lol/static-data/v3/versions/";
    const RANKS = "/lol/league/v3/positions/by-summoner/";
}
    

$userData = array(
    "summonerName" => $_GET["summonerName"]
    /*
    First request to get these variables
    "id" 
    "accountId" 
    "name" 
    "profileIconId" 
    "revisionDate" 
    "summonerLevel"
    
    **/

);

$curl = curl_init();


$parameters = http_build_query([
        "?".'api_key' => STATIK::API
    
        ]);
$parameters = urldecode($parameters);
$prefix = "https://euw1.api.riotgames.com";
$options = array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $prefix.STATIK::SUMMONER_NAME.$userData["summonerName"].$parameters,
);

curl_setopt_array($curl, $options); 
$result = json_decode(curl_exec($curl), true); // Converting to array. Second arguemnt true makes it so instead convertying to object it converts to array.
forEach($result as $key => $value) {
    $userData[$key] = $value;
}
curl_close($curl); 

function getLatestVersion() { // Gets the latest game
     $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $GLOBALS['prefix'].STATIK::VERSIONS.$GLOBALS["parameters"]
    ));
    
    $result = json_decode(curl_exec($curl), true);
    //mysqlConnection($result);
    
    curl_close($curl);
    if(array_key_exists("status", $result)) {
        $connection = mysqli_connect("localhost", "root", "", "barcode");
        $sql = "SELECT MAX(versions) FROM static_data";
        $result = mysqli_fetch_array(mysqli_query($connection, $sql));

        mysqli_close($connection);
        return $result[0];
    } else {
        mysqlConnection($result);
        return $result[0];
    }
    
}

function getRanks() {
    $curl = curl_init();

    $userData = $GLOBALS["userData"];


    $options = array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $GLOBALS["prefix"].STATIK::RANKS.$userData['id'].$GLOBALS["parameters"],
    );

    curl_setopt_array($curl, $options);
    $result = json_decode(curl_exec($curl), true);

    
    forEach($result as $key => $value) {
        unset($result[$key]['leagueId']);
        unset($result[$key]['veteran']);
        unset($result[$key]['freshBlood']);
        unset($result[$key]['hotStreak']);       
    }


    curl_close($curl);
    return $result;
}

function mysqlConnection($data) {
    $servername = "localhost";
    $username = "root";

    $conn = mysqli_connect($servername, $username, "", "barcode");
    if(!$conn) {
        echo "could not connect to server" .mysql_error();
    }
    
    for($n = 0; $n <= 5; $n++) {
        $check = mysqli_query($conn, "SELECT * FROM static_data WHERE versions = '".$data[$n]."'");
        if(mysqli_num_rows($check) > 0) {
            return;
        } else {
            $sql = "INSERT IGNORE INTO static_data (versions) VALUES ('".$data[$n]."')";
            $retval = mysqli_query($conn, $sql);
            if(!$retval) {
                echo "Error: ".mysqli_error();
            } 
        }
        
    }
   

    mysqli_close($conn);
    
}



?>
