<?php 

if ($_SERVER["REQUEST_METHOD"] == "GET") {
  
        render("index.php");

     }

else if($_SERVER["REQUEST_METHOD"] == "POST") {
   
        
        $url = file_get_contents('http://www.shiksha.com/b-tech/colleges/b-tech-colleges-'.urlencode(strtolower($_POST["city"])));

        // for name of college
        preg_match_all('/<h2 class="tuple-clg-heading"><a(.+)>(.+)<\/a>
<p>(.+)<\/p>/i', $url, $matches1);

        print_r($matches1[0]);

        // for number of reviews
        preg_match_all('/<div class="tuple-revw-sec">
<span><b>(\d+)<\/b>/i', $url, $matches2);
        
        print_r($matches2);

        // for all facilities
        preg_match_all('/<h3>(.+)<\/h3>
<p><\/p>/i', $url, $matches3);

        print_r($matches3);

        // for last facility of a college in the list
        preg_match_all('/<h3>(.+)<\/h3>
<p><\/p>
<\/div>
<\/i>
<\/li>
<\/ul>/i', $url, $matches4);

        print_r($matches4);

        //echo sizeof($matches);
        //echo preg_last_error();
    }
?>

