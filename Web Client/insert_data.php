<?php
   require_once("config.php");
   $con = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE) or die ("connection failed");
   $sqlstring="SELECT imei FROM users";
   $result=mysqli_query($con,$sqlstring);
   $data = array();
   while(($row = mysqli_fetch_array($result))) {
             $data[] = $row['imei'];
             }
   function sendMessageThroughGCM($registration_ids, $message) {
		//Google cloud messaging GCM-API url
        $url = 'https://android.googleapis.com/gcm/send';
        $fields = array(
            'registration_ids' => $registration_ids,
            'data' => $message,
        );
		// Update your Google Cloud Messaging API Key
		define("GOOGLE_API_KEY", "AIzaSyAZkvlEZxGIsuMhhlvCLM750GiloYfEa78"); 		
        $headers = array(
            'Authorization: key=' . GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);	
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);				
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }
  if(isset($_POST['imei'])&&isset($_POST['main_id'])&&isset($_POST['sub_id'])&&isset($_POST['data'])&&isset($_POST['sub_name']))
   {
         $imei=$_POST['imei'];
         $main_id=$_POST['main_id'];
         $sub_id=$_POST['sub_id'];
         $sub_name=$_POST['sub_name'];
         $dat=$_POST['data'];
         $sqlstring="SELECT reg_id FROM users WHERE imei='$imei'";
         $result=mysqli_query($con,$sqlstring);
         $data = array();
         $row = $result->fetch_row();
         $data[0]=$row[0];
         $client_id=$data[0];
         $sqlstring="SELECT * FROM data WHERE client_id='$client_id' AND main_id='$main_id' AND sub_id='$sub_id'";
         $result=mysqli_query($con,$sqlstring);
         $sqlstring="SELECT * FROM data WHERE main_id='$main_id' AND sub_id='$sub_id'";
         $result2=mysqli_query($con,$sqlstring);
         $num_rows = mysqli_num_rows($result);
         $num_rows_2 = mysqli_num_rows($result2);
         if($num_rows==0 && $num_rows_2<4)
         {
             $cluster_id=$main_id.$sub_id;
             $sqlstring="INSERT INTO data(client_id,main_id,sub_id,sub_name,cluster_id) VALUES ('$client_id','$main_id','$sub_id','$sub_name','$cluster_id')";
             $result=mysqli_query($con,$sqlstring);
         }
         $field=array("type"=>"Insert","main_id"=>$main_id,"sub_id"=>$sub_id,"data"=>$dat);
         $message = array("m" => json_encode($field));
         $pushStatus = sendMessageThroughGCM($data, $message);	
         echo $pushStatus;
   }
?>

<html>
<body>
<h2>SELECT IMEI</h2>
<select id="imei" name="imei" form="sub_form">
</select><br>
<form id="sub_form" method="post">

Main ID: <input type="text" name="main_id"><br>
Sub ID: <input type="text" name="sub_id"><br>
SubTopic Name: <input type="text" name="sub_name"><br>
Data: <input type="text" name="data"><br>
<input type="submit" value="Submit">
</form>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>
var select = document.getElementById('imei');
var obj = <?php echo json_encode($data); ?>;
for(i in obj)
{
    var opt = document.createElement('option');
    opt.value = obj[i];
    opt.innerHTML = obj[i];
    select.appendChild(opt);
}

$(document).ready(function(){
    $('.button').click(function(){
        var ajaxurl = 'test.php',
        data =  {'action': clickBtnValue};
        $.post(ajaxurl, data, function (response) {
            alert("action performed successfully");
        });
    });

});
</script>
</body>
</html>
	