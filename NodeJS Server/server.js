var WebSocketServer = require('ws').Server;
var http = require('http');
var querystring = require('querystring');

var ipaddr  = process.env.OPENSHIFT_NODEJS_IP || "127.0.0.1";
var port      = process.env.OPENSHIFT_NODEJS_PORT || 3000

var server = http.createServer(function(request, response) {

        response.writeHead(200, {"Content-Type": "text/plain","Access-Control-Allow-Origin":"*"});
        if(request.method == "POST")
        {
                var url = request.url;
                if(url == "/auth")
                {
                	var body = '';
                	request.on('data', function(chunk)
                	{
                    	body += chunk.toString();
                	});
            		request.on('end', function () 
            		{
            	    console.log("Received Params: "+querystring.unescape(body.replace(/\+/g, " ")));
                	var fullstr = querystring.unescape(body.replace(/\+/g, " "));
                	var strArr = fullstr.split('&');

                	var uuid = strArr[3].split('=')[1];
                	var msg = {'sub_topic': strArr[2].split('=')[1],'data':strArr[0].split('=')[1]};
                	console.log(msg);
                	 if(clients[uuid] != undefined || clients[uuid] != null)
                        {
                                clients[uuid].send(JSON.stringify(msg),{mask:false});
                                //delete clients[uuId];
                                response.end('{"status":"OK"}');
                        }
                	});
                }
        }
    });

var  wss = new WebSocketServer({server:server});
var clients = {};
console.log(wss);
wss.on('connection', function(ws) {
    console.log('/connection connected');
    ws.on('message', function(data, flags) {
        var obj = JSON.parse(data);
        console.log("Receivied:" + JSON.stringify(obj));
        clients[obj.uuid] = ws;
    });
    ws.on('close', function() {
      console.log('Connection closed!');
    });
    ws.on('error', function(e) {
      console.log(e);
    });
});

console.log('Listening at IP ' + ipaddr +' on port '+port);
server.listen(port,ipaddr);