Feature: Routing

  Assign route in the method that I want to use as controller.

  As web developer I want to simplify the route set up.

  Scenario Outline: Simple routes
    Given a class "<class>" with methods "<methods>"
    When I set the url "<url>"
    And I set the method "<http>"
    And I set up the front controller as "<class>"
    Then the output should be "<output>"

  Examples:
    | class     | url      | http | output | methods |
    | Example   | /hello   | GET  | Hello! | /** @Route("GET /hello") */ public function home(){ return 'Hello!'; } |
    | Index     | /        | GET  | index  | /** @Route("GET /") */ public function indexAction(){ return 'index'; } |
    | Ambiguous | /a       | GET  | a      | /** @Route("GET /a") */ function a(){ return 'a'; } /** @Route("GET /aa") */ function aa(){ return 'aa'; } |
    | TrimSlash | /s/      | GET  | slash  | /** @Route("GET /s") */ function s(){ return 'slash'; } |
    | HttpMethd | /        | POST | post   | /** @Route("GET /") */ function g(){ return 'get'; } /** @Route("POST /") */ function p(){ return 'post'; }  |
    | OneParam  | /user/1  | GET  | usr 1  | /** @Route("GET /user/:id") */ public function usr($params){ return 'usr '.$params['id']; } |
    | MultParam | /a/11/02 | GET  | 02-11  | /** @Route("GET /a/:y/:m") */ public function archive($p){ return $p['m'].'-'.$p['y']; } |

    
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
        
  Scenario: Get multiple params in the URL
    Given a class "Gallery" with methods "/** @Route("GET /gallery/:id") */ public function g($p){ $ph = new Photo; $ph->gallery = $p['id']; return new Encaminar($ph); }"
    And a class "Photo" with methods "/** @Route("GET photo/:id") */ public function p($p){ return $this->gallery . '.' . $p['id']; }"
    When I set the url "/gallery/32/photo/127"
    And I set the method "GET"
    And I set up the front controller as "Gallery"
    Then the output should be "32.127"
    
  Scenario: Not found URL
    Given a class "Error" with methods "/** @Route("GET /") */ function index(){ return 'index'; }"
    When I set the url "/not-found"
    And I set the method "GET"
    And I set up the front controller as "Error"
    Then the exception should be "Encaminar\NotFoundException"  
    
