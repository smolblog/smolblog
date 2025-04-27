<?php

namespace Smolblog\FeatureTest\Context;

use Behat\Step\Then;
use Behat\Step\When;
use Behat\Step\Given;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class ConnectionContext implements Context {
	/**
	 * Initializes context.
	 *
	 * Every scenario gets its own context instance.
	 * You can also pass arbitrary arguments to the
	 * context constructor through behat.yml.
	 */
	public function __construct() {
	}

	#[Given('I am logged in as a(n) :arg1')]
	public function iAmLoggedInAsAnAuthor(): void {
		throw new PendingException();
	}

	#[When('I create a new :arg1 connection')]
	public function iCreateANewServiceConnection(): void {
		throw new PendingException();
	}

	#[Then('I am redirected to :arg1')]
	public function iAmRedirectedToService(): void {
		throw new PendingException();
	}

	#[Then('I return to Smolblog with an OAuth code for :arg1')]
	public function iReturnToSmolblogWithAnOauthCode(): void {
		throw new PendingException();
	}

	#[Then('an :arg1 connection called :arg2 is created for me')]
	public function anServiceConnectionCalledIsCreatedForMe($arg1): void {
		throw new PendingException();
	}

	#[Then(':arg1 channels for :arg2 are created for me')]
	public function serviceChannelsForAreCreatedForMe($arg1): void {
		throw new PendingException();
	}

	#[Given('I have an :arg1 connection called :arg2')]
	public function iHaveAnServiceConnectionCalled($arg1): void {
		throw new PendingException();
	}

	#[Given('I have :arg1 channels for :arg2')]
	public function iHaveServiceChannelsFor($arg1): void {
		throw new PendingException();
	}

	#[When('I refresh the :arg1 connection called :arg2')]
	public function iRefreshTheServiceConnectionCalled($arg1): void {
		throw new PendingException();
	}

	#[Then('the :arg1 connection called :arg2 is updated')]
	public function theServiceConnectionCalledIsUpdated($arg1): void {
		throw new PendingException();
	}

	#[Then(':arg1 channels for :arg2 are updated')]
	public function serviceChannelsForAreUpdated($arg1): void {
		throw new PendingException();
	}

	#[When('I delete the :arg1 connection called :arg2')]
	public function iDeleteTheServiceConnectionCalled($arg1): void {
		throw new PendingException();
	}

	#[Then('the :arg1 connection :arg2 is deleted')]
	public function theServiceConnectionIsDeleted($arg1): void {
		throw new PendingException();
	}

	#[Then(':arg1 channels for :arg2 are deleted')]
	public function serviceChannelsForAreDeleted($arg1): void {
		throw new PendingException();
	}
}
