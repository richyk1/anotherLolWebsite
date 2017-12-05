<?php

class STATIK {
    const API = "RGAPI-b4116126-fb4a-4d6a-bbba-360fdc3936b6";
    
    

    const MYSQL_CONNECTION = "localhost";
    const MYSQL_USERNAME = "root";
    const MYSQL_PASSWORD = "";
    const MYSQL_DATABASE = "barcode";

    const SUMMONER_NAME = "/lol/summoner/v3/summoners/by-name/"; // REQUIRES SUMMONER NAME
    const VERSIONS = "/lol/static-data/v3/versions/";
    const RANKS = "/lol/league/v3/positions/by-summoner/"; // REQUIRES SUMMONERID
    const MASTERY_POINTS = "/lol/champion-mastery/v3/champion-masteries/by-summoner/"; // REQUIRES SUMMONERID
    const ALL_CHAMPIONS = "/lol/static-data/v3/champions";
    const RECENT_GAMES = "/lol/match/v3/matchlists/by-account/{accountId}/recent";
    const MATCH = "/lol/match/v3/matches/{matchId}";
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

    public static function GET_Ranks($userData) {
        $conn = mysqli_connect(STATIK::MYSQL_CONNECTION, STATIK::MYSQL_USERNAME, STATIK::MYSQL_PASSWORD, STATIK::MYSQL_DATABASE);
        $sql = mysqli_query($conn, "SELECT rank FROM users WHERE summonerID = '".$userData['id']."'");

        return $sql;
        
    }

    public static function CACHE_RANKS($ranks, $userData) {
        $conn = mysqli_connect(STATIK::MYSQL_CONNECTION, STATIK::MYSQL_USERNAME, STATIK::MYSQL_PASSWORD, STATIK::MYSQL_DATABASE);
        $escaped = mysqli_real_escape_string($conn, serialize($ranks));
        mysqli_query($conn, "UPDATE users SET rank = '".$escaped."' WHERE summonerID = '".$userData['id']."'");

    }

    public static function GET_MATCHES($userData) {
        $conn = mysqli_connect(STATIK::MYSQL_CONNECTION, STATIK::MYSQL_USERNAME, STATIK::MYSQL_PASSWORD, STATIK::MYSQL_DATABASE);
        $sql = mysqli_query($conn, "SELECT recentGames FROM users WHERE summonerID = '".$userData['id']."'");

        return $sql;
        
    }

    public static function CACHE_MATCHES($matches, $userData) {
        $conn = mysqli_connect(STATIK::MYSQL_CONNECTION, STATIK::MYSQL_USERNAME, STATIK::MYSQL_PASSWORD, STATIK::MYSQL_DATABASE);
        $escaped = mysqli_real_escape_string($conn, serialize($matches));
        mysqli_query($conn, "UPDATE users SET recentGames = '".$escaped."' WHERE summonerID = '".$userData['id']."'");

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
if(isset($result['status'])) {
if($result['status']['status_code'] == 404) { // Error handling in case the champion name was not found.
    echo "<div id='error'>";
    echo $result['status']['message']." ";
    echo $result['status']['status_code'];
    echo "</div>";
    exit();
}

}

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
    sleep(2);
    $curl = curl_init();

    $userData = $GLOBALS["userData"];


    $options = array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $GLOBALS["prefix"].STATIK::RANKS.$userData['id'].$GLOBALS["parameters"],
    );

    curl_setopt_array($curl, $options);
    $result = json_decode(curl_exec($curl), true);
    if(isset($result['status'])) {
        if($result['status']['status_code'] == 429) $result = MYSQL_HANDLER::GET_RANKS($userData); 
    } else {
        MYSQL_HANDLER::CACHE_RANKS($result, $userData);
    }
    
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
    if(isset($result[0])) {
        $mostPlayedChamp = $result[0];
        $mostPlayedChamp['championId'] = getChampionName($mostPlayedChamp['championId']);
        return array(
            "championPoints" => $mostPlayedChamp['championPoints'],
            "championName" => $mostPlayedChamp['championId']
        );
    } else {
        return "null";
    }
    


    

    
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

function getRecentGames($accountId) {
    $curl = curl_init();
    $userData = $GLOBALS['userData'];

    $options = array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $GLOBALS['prefix'].str_replace("{accountId}", $accountId, STATIK::RECENT_GAMES).$GLOBALS['parameters']
    );

    curl_setopt_array($curl, $options);
    $result = json_decode(curl_exec($curl), true);
    curl_close($curl);

    $games = array();
    foreach($result['matches'] as $match) {
        array_push($games, $GLOBALS['prefix'].str_replace("{matchId}", $match['gameId'], STATIK::MATCH).$GLOBALS['parameters']);
    }

    $ch = array();
    $mh = curl_multi_init();

    foreach ($games as $key => $value) {
        $ch[$key] = curl_init();
        if ($options) {
            curl_setopt_array($ch[$key], $options);
        }
        curl_setopt($ch[$key], CURLOPT_URL, $value);
        curl_setopt($ch[$key], CURLOPT_RETURNTRANSFER, true);
        curl_multi_add_handle($mh, $ch[$key]);
    }

    $game_result_array = array();
    $running = null;
    do {
        curl_multi_exec($mh, $running); //add handle
 
       
    } while ($running > 0);
    // Get content and remove handles.
    foreach ($ch as $key => $val) {
        $result_two = json_decode(curl_multi_getcontent($val), true);
        if(isset($result_two['status']['status_code'])) if($result_two['status']['status_code'] == 429) {
            // $result_two = MYSQL_HANDLER::GET_MATCHES($userData);
            continue;
        } 
        if(!isset($result_two['status'])) {
            MYSQL_HANDLER::CACHE_MATCHES($result_two, $userData);
        }

        $participantId;
        foreach($result_two['participantIdentities'] as $count => $identity) {
            if($identity['player']['accountId'] == $accountId) $participantId = $identity['participantId'];        
        }
        foreach($result_two['participants'] as $count => $participant) {
            if($participant['participantId'] == $participantId) array_push($game_result_array, $participant['stats']['win']);
        }

        curl_multi_remove_handle($mh, $val);
    }
    curl_multi_close($mh);
    return $game_result_array;
}


?>