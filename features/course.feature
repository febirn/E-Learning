Feature: Course Feature

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

    Scenario: Change Role to Super Admin
        Given information about "users" by "username" "testbehat"
    	When my role change to "Super Admin" role

    Scenario: Register New User
        When I "POST" in "api/register"
        And I fill post with this:
        | username  | password  | email                 | name       |
        | testbehat1 | testbehat | testbehat1@gmail.com | Test Behat |
        Then I store it

    Scenario: Get Add Admin Course
    	Given token with username "testbehat"
    	When I "GET" in "api/admin/course/add_admin_course"
    
    Scenario: Put Add Admin Course
    	Given token with username "testbehat"
    	Given information about "users" by "username" "testbehat1"
    	When I "PUT" in "api/admin/course/add_admin_course"
    	And I want add admin course by username "testbehat1"
    	Then I store it

    Scenario: Delete New User
        When I delete user with username "testbehat"

    Scenario: Delete New User
        When I delete user with username "testbehat1"