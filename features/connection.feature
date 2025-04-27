Feature: External connection
	In order to maintain my presence on other sites
	As a User
	I need to be able to connect Smolblog to my other accounts.

	Scenario: Create a new connection
		Given I am logged in as an author
		When I create a new ExampleSocial connection
		Then I am redirected to ExampleSocial
		Then I return to Smolblog with an OAuth code
		Then an ExampleSocial connection called "smolbot" is created for me
		And ExampleSocial channels for "smolbot" are created for me
	
	Scenario: Refresh a connection
		Given I am logged in as an author
		And I have an ExampleSocial connection called "smolbot"
		And I have ExampleSocial channels for "smolbot"
		When I refresh the ExampleSocial connection called "smolbot"
		Then the ExampleSocial connection called "smolbot" is updated
		And ExampleSocial channels for "smolbot" are updated

	Scenario: Remove a connection
		Given I am logged in as an author
		And I have an ExampleSocial connection called "smolbot"
		And I have ExampleSocial channels for "smolbot"
		When I delete the ExampleSocial connection called "smolbot"
		Then the ExampleSocial connection "smolbot" is deleted
		And ExampleSocial channels for "smolbot" are deleted