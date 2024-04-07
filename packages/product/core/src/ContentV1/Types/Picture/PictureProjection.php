<?php

namespace Smolblog\Core\ContentV1\Types\Picture;

use Illuminate\Database\ConnectionInterface;
use Smolblog\Core\ContentV1\Events\ContentEvent;
use Smolblog\Core\ContentV1\Media\Media;
use Smolblog\Core\ContentV1\Types\Note\Note;
use Smolblog\Framework\Messages\Attributes\ContentBuildLayerListener;
use Smolblog\Framework\Messages\Attributes\ExecutionLayerListener;
use Smolblog\Framework\Messages\Projection;

/**
 * Store Picture-specific state.
 */
class PictureProjection implements Projection {
	public const TABLE = 'pictures';

	/**
	 * Create the Projection.
	 *
	 * @param ConnectionInterface $db Working database connection.
	 */
	public function __construct(
		private ConnectionInterface $db
	) {
	}

	/**
	 * Create a new Picture entry.
	 *
	 * @param PictureCreated $event Event to handle.
	 * @return void
	 */
	public function onPictureCreated(PictureCreated $event) {
		$this->db->table(self::TABLE)->insert([
			'content_uuid' => $event->contentId->toString(),
			'media' => json_encode($event->getMediaObjects()),
			'caption' => $event->caption,
			'media_html' => json_encode($event->getMediaHtml()),
			'caption_html' => $event->getCaptionHtml(),
		]);
	}

	/**
	 * Update the Media for a Picture
	 *
	 * @param PictureMediaEdited $event Event to handle.
	 * @return void
	 */
	#[ExecutionLayerListener(earlier: 1)]
	public function onPictureMediaEdited(PictureMediaEdited $event) {
		$this->db->table(self::TABLE)->where('content_uuid', '=', $event->contentId->toString())->update([
			'media' => json_encode($event->getMediaObjects()),
			'media_html' => json_encode($event->getMediaHtml()),
		]);

		$this->updateState($event);
	}

	/**
	 * Update the caption for a Picture
	 *
	 * @param PictureCaptionEdited $event Event to handle.
	 * @return void
	 */
	#[ExecutionLayerListener(earlier: 1)]
	public function onPictureCaptionEdited(PictureCaptionEdited $event) {
		$this->db->table(self::TABLE)->where('content_uuid', '=', $event->contentId->toString())->update([
			'caption' => $event->caption,
			'caption_html' => $event->getCaptionHtml(),
		]);

		$this->updateState($event);
	}

	/**
	 * Delete a Picture.
	 *
	 * @param PictureDeleted $event Event to handle.
	 * @return void
	 */
	public function onPictureDeleted(PictureDeleted $event) {
		$this->db->table(self::TABLE)->where('content_uuid', '=', $event->contentId->toString())->delete();
	}

	/**
	 * Add a Picture to a ContentBuilder.
	 *
	 * @param PictureBuilder $message Message to handle.
	 * @return void
	 */
	#[ContentBuildLayerListener()]
	public function buildPicture(PictureBuilder $message) {
		$row = $this->db->table(self::TABLE)->where('content_uuid', '=', $message->getContentId()->toString())->first();

		$message->setContentType(new Picture(
			media: array_map(fn($m) => Media::fromArray($m), json_decode($row->media, true)),
			caption: $row->caption,
			mediaHtml: json_decode($row->media_html),
			captionHtml: $row->caption_html,
		));
	}

	/**
	 * Update event state with the latest.
	 *
	 * @param ContentEvent $event Event to update.
	 * @return void
	 */
	private function updateState(ContentEvent $event): void {
		$row = $this->db->table(self::TABLE)->where('content_uuid', '=', $event->contentId->toString())->first();

		if (method_exists($event, 'setTitle')) {
			$event->setTitle(
				isset($row->caption) ? Note::truncateText($row->caption) : json_decode($row->media)[0]->title
			);
		}
		if (method_exists($event, 'setCaptionHtml')) {
			$event->setCaptionHtml($row->caption_html);
		}
		if (method_exists($event, 'setAllMediaHtml')) {
			$event->setAllMediaHtml(join("\n\n", json_decode($row->media_html)));
		}
	}
}
