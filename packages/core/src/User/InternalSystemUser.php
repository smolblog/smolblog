<?php

namespace Smolblog\Core\User;

use Cavatappi\Foundation\Factories\UuidFactory;

/**
 * A user object to represent actions taken by the core system.
 */
class InternalSystemUser {
	public const ID = '4cf81e87-02ae-492c-9458-eef01a968d45';

	/**
	 * User object to represent system actions not tied to a particular user.
	 *
	 * @var User|null
	 */
	private static ?User $smolbot = null;

	/**
	 * Get the Smolblog Internal System (smolbot) account.
	 *
	 * This User object is meant to represent actions initiated by the system. For example, when a Connection is
	 * automatically refreshed, it happens when the Connection is accessed by a query. But because the user did not
	 * initiate the action to refresh the Connection, it is considered taken by the system.
	 *
	 * @return User
	 */
	public static function object(): User {
		self::$smolbot ??= new User(
			id: UuidFactory::fromString(self::ID),
			key: 'smolbot',
			displayName: 'Smolblog Internal System',
			handler: self::class,
		);
		return self::$smolbot;
	}
}
