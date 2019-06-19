Feature: List features
	
	Scenario: Request Lists collection
		Given there are 5 Lists with 1 Items each
		And the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		When I request "lists" using HTTP GET
		Then the response code is 200
		And the "Content-Type" response header is "application/json"
		And the "Allow" response header exists
		And the "Allow" response header is "GET, POST"
		And the response body is a JSON array of length 5
	
	Scenario: Request a single List
		Given there are 3 Lists with 2 Items each
		And the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		When I request "lists/2" using HTTP GET
		Then the response code is 200
		And the "Content-Type" response header is "application/json"
		And the "Allow" response header exists
		And the "Allow" response header is "GET, DELETE"
		And the response body contains JSON:
        """
        {
            "id": "@variableType(integer)",
            "title": "@variableType(string)",
            "created_at": "@variableType(string)",
            "items_count": "@variableType(integer)"
        }
        """
	
	Scenario: Request non-existing List
		Given there are 1 Lists with 1 Items each
		And the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		When I request "lists/2" using HTTP GET
		Then the response code is 404
		And the "Content-Type" response header is "application/json"
		And the response body contains JSON:
        """
        {
            "code": 404,
            "message": "Not found"
        }
        """
	
	Scenario: List ID must be numeric
		Given the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		When I request "lists/a" using HTTP GET
		Then the response code is 404
		And the "Content-Type" response header is "application/json"
		And the response body contains JSON:
        """
        {
            "code": 404,
            "message": "@variableType(string)"
        }
        """
	
	Scenario: Request a single List with not allowed method POST
		Given the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		When I request "lists/1" using HTTP POST
		Then the response code is 405
		And the "Content-Type" response header is "application/json"
		And the response body contains JSON:
        """
        {
            "code": 405,
            "message": "@variableType(string)"
        }
        """
	
	Scenario: Adding a List
		Given the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		And the request body is:
        """
        {
            "title": "Test1"
        }
        """
		When I request "lists" using HTTP POST
		Then the response code is 201
		And the "Content-Type" response header is "application/json"
		And the "Location" response header exists
		And the "Location" response header matches "/^\/api\/lists\/\d+$/"
		And the response body contains JSON:
        """
        {
            "id": "@variableType(integer)",
            "title": "@variableType(string)",
            "created_at": "@variableType(string)"
        }
        """
	
	Scenario: List POST request header must be application/json
		Given the "Content-Type" request header is "text/plain"
		And the "Accept" request header is "application/json"
		And the request body is:
        """
        {
            "title": "Test2"
        }
        """
		When I request "lists" using HTTP POST
		Then the response code is 400
		And the "Content-Type" response header is "application/json"
		And the response body contains JSON:
        """
        {
            "code": 400,
            "message": "Validation Failed",
            "errors": {
                "children": {
                    "title": {
                        "errors": [
                        "This value should not be blank."
                        ]
                    }
                }
            }
        }
        """
	
	Scenario: List must not contain extra fields
		Given the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		And the request body is:
        """
        {
            "title": "Test7878", "id": 101
        }
        """
		When I request "lists" using HTTP POST
		Then the response code is 400
		And the "Content-Type" response header is "application/json"
		And the response body contains JSON:
        """
        {
            "code": 400,
            "message": "Validation Failed"
        }
        """
	
	Scenario: List title must not be blank
		Given the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		And the request body is:
        """
        {
            "title": ""
        }
        """
		When I request "lists" using HTTP POST
		Then the response code is 400
		And the "Content-Type" response header is "application/json"
		And the response body contains JSON:
        """
        {
            "code": 400,
            "message": "Validation Failed"
        }
        """
	
	Scenario: Adding a List with wrong parameter name
		Given the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		And the request body is:
        """
        {
            "titled": ""
        }
        """
		When I request "lists" using HTTP POST
		Then the response code is 400
		And the "Content-Type" response header is "application/json"
		And the response body contains JSON:
        """
        {
            "code": 400,
            "message": "Validation Failed"
        }
        """
	
	Scenario: Adding a List with invalid json
		Given the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		And the request body is:
        """
        {
            "titled" => ""
        }
        """
		When I request "lists" using HTTP POST
		Then the response code is 400
		And the "Content-Type" response header is "application/json"
		And the response body contains JSON:
        """
        {
            "code": 400,
            "message": "Invalid json message received"
        }
        """
	
	Scenario: Delete a List
		Given there are 3 Lists with 2 Items each
		And the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		When I request "lists/3" using HTTP DELETE
		Then the response code is 204
		And the response reason phrase is "No Content"
	
	Scenario: Deleted List ID must be numeric
		And the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		When I request "lists/a" using HTTP DELETE
		Then the response code is 404
		And the "Content-Type" response header is "application/json"
		And the response body contains JSON:
        """
        {
            "code": 404,
            "message": "@variableType(string)"
        }
        """