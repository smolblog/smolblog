<?php

namespace Smolblog\Foundation\v2\Fields;

use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\v2\Validation\Validated;
use Smolblog\Foundation\v2\Value;
use Smolblog\Foundation\v2\Value\CloneKit;

/**
 * Field to store a valid email address.
 *
 * Not stored in any special format, just validated on creation.
 */
readonly class Email implements Value, Field, Validated {
	use CloneKit;

	/**
	 * @param string $email Email address to save.
	 */
	public function __construct(public string $email) {
	}

	/**
	 * Validate the field.
	 *
	 * Uses PHP's FILTER_VALIDATE_EMAIL.
	 *
	 * @throws InvalidValueProperties When $email is not a valid email.
	 *
	 * @return void
	 */
	public function validate(): void {
		if (!\filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
			throw new InvalidValueProperties("{$this->email} is not a valid email address.");
		}
	}

	/**
	 * Get the string value of the email.
	 *
	 * @return string
	 */
	public function __toString(): string {
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
