Feature: Publish Content
	In order to express myself
	As an author
	I need to be able to publish content

	Scenario: Posting a note
		Given a user Bob
		And a site "Bob's Blog" at "http://bob.smol.blog/"
		When Bob creates a Note
		Then a Note Created event is dispatched with the content
		And the content is stored in the ContentStateRepo
