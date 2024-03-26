<?php

namespace Smolblog\Framework\Foundation\Value\Fields;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Smolblog\Framework\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Framework\Foundation\Value;
use Smolblog\Framework\Foundation\Value\Traits\Field;
use Smolblog\Framework\Foundation\Value\Traits\FieldKit;
use Throwable;

/**
 * A DateTime object.
 *
 * This is a wrapper around DateTimeImmutable that provides a serializable interface.
 */
readonly class DateTimeField extends Value implements Field {
	use FieldKit;

	/**
	 * Internal DateTime object.
	 *
	 * @var DateTimeImmutable
	 */
	public DateTimeImmutable $object;

	/**
	 * Create the DateTime.
	 *
	 * @throws InvalidValueProperties Thrown if the string is not a valid DateTime.
	 *
	 * @param string            $datetime Date and time to create the DateTime from.
	 * @param DateTimeZone|null $timezone Timezone to use for the DateTime.
	 * @param DateTimeInterface $object   Pass this to create a DateTime from an existing object.
	 */
	public function __construct(
		string $datetime = 'now',
		?DateTimeZone $timezone = null,
		DateTimeInterface $object = null
	) {
		try {
			$this->object = isset($object) ?
				DateTimeImmutable::createFromInterface($object) :
				new DateTimeImmutable($datetime, $timezone);
		} catch (Throwable $e) {
			throw new InvalidValueProperties(
				message: "Could not create DateTime from string `$datetime`",
				previous: $e
			);
		}
	}

	/**
	 * Get the DateTime as a RFC3339 string.
	 *
	 * @return string
	 */
	public function toString(): string {
		return $this->object->format(DateTimeInterface::RFC3339_EXTENDED);
	}

	/**
	 * Create the DateTime from a string.
	 *
	 * @param mixed $datetime String to create DateTime from.
	 * @return self
	 */
	public static function fromString(mixed $datetime): static {
		return new self(datetime: strval($datetime));
	}
}
