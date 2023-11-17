<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Praktikos užduotis</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>

<body>
    <div class="my-4 h-100 d-flex justify-content-center align-items-center">
        <form action="index.php" method="get">
            <div class="form-group">
                <label for="title">Title</label>
                <div>
                    <input class="form-control" id="title" name="title" type="text" value="<?php if(empty($_GET['title'])) { echo ""; } else { echo $_GET['title'] ;} ?>" placeholder="Movie title..">
                </div>
            </div>
            <div>
                <label for="run-time">Duration</label>
                <span class="small">(in minutes)</span>
                <div class="d-flex">
                    <input class="form-control" id="run-time-min" name="run-time-min" value="<?php if(empty($_GET['run-time-min'])) { echo ""; } else { echo $_GET['run-time-min'] ;} ?>" type="number" placeholder="1">
                    <span class="my-2 px-2">to</span>
                    <input class="form-control" id="run-time-max" name="run-time-max" value="<?php if(empty($_GET['run-time-max'])) { echo ""; } else { echo $_GET['run-time-max'] ;} ?>" type="number" placeholder="300">
                </div>
            </div>
            <div class="my-4 d-flex justify-content-center">
                <button class="mx-2 px-4 py-2 btn btn-primary" type="submit">Find</button>
                <a class="mx-2 px-4 py-2 btn btn-warning" href="index.php">Reset</a>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>

</html>

<?php
// film klase
class Film {
    public $title;
    public $duration;
    public $image;
    public $description;

    /* public function __construct($title, $duration, $image, $description) {
        $this->title = $title;
        $this->duration = $duration;
        $this->image = $image;
        $this->description = $description;
    } */
    function set_title($title) {
        $this->title = $title;
    }
    function get_title() {
        $this->title;
    }

    function set_image($image) {
        $this->image = $image;
    }
    function get_image() {
        $this->image;
    }

    function set_duration($duration) {
        $this->duration = $duration;
    }

    function set_description($description) {
        $this->description = $description;
    }

    function get_description($description) {
        $this->description = $description;
    }

}

$ch = curl_init();

$url="https://ghibliapi.dev/films?limit=200";

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$resp = curl_exec($ch);

if ($e = curl_error($ch)) {
    echo "". $e;
} else {
    $decoded = json_decode($resp, true);

    // tikrinama ar nustatyti input values
    if(isset($_GET['run-time-min']) && isset($_GET['run-time-max'])) {
    $durationMin = $_GET['run-time-min'];
    $durationMax = $_GET['run-time-max'];
    }
    // jei norima tik 'nuo'
    if(empty($durationMax)) {
        $durationMax = 300;
    }

    if(isset($_GET['title'])) {
        $movieTitle = $_GET['title'];
    } else {
        $movieTitle = '';
    }
    //$amountFound = 0;
    
    // funkcija skirta rasti ar egzistuoja filmo trukme; taip pat ji konvertuoja minutes i valandas bei minutes
    function convert_amount($runtime) {
        if($runtime !='-') {
            $hours = floor($runtime/60);
            $minutes = $runtime%60;
            echo "<div class='d-flex justify-content-center w-100 mx-4'>";
            echo "<h3 class='d-flex justify-content-center'> Duration: ". $hours ." h $minutes min</h3>";
            echo "</div>";
        } else { 
            echo '';
        }
    }   
    
    // looping
    foreach( $decoded as $key => $value ) {
        $film = new Film();
        $film->set_title($value["title"]);
        $film->set_image($value["image"]);
        $film->set_duration($value["running_time"]);
        $film->set_description($value["description"]);
        //search filtras
        $isFound = str_contains(strtolower($value["title"]), strtolower($movieTitle));
        if($isFound === true) {
            // atvaizdavimas su filtru
            if(isset($durationMax) && isset($durationMin) && $film->duration >= $durationMin && $film->duration <= $durationMax) {
                echo "<div class='d-flex justify-content-center mt-4 w-100'>";
                echo "<div class='d-flex p-4 w-50 shadow-lg'>";
                echo "<img class='p-2' width='300' src=$film->image>";
                echo "<div class='w-100 mx-4'>";
                echo "<h1 class='d-flex justify-content-center'>$film->title</h2>";
                echo "<p class='d-flex justify-content-center'>$film->description</p>";
                echo convert_amount($film->duration);
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
        }
    }
}

curl_close($ch);
?>