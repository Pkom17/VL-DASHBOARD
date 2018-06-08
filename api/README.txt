API coded by Caleb Steele-Lane 2018

API Usage:

	GET request
		Get by parameter(s)
			Used to get a entries in a table that match the provided parameter(s)
			ie. http://localhost/api/index.php/{tableName}?{columnName}={columnValue}
			
			returns JSON encoded object with a JSONArray in the message object of all returned results
			ie {"success":1,"message":[{"id":"0","lastupdated":"2018-06-08"...}]
			
		Get by id
			Used to get a single entry based on it's foreignId
			ie. http://localhost/api/index.php/{tableName}/{id}
			
			returns JSON encoded object with a JSONArray in the message object of returned results
			ie {"success":1,"message":[{"id":"0","lastupdated":"2018-06-08"}]
			

	POST request
		POST insert
			Inserts a new object into the foreign table by placing a JSON object in the POST body
			ie. http://localhost/api/index.php/{tableName}
				body: {"column1":"value1","column2":"value2"}
	
	PUT request
		PUT update
			Updates an already existing object into the foreign table by placing the new values in the PUT body
			ie. http://localhost/api/index.php/{tableName}/{id}
				body: {"column1":"value1","column2":"value2"}
				
Security:

	SQL injection protection
		SQL injection is provided by a number of mechanisms: 
			Table Names are checked against a whitelist of all valid table names
			Table columns are checked against a whitelist of valid characters (alphanumeric and underscore)
			Table values are protected by using prepared statements in PDO
			
		Violations of these principles cause the sql to be discarded and an error response to be returned
		
	Resource protection
		All resources are completely open. As long as the request is well formed, the insert, update, or select will be run on any and all resources.