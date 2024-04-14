<?php

namespace Smolblog\Core\ContentV1;

use DateTimeInterface;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Traits\MessageKit;
use Throwable;

/**
 * Allow an object to build a piece of content over time.
 */
trait ContentBuilderKit {
	/**
	 * Get the state of the content as of this event.
	 *
	 * If the required data has not been given, this will throw an error or exception.
	 *
	 * @throws InvalidContentException Thrown if the Content is incomplete.
	 *
	 * @return Content
	 */
	public function getContent(): Content {
		$contentProps = $this->getMetaValue('props') ?? [];
		try {
			if (!$this->getMetaValue('state')) {
				$this->setMetaValue('state', new Content(...$contentProps));
			}
			return $this->getMetaValue('state');
		} catch (Throwable $err) {
			throw new InvalidContentException(
				message: 'Called getContent() before Content was complete.',
				previous: $err
			);
		}
	}

	/**
	 * Set the type of the Content
	 *
	 * @param ContentType $type ContentType for this Content.
	 * @return void
	 */
	public function setContentType(ContentType $type): void {
		$contentProps = $this->getMetaValue('props') ?? [];
		$contentProps['type'] = $type;

		$this->setMetaValue('props', $contentProps);
		$this->setMetaValue('state', null);
	}

	/**
	 * Set an Extension on this Content.
	 *
	 * @param ContentExtension $extension Extension to add to this Content.
	 * @return void
	 */
	public function addContentExtension(ContentExtension $extension): void {
		$contentProps = $this->getMetaValue('props') ?? [];
		$contentProps['extensions'] ??= [];
		$contentProps['extensions'][get_class($extension)] = $extension;

		$this->setMetaValue('props', $contentProps);
		$this->setMetaValue('state', null);
	}

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
	): void {
		$contentProps = $this->getMetaValue('props') ?? [];
		$contentProps = array_merge($contentProps, array_filter([
			'id' => $id,
			'siteId' => $siteId,
			'authorId' => $authorId,
			'permalink' => $permalink,
			'publishTimestamp' => $publishTimestamp,
			'visibility' => $visibility,
		], fn($val) => isset($val)));

		$this->setMetaValue('props', $contentProps);
		$this->setMetaValue('state', null);
	}
}
