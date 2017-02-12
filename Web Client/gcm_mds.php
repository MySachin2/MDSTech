<?php
	//Generic php function to send GCM push notification
   session_start();
   require_once("config.php");
   $con = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE) or die ("connection failed");
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
	//Post message to GCM when submitted
	$pushStatus = "GCM Status Message will appear here";	
	if(!empty($_GET["push"])) {
                $sqlString = "SELECT reg_id FROM users";
                $data = array();
                $gcmRegID = mysqli_query($con,$sqlString) OR die(mysqli_error($con));
                while(($row = mysqli_fetch_array($gcmRegID))) {
                               $data[] = $row['reg_id'];
                              }
		$pushMessage = '{"client_id":1,"main_id":2,"sub_id":3}';	
		if (isset($gcmRegID) && isset($pushMessage)) {		
			$message = array("m" => $pushMessage);	
			$pushStatus = sendMessageThroughGCM($data, $message);
		}		
	}
	
	//Get Reg ID sent from Android App and store it in text file
	if(!empty($_GET["shareRegId"])) {
		$gcmRegID  = $_POST["regId"]; 
                $email =  $_POST["email"];
                $imei =  $_POST["imei"];
                $sqlString = "INSERT INTO users(reg_id,email,imei) VALUES('$gcmRegID','$email','$imei')";
                $detail = mysqli_query($con,$sqlString) OR die(mysqli_error($con));
		echo "Done!";
		exit;
	}	
	echo $pushStatus; ?>