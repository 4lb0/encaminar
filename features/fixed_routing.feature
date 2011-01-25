Feature: Fixed routing
  Create simple routes without specific page controllers.
  As web developer I want to simplify the route set up.
  Scenario: Simple route
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
    Given a class "A" with methods "/** @Route("GET /a") */ function a(){ return 'a'; } /** @Route("GET /aa") */ function aa(){ return 'aa'; }"
    When I set the url "/a"
    And I set the method "GET"
    And I set up the front controller as "A"
    Then the output should be "a"

  Scenario: Get with last slash is the same that without the slash
    Given a class "S" with methods "/** @Route("GET /s") */ function s(){ return 's'; }"
    When I set the url "/s/"
    And I set the method "GET"
    And I set up the front controller as "S"
    Then the output should be "s"

  Scenario: Different HTTP method
    Given a class "M" with methods "/** @Route("GET /") */ function g(){ return 'G'; } /** @Route("POST /") */ function p(){ return 'P'; }"
    When I set the url "/"
    And I set the method "POST"
    And I set up the front controller as "M"
    Then the output should be "P"

  Scenario: Chain routes
    Given a class "How" with methods "/** @Route("GET /how/") */ function h(){ return new Encaminar(new You); }"
    And a class "You" with methods "/** @Route("GET /are/you") */ function a(){ return new Encaminar(new Today); }"
    And a class "Today" with methods "/** @Route("GET /today") */ function t(){ return 'I\'m fine'; }"
    When I set the url "/how/are/you/today"
    And I set the method "GET"
    And I set up the front controller as "How"
    Then the output should be "I'm fine"
  
  Scenario: Chain routes with index
    Given a class "Main" with methods "/** @Route("GET /admin") */ function admin(){ return new Encaminar(new Admin); }"
    And a class "Admin" with methods "/** @Route("GET .") */ function index(){ return 'Admin Index'; }"
    When I set the url "/admin"
    And I set the method "GET"
    And I set up the front controller as "Main"
    Then the output should be "Admin Index"  
    
  Scenario: Not found URL
    Given a class "Error" with methods "/** @Route("GET /") */ function index(){ return 'index'; }"
    When I set the url "/not-found"
    And I set the method "GET"
    And I set up the front controller as "Error"
    Then the exception should be "Encaminar\NotFound"  
    
    