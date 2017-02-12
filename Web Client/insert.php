<script> var time=new Date()).getTime();
</script>
<?php
        require_once("config.php");
	$con = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE) or die ("connection failed");

	$main=$_GET['main_id'];
	$info=$_GET['info'];
	$gcm_id=$_GET['gcm_id'];
        $time= round(microtime(true) * 1000);
	$sqlString = "UPDATE data SET response_time='$time',info='$info' WHERE main_id='$main' AND client_id='$gcm_id'";
        $detail = mysqli_query($con,$sqlString) OR die(mysqli_error($con));
        if($detail)
                echo 'Success';
        else
                echo 'Failiure';
?>		