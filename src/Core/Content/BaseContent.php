<?php

namespace Smolblog\Core\Content;

use DateTimeInterface;
use Smolblog\Framework\Objects\Entity;
use Smolblog\Framework\Objects\Identifier;

/**
 * Base class for content.
 *
 * This is the foundation for all pieces of content in the system. There are two hooks available:
 *
 * 1) Content types can store and handle their data as they see fit. The system can interact with that data
 *    through the getTitle and getHtmlContent functions.
 * 2) Content extensions can attach extra data to the content through the attachExtension function.
 *
 * Remember, the canonical store for all data is the event stream! The BaseContent class is intented to provide a
 * view into the data, but there may be other data accessable in other ways.
 */
abstract class BaseContent extends Entity {
	/**
	 * Relative URL for this content.
	 *
	 * @var string
	 */
	public readonly string $permalink;

	/**
	 * Date and time this content was first published.
	 *
	 * @var DateTimeInterface
	 */
	public readonly DateTimeInterface $timestamp;

	/**
	 * Visiblity of the content.
	 *
	 * @var ContentVisibility
	 */
	public readonly ContentVisibility $visibility;

	/**
	 * ID of the site this content belongs to.
	 *
	 * @var Identifier
	 */
	public readonly Identifier $siteId;

	/**
	 * ID of the user that authored/owns this content.
	 *
	 * @var Identifier
	 */
	public readonly Identifier $userId;

	/**
	 * Extensions attached to this content.
	 *
	 * @var array
	 */
	private array $extensions;

	/**
	 * Get the title of the content.
	 *
	 * For use in the title tag, the list of content, and other places.
	 *
	 * @return string
	 */
	abstract public function getTitle(): string;

	/**
	 * Get the HTML-formatted content body.
	 *
	 * @return string
	 */
	abstract public function getBodyContent(): string;

	/**
	 * Add information from a ContentExtension
	 *
	 * @param ContentExtension $extension Data to add.
	 * @return void
	 */
	public function attachExtension(ContentExtension $extension): void {
		$this->extensions[get_class($extension)] = $extension;
	}

	/**
	 * Get the given extension from this content.
	 *
	 * @param string $class Fully-qualified class name of an extension.
	 * @return ContentExtension
	 */
	public function getExtension(string $class): ContentExtension {
		if (is_array($this->extensions[$class])) {
			$this->extensions[$class] = new $class(...$this->extensions[$class]);
		}
		return $this->extensions[$class];
	}
}
