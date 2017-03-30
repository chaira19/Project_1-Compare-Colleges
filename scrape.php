<html>
	<head>
		<title><?php echo $_POST["$city"] ?></title>
		
		<!--- styling from head section the different divisions -->
		<style type="text/css">
		
			.loader 
			{
				position: fixed;
				left: 0px;
				top: 0px;
				width: 100%;
				height: 100%;
				z-index: 9999;
				background: url('page-loader.gif') 50% 50% no-repeat rgb(249,249,249);
				opacity: .8;
			}

			body {
				background-color:#193753;
			}
		</style>
		
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

		<!-- jQuery library -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

		<!-- Latest compiled JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

		<!-- function to show loading gif -->
		<script type="text/javascript">
			$(window).load(function() 
		{
		  $(".loader").fadeOut("slow");
		})
		</script>
	</head>

<?php 
	
	// render the same page if request is done from url
	if ($_SERVER["REQUEST_METHOD"] == "GET") {
	  
			render("index.php");

		 }
	
	// processed if request is posted from form 
	else if($_SERVER["REQUEST_METHOD"] == "POST") {
	   
			//url of the city at siksha.com
			$url = file_get_contents('http://www.shiksha.com/b-tech/colleges/b-tech-colleges-'.urlencode(strtolower($_POST["city"])));

			//print_r($url);
			
			// regex for name of college and location
			preg_match_all('/<h2 class="tuple-clg-heading"><a(.+)>(.+)<\/a>
	<p>\| (.+)<\/p>/i', $url, $matches1);

			// regex for number of reviews
			preg_match_all('/<div class="tuple-revw-sec">
	<span><b>(\d+)<\/b>/i', $url, $matches2);
			
			// regex for all facilities
			preg_match_all('/<h3>(.+)<\/h3>
	<p><\/p>/i', $url, $matches3);

			// regex for last facility of a college in the list
			preg_match_all('/<h3>(.+)<\/h3>
	<p><\/p>
	<\/div>
	<\/i>
	<\/li>
	<\/ul>/i', $url, $matches4);

			//echo preg_last_error();

			$servername = "localhost";
			$username = "put_your_mysql_username_here"; //mysql username
			$password = "put_your_mysql_password_here"; //mysql password

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

			// sql to create table by the name of the city
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

			//Algorithm for facilities starts here
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

				$result = $conn->query("SELECT * FROM ".strtolower($_POST["city"])." WHERE college = "."'".$collegename."'");

				// to update the table only if there is a new entry
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

			// selection stored in variable for printing in table output
			$sql = "SELECT DISTINCT id, college, location, reviews, facilities FROM ".strtolower($_POST["city"]);
			$result = $conn->query($sql);

			// to close the connection to the database
			$conn->close();
		}

?>


	<body>

		<h1> Colleges in <?= $_POST["city"] ?> </h1>
		<div class="loader"></div>

		<div>

		<table class = "table table-striped">
			<tr>
				<td><b>Id</b></td>
				<td><b>College</b></td>
				<td><b>Location</b></td>
				<td><b>Reviews</b></td>
				<td><b>Facilities</b></td>
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
		</div>
	</body>
</html>

