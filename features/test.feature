Feature: test

	Scenario: test
		When I POST url "test" with param:
		| a | b |
		| 1 | 2 |
		And I fill "test" with "test"
		And I fill "aa" with "aa"
		And I fill "bb" with "bb"
		Then I store it