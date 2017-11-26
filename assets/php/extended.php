<?php

class STATIK {
    const API = "RGAPI-ac3b7c1d-51a1-4f07-9989-427d43080ca8";

    const MYSQL_CONNECTION = "localhost";
    const MYSQL_USERNAME = "root";
    const MYSQL_PASSWORD = "";
    const MYSQL_DATABASE = "barcode";

    const SUMMONER_NAME = "/lol/summoner/v3/summoners/by-name/"; // REQUIRES SUMMONER NAME
    const VERSIONS = "/lol/static-data/v3/versions/";
    const RANKS = "/lol/league/v3/positions/by-summoner/"; // REQUIRES SUMMONERID
    const MASTERY_POINTS = "/lol/champion-mastery/v3/champion-masteries/by-summoner/"; // REQUIRES SUMMONERID
    const ALL_CHAMPIONS = "/lol/static-data/v3/champions";
}

class MYSQL_HANDLER {
    public static function getChampionNameSQL($championId) {
        $conn = mysqli_connect(STATIK::MYSQL_CONNECTION ,STATIK::MYSQL_USERNAME, STATIK::MYSQL_PASSWORD, STATIK::MYSQL_DATABASE);
        $sql = mysqli_query($conn, "SELECT * FROM champion_list WHERE id = '".$championId."'");
        $result = mysqli_fetch_array($sql);

    
        mysqli_close($conn); // Always close the connections to mysql;

        return $result['photo_name'];
        
    }
    public static function setChampionListSQL($championList) {
        $conn = mysqli_connect(STATIK::MYSQL_CONNECTION, STATIK::MYSQL_USERNAME, STATIK::MYSQL_PASSWORD, STATIK::MYSQL_DATABASE);
        # code...
        $count = count($championList);
        $query = 'INSERT INTO champion_list (id, title, photo_name, name) VALUES ';
        for ($i=$count; $i > 0; $i--){
            
            if(isset($championList[$i])){
                $check = "SELECT * FROM champion_list WHERE id = '".$championList[$i]['id']."'";
                $result = mysqli_query($conn, $check);
                if(mysqli_num_rows($result) == 0) {
                    $newTitle = str_replace ("'","''", $championList[$i]['title']);
                    $newName = str_replace ("'","''", $championList[$i]['name']);
                    $newKey = str_replace ("'","''", $championList[$i]['key']);
                    $query .= '('.$championList[$i]['id'].',"'.$newTitle.'","'.$newKey.'","'.$newName.'")';
                    if ($i != 1) {
                        $query .= ','; 
                    }
                } else {
                    continue;
                }
                
            } else {
                continue;
            }
        }
        $query = str_replace('"', "'", $query);


        mysqli_query($conn, $query);
        mysqli_close($conn);
        }

        
    }
    


$userData = array(
    "summonerName" => $_GET["summonerName"],
    "region" => $_GET["region"]
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
$prefix = "https://".$userData['region'].'.api.riotgames.com';
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
        $connection = mysqli_connect(STATIK::MYSQL_CONNECTION ,STATIK::MYSQL_USERNAME, STATIK::MYSQL_PASSWORD, STATIK::MYSQL_DATABASE);
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
    $conn = mysqli_connect(STATIK::MYSQL_CONNECTION ,STATIK::MYSQL_USERNAME, STATIK::MYSQL_PASSWORD, STATIK::MYSQL_DATABASE);
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

function getChampionMastery() {
    
    $userData = $GLOBALS['userData'];
    $mysql_handler = new MYSQL_HANDLER();

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $GLOBALS['prefix'].STATIK::MASTERY_POINTS.$userData['id'].$GLOBALS["parameters"]
    ));

    $result = json_decode(curl_exec($curl), true);
    curl_close($curl);

    $mostPlayedChamp = $result[0];


    $mostPlayedChamp['championId'] = getChampionName($mostPlayedChamp['championId']);

    return array(
        "championPoints" => $mostPlayedChamp['championPoints'],
        "championName" => $mostPlayedChamp['championId']
    );
}

 function getChampionName($championId) {
    $parameters = http_build_query([
        "?".'api_key' => STATIK::API,
        "dataById" => "true"
    ]);
    $parameters = urldecode($parameters);

     $curl = curl_init();
     curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $GLOBALS['prefix'].STATIK::ALL_CHAMPIONS.$parameters
     ));


     $result = json_decode(curl_exec($curl), true);
     curl_close($curl);

     if(array_key_exists("data", $result)) {
        
        MYSQL_HANDLER::setChampionListSQL($result['data']); //Inserts a list of champions in the SQL SYSTEM / updates.
        return $result['data'][$championId]['name'];
        //return("is this working ");
     } else {
        return MYSQL_HANDLER::getChampionNameSQL($championId);
     };

 }



?>