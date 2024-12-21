<?php

namespace Smolblog\Foundation\Value\Fields;

use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\Field;
use Smolblog\Foundation\Value\Traits\FieldKit;

/**
 * Field to store a valid email address.
 *
 * Not stored in any special format, just validated on creation.
 */
readonly class Email extends Value implements Field {
	use FieldKit;

	/**
	 * Create the field.
	 *
	 * @throws InvalidValueProperties When $email is not a valid email.
	 *
	 * @param string $email Email address to save.
	 */
	public function __construct(public string $email) {
		if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
			throw new InvalidValueProperties("{$this->email} is not a valid email address.");
		}
	}

	/**
	 * Get the string value of the email.
	 *
	 * @return string
	 */
	public function toString(): string {
		return $this->email;
	}

	/**
	 * Create an email address from a string.
	 *
	 * @param string $string Valid email address.
	 * @return static
	 */
	public static function fromString(string $string): static {
		return new self($string);
	}
}
