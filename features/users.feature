Feature: Users

	Scenario: Register New User
        When I "POST" in "api/register"
        And I fill post with this:
        | username  | password  | email               | name       |
        | testbehat | testbehat | testbehat@gmail.com | Test Behat |
        Then I store it

    Scenario: Verification Email
        When I verify user with email "testbehat@gmail.com"

    Scenario: Login User
        When I "POST" in "api/login"
        And I fill post with this:
        | username  | password  |
        | testbehat | testbehat |
        Then I store it

    Scenario: Update Profile
    	Given token with username "testbehat"
        Given information about "users" by "username" "testbehat"
    	When I "POST" in "api/profile/edit" by column "id"
    	And I fill post with this:
    	| email               | name            |
    	| testbehat@gmail.com | Test Behat Edit |
    	Then I store it

    Scenario: Change Password
        Given token with username "testbehat"
        When I "PUT" in "api/profile/change_password"
        And I fill post with this:
        | old_password | new_password    | retype_password |
        | testbehat    | testchangebehat | testchangebehat |
        Then I store it

    Scenario: Request Reset Password
    	When I "POST" in "api/password_reset"
    	And I fill post with this:
    	| email               |
    	| testbehat@gmail.com |
    	Then I store it

    Scenario: ReNew Password
    	Given information about "users" by "username" "testbehat"
    	And I set reset password token to "resetpassword"
    	When I "PUT" in "api/renew_password" with param:
    	| token 	   |
    	| resetpassword|
    	And I fill post with this:
    	| password       | retype_password |
    	| testrenewbehat | testrenewbehat  |
    	Then I store it

    Scenario: Find Other User
        Given token with username "testbehat"
        Given information about "users" by "username" "testbehat"
        When I "GET" in "api" by column "username"

    Scenario: Delete New User
        When I delete user with username "testbehat"