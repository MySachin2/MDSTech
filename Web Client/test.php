<?php 
        require_once("config.php");
	$con = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE) or die ("connection failed");

	$main=$_GET['main_id'];
	$gcm_ids=$_GET['gcm_id'];

        $idstring = implode("','",$gcm_ids);
        $sqlString = "SELECT COUNT(DISTINCT(sub_id)) AS count FROM data WHERE main_id='$main'";
        $detail = mysqli_query($con,$sqlString) OR die(mysqli_error($con));
        $row = $detail->fetch_row();
        $total=$row[0];
        $sqlString = "SELECT COUNT(DISTINCT(sub_id)) AS count FROM data WHERE main_id='$main' AND NOT info=''";
        $detail = mysqli_query($con,$sqlString) OR die(mysqli_error($con));
        $row = $detail->fetch_row();
        $count=$row[0];
        if($count==$total)
        {
        $output = array();
        $flag=0;
        $sqlString = "SELECT client_id,cluster_id,sub_id,imei,sub_name,info,response_time FROM data,users WHERE main_id='$main' AND NOT info='' AND data.client_id=users.reg_id GROUP BY sub_id";
        $detail = mysqli_query($con,$sqlString) OR die(mysqli_error($con));
        while($row= mysqli_fetch_assoc($detail)){
                     $output[] = $row;
               }
        foreach($gcm_ids as $id)
          {
                     $sqlString = "UPDATE data SET info='' WHERE client_id='$id'";
                     $detail = mysqli_query($con,$sqlString) OR die(mysqli_error($con));
          }
          echo json_encode($output);
        }
        else
            echo('Error');
 
       
         
        /*$row = $detail->fetch_row();

        if($row[0]=='')
              echo('Error');
        else
          {
             $sqlString = "UPDATE data SET info='' WHERE main_id='$main'";
             $detail = mysqli_query($con,$sqlString) OR die(mysqli_error($con));
              echo $row[0];
          }*/
?>