<?php

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
    $milliseconds = round(microtime(true) * 1000);
                if(isset($_POST['topic']))
                {
                 $topic=$_POST['topic'];
                 $sqlString = "SELECT topic_id FROM topic WHERE name='$topic'";
                 $result = mysqli_query($con,$sqlString) OR die(mysqli_error($con));
                 $row=$result->fetch_row();
                 $main_id=$row[0];
                $sqlString = "SELECT client_id FROM data WHERE main_id='$main_id'";
                $data = array();
                $gcmRegID = mysqli_query($con,$sqlString) OR die(mysqli_error($con));
                while(($row = mysqli_fetch_array($gcmRegID))) {
                               $data[] = $row['client_id'];
                              }
                $req=array();
                $uuid = uniqid('',true);
                $req['uuid']= $uuid;
                $req['type']="Request";
                $req['main_id']=$main_id;
                $message = array("m" => json_encode($req));	
		$pushStatus = sendMessageThroughGCM($data, $message);
	        }
?>
<!DOCTYPE html>
<html>
<body>
<h2 id="main_topic" align="center"></h2><br>
<div id="content" style="color:#000000">
</div>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script>
        document.title =<?php echo json_encode($topic);?>;
        var main_topic = document.getElementById('main_topic');
        main_topic.innerHTML=<?php echo json_encode($topic);?>;
        initiate();

         function initiate() {
            if ("WebSocket" in window) {
                var ws = new WebSocket("ws://socket-nirvana666.rhcloud.com:8000");
                var uuid = <?php echo json_encode($uuid);?>;
                ws.onopen = function() {
                    var hello = {
                        "topic": main_topic.innerHTML,
                        "uuid" : uuid
                    };
                    ws.send(JSON.stringify(hello));
                };
                ws.onmessage = function(evt) {
                    var obj = JSON.parse(evt.data);
                    $("#content").append("<p>"+obj.data+"</p>");
                }
              }
         }     
      </script>		
</body>
</html>
					