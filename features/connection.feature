Feature: Connection to external service
	In order to maintain my social network
	As an admin
	I need to authenticate my site to external services

	Scenario: Connect to a single-source provider
		Given a user Bob
		And an external monolithic service LargeBlog
		When Bob initiates an authentication request against LargeBlog
		Then the request information for Bob is saved
		And the redirect URL is provided
	
	# Scenario: Connect to an IndieAuth provider
	# 	Given a user Bob
	# 	And an external open service using IndieAuth
	# 	When Bob initiates an authentication request against "https://my.blog/"
	# 	Then the request information for Bob is saved
	# 	And the redirect URL is provided
	
	Scenario: Receive a request with a known state
		Given a user Bob
		And an external monolithic service SomeBlog
		And an existing authentication request for Bob against SomeBlog
		When a finished authentication request for Bob against SomeBlog is received
		Then a connection exists for SomeBlog owned by Bob
		And updated channels for SomeBlog exist
	
	Scenario: Receive a request with an unknown state
		Given a user Bob
		And an external monolithic service OtherBlog
		When a finished authentication request for Bob against OtherBlog is received
		Then no connection exists for OtherBlog owned by Bob
		And no channels for OtherBlog exist
	
	Scenario: Refresh channels by owner
		Given a user Bob
		And an external monolithic service LargeBlog
		And an existing connection for LargeBlog owned by Bob
		When the connection for LargeBlog is refreshed by Bob
		Then updated channels for LargeBlog exist
	
	Scenario: Refresh channels by another
		Given a user Bob
		And a user Larry
		And an external monolithic service LargeBlog
		And an existing connection for LargeBlog owned by Bob
		When the connection for LargeBlog is refreshed by Larry
		Then no channels for LargeBlog exist
