Feature: test

	Scenario: Register New User
        When I POST url "api/register"
        And I fill "username" with "testbehat"
        And I fill "password" with "testbehat"
        And I fill "email" with "testbehat@gmail.com"
        And I fill "name" with "Test Behat"
        Then I store it

    Scenario: Verification Email
        When I verify user with email "testbehat@gmail.com"

    Scenario: Login User
        When I POST url "api/login"
        And I fill "username" with "testbehat"
        And I fill "password" with "testbehat"
        Then I store it