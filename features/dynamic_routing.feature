Feature: Dynamic routing
  Support for rules on the routes.  
  As web developer I want to set up routes with rules so I can use parameters on the URL
  Scenario: Get one param in the URL
    Given a class "Users" with methods "/** @Route("GET /user/:id") */ public function showUser($params){ return 'list '.$params['id']; }"
    When I set the url "/user/1"
    And I set the method "GET"
    And I set up the front controller as "Users"
    Then the output should be "list 1"

  Scenario: Get multiple params in the URL
    Given a class "Blog" with methods "/** @Route("GET /archive/:y/:m") */ public function archive($p){ return $p['m'].'-'. $p['y']; }"
    When I set the url "/archive/2011/02"
    And I set the method "GET"
    And I set up the front controller as "Blog"
    Then the output should be "02-2011"
        
  Scenario: Get multiple params in the URL
    Given a class "Gallery" with methods "/** @Route("GET /gallery/:id") */ public function g($p){ $ph = new Photo; $ph->gallery = $p['id']; return new Encaminar($ph); }"
    And a class "Photo" with methods "/** @Route("GET photo/:id") */ public function p($p){ return $this->gallery . '.' . $p['id']; }"
    When I set the url "/gallery/32/photo/127"
    And I set the method "GET"
    And I set up the front controller as "Gallery"
    Then the output should be "32.127"
    