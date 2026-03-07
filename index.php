<?php

// header('Content-Type: image/png;');

$dsn = 'mysql:dbname=mendela;host=127.0.0.1:3307';
$user = 'root';
$password = '';



$dbh = new PDO($dsn, $user, $password);

    $sth = $dbh->prepare("SELECT * FROM temperature WHERE user_id = :user_id ORDER BY id");

// $id='id'; // w domysle wartosc od klienta (GET/POST)
// $sth = $dbh->prepare("select * from temperature");
$sth->bindValue('user_id', 1, PDO::PARAM_INT);
$sth->execute();
$data = $sth->fetchAll(PDO::FETCH_ASSOC);


//GET
$width = isset($_GET["width"]) && $_GET["width"] > 0 ? (int)$_GET["width"] : 1000;
$height = isset($_GET["height"]) && $_GET["height"] > 0 ? (int)$_GET["height"] : 400;
$margin = isset($_GET["margin"]) && $_GET["margin"] > 0 ? (int)$_GET["margin"] : 30;
$dayCount = isset($_GET["dayCount"]) && $_GET["dayCount"] > 0 ? (int)$_GET["dayCount"] : 20;


//data from get after prepared
$marginLeft = $margin+40;
$marginTop = $margin;
$marginRight = $margin;
$marginBottom = $margin+40;

$im = imagecreatetruecolor($width, $height);
$white = imagecolorallocate($im, 255, 255, 255);
$black = imagecolorallocate($im, 0, 0, 0);
$gray = imagecolorallocate($im, 125, 125, 125);
$red = imagecolorallocate($im, 255, 0, 0);
$blue = imagecolorallocate($im, 0, 0, 255);
$tableOfColors = [
    'white' => $white,
    'black' => $black,
    'gray'  => $gray,
    'red'   => $red,
    'blue' => $blue
];
$intermittent= [$white, $white, $white, $gray, $gray, $gray];
$points = [];

//data do send to the world by endpoint :)
enum Status: string {
    case Normal = "normal";
    case Ill = "ill";
    case Nothing = "nothing";

} 
class TemperaturePoint
{
    public string $id;
    public int $x;
    public int $y;
    public string $temperature;
    public Status $healthStatus;

    public function __construct(
        string $id,
        int $x,
        int $y,
        string $temperature,
        Status $healthStatus
    ) {
        $this->id = $id;
        $this->x = $x;
        $this->y = $y;
        $this->temperature = $temperature;
        $this->healthStatus = $healthStatus;
    }
}
// global$temperaturePoints = [];





imagefilledrectangle($im, 0, 0, $width, $width, $white);

imagestringup($im, 7, 10, $height/1.5, "temperatura", $black);
imagestring($im, 7, $width/2, $height-30,mb_convert_encoding("dzień pomiaru", 'ISO-8859-2', 'UTF-8'), $black);

//chart
imagesetstyle($im, $intermittent);
$chartWidth = $width-$marginLeft-$marginRight;
$chartHeight = $height-$marginTop-$marginBottom;
// tyle tyle moze byc miedzy temperaturami minmalnie - i mnozmy razy tą wartość i wychodzi nam gdzie powinna być kropka
$chartBreaks = $chartHeight / 1.2;
imageline($im, $marginLeft, $marginTop-7, $marginLeft, $height-$marginBottom, $black);
//oy:
$valuesOfTemperature = array("  .2", "37.0", "  .8", "  .6", "  .4", "36.2");
$indexOfvaluesOfTemperature = 0;
$currentheight = $marginTop;
for ($i=0; $i < 6; $i++) { 
    //NUMBERS
    imagestring($im, 5, $margin-5, $currentheight-5, $valuesOfTemperature[$indexOfvaluesOfTemperature], $black);
    //DATTECHED LINE
    imageline($im, $marginLeft, $currentheight, $width-$marginRight, $currentheight, IMG_COLOR_STYLED);
    //NEAR NUMBERS
    imageline($im,$marginLeft-5, $currentheight, $marginLeft+5,$currentheight, $black);

    $indexOfvaluesOfTemperature++;
    $currentheight+=$chartHeight/6;
}
imageline($im, $marginLeft, $marginTop+$chartHeight/6, $width-$marginRight, $marginTop+$chartHeight/6, $red);
//ox
imageline($im, $marginLeft, $height-$marginBottom, $width-$marginRight, $height-$marginBottom, $black);
$gap = $chartWidth/($dayCount+1);
$distance = $gap+$marginLeft;
$currentDataIndex = 0;
for ($i=1; $i <= $dayCount; $i++) { 
    imageline($im, $distance, $marginTop, $distance, $height-$marginBottom, IMG_COLOR_STYLED);
    imageline($im, $distance, $height-$marginBottom-5, $distance, $height-$marginBottom+5, $black);
    imagestring($im,5, $distance-5, $height-$marginBottom+10, $i, $black);

    //przeniesc to glebiej ale jak narazie nie
    if(array_key_exists($currentDataIndex, $data) && $data[$currentDataIndex]['id'] == $i){
        if($i>1){
            dotInChart($i, $tableOfColors, $im, $distance, $marginTop, $chartBreaks, $data[$currentDataIndex], $data[$currentDataIndex-1], $chartHeight, $gap);
        }
        else
            dotInChart($i, $tableOfColors, $im, $distance, $marginTop, $chartBreaks, $data[$currentDataIndex], $data[$currentDataIndex], $chartHeight, $gap);

        $currentDataIndex++;
    }
    else{
        $temperaturePoints[] = new TemperaturePoint($i, $distance, $chartHeight+$marginTop, 0,Status::Nothing);
        imagefilledellipse($im, $distance, $chartHeight+$marginTop, 10, 10, $tableOfColors['gray']);
    }
    //loedu llicze te punkty zapisz w tablicy, 
    //ob_start
    // return tego buffer
    $distance+=$gap;
}




//dots






function dotInChart($id, $tableOfColors, $im, $distance, $marginTop, $chartBreaks, $row, $earlierRow, $chartHeight, $gap){
    global $temperaturePoints;
    if($row['temperature'] == 0){
        imagefilledellipse($im, $distance, $chartHeight+$marginTop, 10, 10, $tableOfColors['gray']);
        $temperaturePoints[] = new TemperaturePoint($id, $distance, $chartHeight+$marginTop, 0,Status::Nothing);
    }
    else if($row['temperature'] == -1){
        imagefilledellipse($im, $distance, $chartHeight+$marginTop, 10, 10, $tableOfColors['red']);
        $temperaturePoints[] = new TemperaturePoint($id, $distance, $chartHeight+$marginTop, -1,Status::Ill);
    }
    else{
        imagefilledellipse($im, $distance, $marginTop+$chartBreaks*(37.2-$row['temperature']), 10, 10, $tableOfColors['blue']);

        $temperaturePoints[] = new TemperaturePoint($id, $distance, $marginTop+$chartBreaks*(37.2-$row['temperature']), $row['temperature'], Status::Normal);

        if($row['id'] - $earlierRow['id'] == 1 && $earlierRow['temperature'] > 0){
            imageline($im,$distance-$gap, $marginTop+$chartBreaks*(37.2-$earlierRow['temperature']), $distance,$marginTop+$chartBreaks*(37.2-$row['temperature']), $tableOfColors['blue']);
        }
    }
}



// imageline($im, 0, 40, 500, 40, IMG_COLOR_STYLED);
// imagesetstyle($im, $arr);

// imageline($im, 0, 42, 500, 42, IMG_COLOR_STYLED);
// imagesetstyle($im, $arr);

// imageline($im, 0, 44, 500, 44, IMG_COLOR_STYLED);
function generatedPoints(): array {
    global $temperaturePoints;
    return $temperaturePoints;
}

// echo '<pre>'; print_r($temperaturePoints); echo '</pre>';
// echo $temperaturePoints[0]->healthStatus->value;

// imagepng($im);
?>