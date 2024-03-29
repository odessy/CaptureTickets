// Simple extension to capture http request

chrome.webRequest.onBeforeRequest.addListener(
  function(request) {
    
	if (request.method == "POST" || request.method == "PUT") {
		
		console.log("intercepted: " + request.url);
		
		var requestBody = {};

		if (request.requestBody && request.requestBody.formData) {
			requestBody = request.requestBody.formData;
		} else {
		  if(request.requestBody.raw.length > 0){

			  var dataView = new DataView(request.requestBody.raw[0].bytes);
			  var decoder = new TextDecoder('utf-8');
			  var json = decoder.decode(dataView);
			  try{
				  requestBody = JSON.parse(json);
			  }
			  catch( err ){
				var obj = {};
				var data = json.split("&");
				for(var key in data)
				{
					var vals = data[key].split("=");
					if(vals[1] != null) obj[vals[0]] = vals[1];
				}
				console.log( err );
				requestBody = obj;
			  }
		  }
		}
			  
		var payload = {
			url: request.url,
			currentDate: new Date().toLocaleString(),
			method: request.method,
			body: requestBody
		};
		
		var myreq = new Request("http://localhost/tickets/webhook.php", {
			method: 'POST',
			mode: 'no-cors',
			body: JSON.stringify(payload)
		});
			  
		fetch(myreq).then(function(response){});
	}
	
  },
  // filters
  {
    urls: [
        "*://secure.helpscout.net/*",
        "*://*.myshopify.com/*",
		"*://app.frontapp.com/*"
    ]
  },
  ["requestBody"]);
 