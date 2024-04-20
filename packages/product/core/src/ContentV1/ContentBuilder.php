<?php

namespace Smolblog\Core\ContentV1;

use DateTimeInterface;
use Smolblog\Framework\Objects\Identifier;

/**
 * An object (usually a message) that can build a Content object over time.
 *
 * See the ContentBuilderKit for an easy implementation.
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
interface ContentBuilder {
	/**
	 * Get the state of the content as of this event.
	 *
	 * If the required data has not been given, this will throw an error or exception.
	 *
	 * @throws InvalidContentException Thrown if the Content is incomplete.
	 *
	 * @return Content
	 */
	public function getContent(): Content;

	/**
	 * Get the ID of the content in question.
	 *
	 * @return Identifier
	 */
	public function getContentId(): Identifier;

	/**
	 * Set the type of the Content
	 *
	 * @param ContentType $type ContentType for this Content.
	 * @return void
	 */
	public function setContentType(ContentType $type): void;

	/**
	 * Set an Extension on this Content.
	 *
	 * @param ContentExtension $extension Extension to add to this Content.
	 * @return void
	 */
	public function addContentExtension(ContentExtension $extension): void;

	/**
	 * Set one or more base properties on the Content.
	 *
	 * @param Identifier|null        $id               ID of this content.
	 * @param Identifier|null        $siteId           ID of the site this content belongs to.
	 * @param Identifier|null        $authorId         ID of the user that authored/owns this content.
	 * @param string|null            $permalink        Relative URL for this content.
	 * @param DateTimeInterface|null $publishTimestamp Date and time this content was first published.
	 * @param ContentVisibility|null $visibility       Visiblity of the content.
	 * @return void
	 */
	public function setContentProperty(
		?Identifier $id = null,
		?Identifier $siteId = null,
		?Identifier $authorId = null,
		?string $permalink = null,
		?DateTimeInterface $publishTimestamp = null,
		?ContentVisibility $visibility = null,
	): void;
}
