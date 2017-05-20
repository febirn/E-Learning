Feature: Article

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

    Scenario: Create Article
        Given token with username "testbehat"
        When I "POST" in "api/admin/article/create"
        And I fill post with this:
        | title       | category | content      | is_publish |
        | testarticle | test     | Test Article | 1          |
        |             | article  |              |            |
        Then I store it

    Scenario: Edit Article
        Given token with username "testbehat"
        Given information about "articles" by "title" "testarticle"
        When I "PUT" in "api/admin/article/edit" by column "title_slug"
        And I fill post with this:
        | title           | category | content           | is_publish |
        | testarticleedit | test     | Test Edit Article | 1          |
        |                 | edit     |                   |            |
        |                 | article  |                   |            |

    Scenario: Show All Article
        Given token with username "testbehat"
        When I "GET" in "api/admin/article/all"

    Scenario: Soft Delete Article
        Given token with username "testbehat"
        Given information about "articles" by "title" "testarticle"
        When I "PUT" in "api/admin/article/soft_delete" by column "title_slug"
        Then I store it

    Scenario: Show Trash Article
        Given token with username "testbehat"
        When I "GET" in "api/admin/article/trash"

    Scenario: Hard Delete Article
        Given token with username "testbehat"
        Given information about "articles" by "title" "testarticle"
        When I "DELETE" in "api/admin/article/hard_delete" by column "title_slug"


    Scenario: Delete New User
        When I delete user with username "testbehat"