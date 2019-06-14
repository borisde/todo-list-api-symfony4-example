Feature: List features

  Scenario: Getting lists collection
    Given the "Content-Type" request header is "application/json"
    And the "Accept" request header is "application/json"
    When I request "/api/lists" using HTTP GET
    Then the response code is 200
    And the "Content-Type" response header is "application/json"
    And the response body is a JSON array with a length of at least 0

  Scenario: Adding a list as json
    Given the "Content-Type" request header is "application/json"
    And the "Accept" request header is "application/json"
    And the request body is:
        """
        {
            "title": "Test1"
        }
        """
    When I request "/api/lists" using HTTP POST
    Then the response code is 201
    And the "Content-Type" response header is "application/json"
    And the response body contains JSON:
        """
        {
            "id": "@variableType(integer)",
            "title": "@variableType(string)"
            "created_at": "@variableType(string)"
        }
        """

  Scenario: Adding a list as plain text
    Given the "Content-Type" request header is "text/plain"
    And the "Accept" request header is "application/json"
    And the request body is:
        """
        {
            "title": "Test2"
        }
        """
    When I request "/api/lists" using HTTP POST
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

  Scenario: Adding a list with extra fields
    Given the "Content-Type" request header is "application/json"
    And the "Accept" request header is "application/json"
    And the request body is:
        """
        {
          "title": "Test7878", "id": 101
        }
        """
    When I request "/api/lists" using HTTP POST
    Then the response code is 400
    And the "Content-Type" response header is "application/json"
    And the response body contains JSON:
    """
      {
        "code": 400,
        "message": "Validation Failed"
      }
    """

  Scenario: Adding a list with blank title
    Given the "Content-Type" request header is "application/json"
    And the "Accept" request header is "application/json"
    And the request body is:
        """
        {
          "title": ""
        }
        """
    When I request "/api/lists" using HTTP POST
    Then the response code is 400
    And the "Content-Type" response header is "application/json"
    And the response body contains JSON:
    """
      {
        "code": 400,
        "message": "Validation Failed"
      }
    """

  Scenario: Adding a list with wrong parameter name
    Given the "Content-Type" request header is "application/json"
    And the "Accept" request header is "application/json"
    And the request body is:
        """
        {
          "titled": ""
        }
        """
    When I request "/api/lists" using HTTP POST
    Then the response code is 400
    And the "Content-Type" response header is "application/json"
    And the response body contains JSON:
    """
      {
        "code": 400,
        "message": "Validation Failed"
      }
    """

  Scenario: Adding a list with invalid json
    Given the "Content-Type" request header is "application/json"
    And the "Accept" request header is "application/json"
    And the request body is:
        """
        {
          "titled" => ""
        }
        """
    When I request "/api/lists" using HTTP POST
    Then the response code is 400
    And the "Content-Type" response header is "application/json"
    And the response body contains JSON:
    """
      {
        "code": 400,
        "message": "Invalid json message received"
      }
    """