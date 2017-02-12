<?php
   require_once("config.php");
   $con = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE) or die ("connection failed");
   $sqlstring="SELECT name FROM topic";
   $result=mysqli_query($con,$sqlstring);
   $data = array();
   while(($row = mysqli_fetch_array($result))) {
             $data[] = $row['name'];
             }
?>

<html>
<body>
<h2>SELECT TOPIC</h2>
<select id="topic" name="topic" form="sub_form">
</select><br>
<form action="view.php" id="sub_form" method="post">
<input type="submit" value="Submit">
</form>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>
var select = document.getElementById('topic');
var obj = <?php echo json_encode($data); ?>;
for(i in obj)
{
    var opt = document.createElement('option');
    opt.value = obj[i];
    opt.innerHTML = obj[i];
    select.appendChild(opt);
}

</script>

</body>
</html>