Feature: Search features
	
	Scenario: Search Items
		Given there are 4 Items with description "some item description"
		And the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		When I request "/api/search/items?query=item desc" using HTTP GET
		Then the response code is 200
		And the "Content-Type" response header is "application/json"
		And the "Allow" response header exists
		And the "Allow" response header is "GET"
		And the response body contains JSON:
        """
            {
            "total_count": 4,
            "items": "@arrayLength(4)"
            }
        """
	
	Scenario: Search for non-existing Items
		Given there are 4 Items with description "some item description"
		And the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		When I request "/api/search/items?query=test" using HTTP GET
		Then the response code is 200
		And the "Content-Type" response header is "application/json"
		And the "Allow" response header exists
		And the "Allow" response header is "GET"
		And the response body contains JSON:
        """
            {
            "total_count": 0,
            "items": "@arrayLength(0)"
            }
        """
	
	Scenario: Search query should match the pattern [A-Za-z0-9\s]+
		Given there are 1 Items with description "some item description"
		And the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		When I request "/api/search/items?query=test..test" using HTTP GET
		Then the response code is 400
		And the "Content-Type" response header is "application/json"
		And the "Allow" response header exists
		And the "Allow" response header is "GET"
		And the response body contains JSON:
        """
            {
            "code": 400,
            "message": "@variableType(string)"
            }
        """
	
	Scenario: Search must contains query parameter
		Given there are 1 Items with description "some item description"
		And the "Content-Type" request header is "application/json"
		And the "Accept" request header is "application/json"
		When I request "/api/search/items?query=test..test" using HTTP GET
		Then the response code is 400
		And the "Content-Type" response header is "application/json"
		And the "Allow" response header exists
		And the "Allow" response header is "GET"
		And the response body contains JSON:
		"""
            {
            "code": 400,
            "message": "@variableType(string)"
            }
		"""