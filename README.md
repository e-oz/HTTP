HTTP
========
Objects to easy manipulate by HTTP-requests and responses  

Example of sending request:

	$Response = new Response();
	$Request  = new Request();
	$Request->setMethod(Request::method_POST);
	$Request->setHeader('Authorization', 'auth_token');
	$Request->setDataKey('some_post_key', 'some value');
	$Request->Send('https://api.example.com', $Response);
	
	print $Response->getBody();

##Features
**Headers in Request and Response are case-insensitive:**

	$Request->setHeader('Authorization', 'token');  
	$Request->setHeader('authorization', 'auth_token');  
	
	// second line will not create new header and will update existing one    
	// $Request->getHeaders('AUTHORIZATION') will return:    
	// auth_token  

**Body of response can be automatically unpacked from JSON or XML**

	// next line will create header Accept: JSON in request,
	// and body of response will be automatically decoded from JSON
	
	$Response->setSerializer(new SerializerJSON());
  
**SSL support**  
**Easy to test and extend**  

##License
[MIT](http://en.wikipedia.org/wiki/MIT_License)
