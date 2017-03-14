<?php 

if ($_SERVER["REQUEST_METHOD"] == "GET") {
  
        render("index.php");

     }

else if($_SERVER["REQUEST_METHOD"] == "POST") {
   
        
        $url = file_get_contents('http://www.shiksha.com/b-tech/colleges/b-tech-colleges-'.urlencode(strtolower($_POST["city"])));

        ////print_r($url);
        
        //for name of college and location
        preg_match_all('/<h2 class="tuple-clg-heading"><a(.+)>(.+)<\/a>
<p>\| (.+)<\/p>/i', $url, $matches1);

        //print_r($matches1[0]);



        // for number of reviews
        preg_match_all('/<div class="tuple-revw-sec">
<span><b>(\d+)<\/b>/i', $url, $matches2);
        
        /*print_r($matches2);

        print_r($matches2[0]);

        print_r($matches2[1]);

        echo "break";

        print_r($matches2[1][0]);*/

        //echo "break";
        // for all facilities
        preg_match_all('/<h3>(.+)<\/h3>
<p><\/p>/i', $url, $matches3);

        //print_r($matches3);

        // for last facility of a college in the list
        preg_match_all('/<h3>(.+)<\/h3>
<p><\/p>
<\/div>
<\/i>
<\/li>
<\/ul>/i', $url, $matches4);

        //print_r($matches4);

        //echo sizeof($matches);
        //echo preg_last_error();

        $servername = "localhost";
        $username = "chirayu";
        $password = "IlovemyindiA19!";

        // http://stackoverflow.com/questions/6445917/connect-failed-access-denied-for-user-rootlocalhost-using-password-yes

        // Create connection
        $conn = new mysqli($servername, $username, $password, "colleges");

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        //echo "Connected successfully";

        // Create database
        $sql = "CREATE DATABASE colleges";
        if ($conn->query($sql) === TRUE) {
           // echo "Database created successfully";
        } else {
           // echo "Error creating database: " . $conn->error;
        }

        // sql to create table
        $sql = "CREATE TABLE ".strtolower($_POST["city"])."(
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
        college VARCHAR(500) NOT NULL,
        location VARCHAR(500),
        reviews INT(50),
        facilities VARCHAR(500)
        )";

        if ($conn->query($sql) === TRUE) {
            //echo strtolower($_POST["city"]). "created successfully";
        } else {
            //echo "Error creating table: " . $conn->error;
        }

        //$conn->close();
        $k = 0;
        $x = 0;

        for ($i = 0; $i < sizeof($matches1[2]); $i++) {

            if ($matches2[1][$i] == NULL) {

                $matches2[1][$i] = 0;

            }

            if (empty($matches1[2][$i])) {

                $matches1[3][$i] = 'Not Known';

            }

            $facs = "";

            for ($j = $k; $j < sizeof($matches3[1]); $j++)
            {
                if($matches3[1][$j]!=$matches4[1][$x])
                {
                    $facs .= $matches3[1][$j];
                    $facs .= ", ";
                    $k = $k + 1;
                }
                else
                {
                    $facs .= $matches3[1][$j];
                    $k = $k + 1;
                    break;
                }
            }

            $x = $x + 1;

            $collegename = addslashes($matches1[2][$i]);

            $check = "SELECT id FROM ".strtolower($_POST["city"])." WHERE college = '". $collegename."'";

            //$query = query($conn, $check);
            //if ($conn->query($check) == TRUE)
                // to do something here for below TODO

            //{
              //  echo "already exists";
                // TODO
                // if row exists then leave else INSERT
            //}

            //else {

            $result = $conn->query("SELECT * FROM ".strtolower($_POST["city"])." WHERE college = "."'".$collegename."'");

            if (!$result) {
              die($conn->error);
            }
            //echo "num_rows = ".$result->num_rows."\n";
            if ($result->num_rows > 0) {
               //echo "Duplicate email\n";
               // do something to alert user about non-unique email
            } else {
             

            $sql = "INSERT IGNORE INTO ".strtolower($_POST["city"])." (id, college, location, reviews, facilities) VALUES (NULL, "."'".addslashes($matches1[2][$i])."'".", "."'".addslashes($matches1[3][$i])."'".", ".$matches2[1][$i].", "."'". $facs."'" . ")";
            // WHERE NOT EXISTS ( SELECT college FROM ".strtolower($_POST["city"]). " WHERE college = "."'".$collegename."') LIMIT 1"}
                }
            if ($conn->query($sql) === TRUE) {
                //echo "New record created successfully \n";
            } else {
              //  echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

        $sql = "SELECT DISTINCT id, college, location, reviews, facilities FROM ".strtolower($_POST["city"]);
        $result = $conn->query($sql);

        $conn->close();
    }
?>

<table border="1">
    <tr>
        <td>Id</td>
        <td>College</td>
        <td>Location</td>
        <td>Reviews</td>
        <td>Facilities</td>
    </tr>

    <?php if ($result->num_rows > 0) { while($row = $result->fetch_assoc()) { ?>

    <tr>
        <td><?php echo $row["id"] ?></td>
        <td><?php echo $row["college"] ?></td>
        <td><?php echo $row["location"] ?></td>
        <td><?php echo $row["reviews"] ?></td>
        <td><?php echo $row["facilities"] ?></td>
    </tr>

    <?php } }  ?>
</table>

