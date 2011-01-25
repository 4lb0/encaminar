Feature: Routing
  Create simple routes without page controllers.
  As web developer I want to simplify the route set up.
  Scenario: Call static route
    Given a class "Example" with methods "/** @Route("GET /hello") */ public function home(){ return 'Hello!'; }"
    When I set the url "/hello"
    And I set the method "GET"
    And I set up the front controller as "Example"
    Then the output should be "Hello!"

  Scenario: Call index
    Given a class "IndexController" with methods "/** @Route("GET /") */ public function indexAction(){ return 'index'; }"
    When I set the url "/"
    And I set the method "GET"
    And I set up the front controller as "IndexController"
    Then the output should be "index"

  Scenario: Ambigous routes
    Given a class "Amb" with methods "/** @Route("GET /a") */ function a(){ return 'a'; } /** @Route("GET /aaa") */ function aaa(){ return 'aaa'; }"
    When I set the url "/a"
    And I set the method "GET"
    And I set up the front controller as "Amb"
    Then the output should be "a"

  Scenario: Different HTTP method
    Given a class "HTTPMethod" with methods "/** @Route("GET /") */ function g(){ return 'get'; } /** @Route("POST /") */ function p(){ return 'post'; }"
    When I set the url "/"
    And I set the method "POST"
    And I set up the front controller as "HTTPMethod"
    Then the output should be "post"

  Scenario: Chain routes
    Given a class "Index" with methods "/** @Route("GET /hey") */ function hey(){ $e = new Encaminar; return $e(new Hey); }"
    And a class "Hey" with methods "/** @Route("GET /world") */ function w(){ return 'Hey world'; }"
    When I set the url "/hey/world"
    And I set the method "GET"
    And I set up the front controller as "Index"
    Then the output should be "Hey world"
    