<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Smolblog\Core\Site\Site;
use Smolblog\Core\User\User;
use Smolblog\Foundation\Value\Fields\RandomIdentifier;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context {
	/**
	 * Users in the test.
	 *
	 * @var User[]
	 */
	private array $users = [];

	/**
	 * Sites in the test
	 *
	 * @var Site[]
	 */
	private array $sites = [];

	/**
	 * Initializes context.
	 *
	 * Every scenario gets its own context instance.
	 * You can also pass arbitrary arguments to the
	 * context constructor through behat.yml.
	 */
	public function __construct() {
	}

	/**
	 * @Given a user :arg1
	 */
	public function makeUser($arg1)
	{
			$this->users[$arg1] = new User(
				id: new RandomIdentifier(),
				handle: strtolower($arg1),
				displayName: $arg1,
				pronouns: 'they/them',
				email: "$arg1@smol.blog"
			);
	}

	/**
	 * @Given a site :name at :url
	 */
	public function makeSite($name, $url)
	{
			$this->sites[$name] = new Site(
				id: new RandomIdentifier(),
				handle: strtolower($name),
				displayName: $name,
				baseUrl: $url,
				publicKey: "key-for-$name"
			);
	}

	/**
	 * @When :user creates a Note with the text :text
	 */
	public function createNote($user, $text)
	{
			throw new PendingException();
	}

	/**
	 * @Then a(n) :event event is dispatched
	 */
	public function aNoteCreatedEventIsDispatchedWithTheContent()
	{
			throw new PendingException();
	}

	/**
	 * @Then the content is stored in the ContentStateRepo
	 */
	public function theContentIsStoredInTheContentstaterepo()
	{
			throw new PendingException();
	}
}
