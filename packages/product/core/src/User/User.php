<?php

namespace Smolblog\Core\User;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\Entity;
use Smolblog\Foundation\Value\Traits\EntityKit;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Represents a User in the system.
 */
readonly class User extends Value implements Entity {
	use EntityKit;
	public const INTERNAL_SYSTEM_USER_ID = '4cf81e87-02ae-492c-9458-eef01a968d45';

	/**
	 * Get the Smolblog Internal System (smolbot) account.
	 *
	 * This User object is meant to represent actions initiated by the system. For example, when a Connection is
	 * automatically refreshed, it happens when the Connection is accessed by a query. But because the user did not
	 * initiate the action to refresh the Connection, it is considered taken by the system.
	 *
	 * @return User
	 */
	public static function internalSystemUser(): User {
		return new User(
			id: Identifier::fromString(self::INTERNAL_SYSTEM_USER_ID),
			handle: 'smolbot',
			displayName: 'Smolblog Internal System',
			pronouns: 'it/its',
			email: 'system@smolblog.org',
		);
	}

	/**
	 * Unique, all-lowercase text identifier (a.k.a. a "username").
	 *
	 * @var string
	 */
	public readonly string $handle;

	/**
	 * Preferred displayed name for the user.
	 *
	 * @var string
	 */
	public readonly string $displayName;

	/**
	 * User's email address.
	 *
	 * @var string
	 */
	public readonly string $email;

	/**
	 * Preferred pronouns for the user.
	 *
	 * @var string
	 */
	public readonly string $pronouns;

	/**
	 * Construct the entity
	 *
	 * @param Identifier $id          Unique identifier for the user.
	 * @param string     $handle      Unique, all-lowercase text identifier (a.k.a. a "username").
	 * @param string     $displayName Preferred displayed name for the user.
	 * @param string     $pronouns    Preferred pronouns for the user.
	 * @param string     $email       User's email address.
	 * @param string[]   $features    Account-level features enabled for the user.
	 */
	public function __construct(
		Identifier $id,
		string $handle,
		string $displayName,
		string $pronouns,
		string $email,
		public readonly array $features = []
	) {
		$this->handle = $handle;
		$this->displayName = $displayName;
		$this->pronouns = $pronouns;
		$this->email = $email;
		$this->id = $id;
	}
}
