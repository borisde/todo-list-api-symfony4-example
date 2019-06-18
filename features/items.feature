Feature: Item features
	
	Scenario: Request Items collection
		Given there are 2 Lists with 4 Items each
		And the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		When I request "/api/lists/2/items" using HTTP GET
		Then the response code is 200
		And the "Content-Type" response header is "application/json"
		And the "Allow" response header exists
		And the "Allow" response header is "GET, POST"
		And the response body contains JSON:
        """
        {
            "id": "@variableType(integer)",
            "title": "@variableType(string)",
            "created_at": "@variableType(string)",
            "items": "@arrayLength(4)"
        }
        """
	
	Scenario: Items List ID must be numeric
		Given the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		When I request "/api/lists/abc/items" using HTTP GET
		Then the response code is 404
		And the "Content-Type" response header is "application/json"
		And the response body contains JSON:
        """
        {
            "code": 404,
            "message": "@variableType(string)"
        }
        """
	
	Scenario: Request a single Item
		Given there are 2 Lists with 3 Items each
		And the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		When I request "/api/lists/1/items/3" using HTTP GET
		Then the response code is 200
		And the "Content-Type" response header is "application/json"
		And the "Allow" response header exists
		And the "Allow" response header is "GET, DELETE"
		And the response body contains JSON:
        """
        {
            "id": "@variableType(integer)",
            "description": "@variableType(string)",
            "created_at": "@variableType(string)",
            "list": {
                "id": "@variableType(integer)",
                "title": "@variableType(string)",
                "created_at": "@variableType(string)"
            }
        }
        """
	
	Scenario: Item ID must be numeric
		Given the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		When I request "/api/lists/1/items/a" using HTTP GET
		Then the response code is 404
		And the "Content-Type" response header is "application/json"
		And the response body contains JSON:
        """
        {
            "code": 404,
            "message": "@variableType(string)"
        }
        """
	
	Scenario: Request a non-existing Item
		Given there are 2 Lists with 2 Items each
		And the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		When I request "/api/lists/1/items/3" using HTTP GET
		Then the response code is 404
		And the "Content-Type" response header is "application/json"
		And the response body contains JSON:
        """
        {
            "code": 404,
            "message": "Not found"
        }
        """
	
	Scenario: Request a single Item with not allowed method POST
		Given the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		When I request "/api/lists/1/items/1" using HTTP POST
		Then the response code is 405
		And the "Content-Type" response header is "application/json"
		And the response body contains JSON:
        """
        {
            "code": 405,
            "message": "@variableType(string)"
        }
        """
	
	Scenario: Adding an Item
		Given there are 1 Lists with 0 Items each
		And the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		And the request body is:
        """
        {
            "description": "Test1"
        }
        """
		When I request "/api/lists/1/items" using HTTP POST
		Then the response code is 201
		And the "Content-Type" response header is "application/json"
		And the "Location" response header exists
		And the "Location" response header matches "/^\/api\/lists\/\d+\/items\/\d+$/"
		And the response body contains JSON:
        """
        {
            "id": "@variableType(integer)",
            "description": "@variableType(string)",
            "created_at": "@variableType(string)"
        }
        """
	
	Scenario: Item POST request header must be application/json
		Given there are 1 Lists with 1 Items each
		And the "Content-Type" request header is "text/plain"
		And the "Accept" request header is "application/json"
		And the request body is:
        """
        {
            "title": "Test2"
        }
        """
		When I request "/api/lists/1/items" using HTTP POST
		Then the response code is 400
		And the "Content-Type" response header is "application/json"
		And the response body contains JSON:
        """
        {
            "code": 400,
            "message": "Validation Failed",
            "errors": {
                "children": {
                    "description": {
                        "errors": [
                        "This value should not be blank."
                        ]
                    }
                }
            }
        }
        """
	
	Scenario: Item must not contain extra fields
		Given there are 1 Lists with 1 Items each
		And the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		And the request body is:
        """
        {
            "description": "Test1", "id": 101
        }
        """
		When I request "/api/lists/1/items" using HTTP POST
		Then the response code is 400
		And the "Content-Type" response header is "application/json"
		And the response body contains JSON:
        """
        {
            "code": 400,
            "message": "Validation Failed"
        }
        """
	
	Scenario: Item description must not be blank
		Given there are 1 Lists with 1 Items each
		And the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		And the request body is:
        """
        {
          "description": ""
        }
        """
		When I request "/api/lists/1/items" using HTTP POST
		Then the response code is 400
		And the "Content-Type" response header is "application/json"
		And the response body contains JSON:
        """
        {
            "code": 400,
            "message": "Validation Failed"
        }
        """
	
	Scenario: Adding an Item with wrong parameter name
		Given there are 1 Lists with 1 Items each
		And the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		And the request body is:
        """
        {
            "descriptionnn": ""
        }
        """
		When I request "/api/lists/1/items" using HTTP POST
		Then the response code is 400
		And the "Content-Type" response header is "application/json"
		And the response body contains JSON:
        """
        {
            "code": 400,
            "message": "Validation Failed"
        }
        """
	
	Scenario: Added Item List ID must be numeric
		Given there are 1 Lists with 1 Items each
		And the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		And the request body is:
        """
        {
            "description": ""
        }
        """
		When I request "/api/lists/a/items" using HTTP POST
		Then the response code is 404
		And the "Content-Type" response header is "application/json"
		And the response body contains JSON:
        """
        {
            "code": 404,
            "message": "@variableType(string)"
        }
        """
	
	Scenario: Delete Item
		Given there are 2 Lists with 2 Items each
		And the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		When I request "/api/lists/1/items/2" using HTTP DELETE
		Then the response code is 204
		And the response reason phrase is "No Content"
	
	Scenario: Deleted Item ID must be numeric
		Given there are 2 Lists with 2 Items each
		And the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		When I request "/api/lists/1/items/a" using HTTP DELETE
		Then the response code is 404
		And the "Content-Type" response header is "application/json"
		And the response body contains JSON:
        """
        {
            "code": 404,
            "message": "@variableType(string)"
        }
        """
	
	Scenario: Deleted Item List ID must be numeric
		Given there are 2 Lists with 2 Items each
		And the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		When I request "/api/lists/aa/items/1" using HTTP DELETE
		Then the response code is 404
		And the "Content-Type" response header is "application/json"
		And the response body contains JSON:
        """
        {
            "code": 404,
            "message": "@variableType(string)"
        }
        """