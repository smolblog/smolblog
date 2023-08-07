<?php

namespace Smolblog\Core\Content\Media;

use Exception;
use Psr\Http\Message\UploadedFileInterface;
use Smolblog\Framework\Exceptions\SmolblogException;

/**
 * Indicates that a paricular piece of media could not be handled.
 */
class InvalidMediaException extends Exception implements SmolblogException {
	/**
	 * Construct the Exception
	 *
	 * @param string                     $message  Message to give. Required.
	 * @param string|null                $url      URL of failed sideload if applicable.
	 * @param UploadedFileInterface|null $file     Info for failed upload if applicable.
	 * @param mixed                      ...$props Remaining Exception props.
	 */
	public function __construct(
		string $message,
		public readonly ?string $url = null,
		public readonly ?UploadedFileInterface $file = null,
		mixed ...$props,
	) {
		parent::__construct($message, ...$props);
	}
}
