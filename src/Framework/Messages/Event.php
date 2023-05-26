<?php

namespace Smolblog\Framework\Messages;

use DateTimeImmutable;
use DateTimeInterface;
use Smolblog\Framework\Objects\DateIdentifier;
use Smolblog\Framework\Objects\EntityKit;
use Smolblog\Framework\Objects\Identifier;

/**
 * A persistable object that tells the Domain Model that a thing happened.
 *
 * Events represent a change in state: a setting change, a new piece of content, a new subscriber. The result of an
 * Event message is a change to the application's state, typically a database table. So when a user makes a change, an
 * Event is created, and the change is saved.
 *
 * The intent of saving changes this way is to persist not only the state in the database but also _the Events
 * themselves._ This can allow old data to be interpreted in new ways, making migrations more reliable. It provides a
 * comprehensive audit trail that can more easily roll back unwanted changes.
 */
abstract class Event extends Message {
	use EntityKit;

	/**
	 * Unique identifier (UUID) for this particular entity.
	 *
	 * @var Identifier
	 */
	public readonly Identifier $id;

	/**
	 * Time this Event took place.
	 *
	 * @var DateTimeInterface
	 */
	public readonly DateTimeInterface $timestamp;

	/**
	 * Create the Event.
	 *
	 * @param Identifier|null        $id        UUID for this Event. Will create from $timestamp by default.
	 * @param DateTimeInterface|null $timestamp Time of the Event. Will create from 'now' by default.
	 */
	public function __construct(
		Identifier $id = null,
		DateTimeInterface $timestamp = null,
	) {
		$this->timestamp = $timestamp ?? new DateTimeImmutable();
		$this->id = $id ?? new DateIdentifier($this->timestamp);
	}
}
