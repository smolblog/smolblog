<?php

namespace Smolblog\Mock\Projections;

use DateTimeInterface;
use PDO;
use Smolblog\Core\Content\Events\ContentBaseAttributeEdited;
use Smolblog\Core\Content\Events\ContentBodyEdited;
use Smolblog\Core\Content\Events\ContentCreated;
use Smolblog\Core\Content\Events\ContentDeleted;
use Smolblog\Core\Content\Events\ContentVisibilityChanged;
use Smolblog\Framework\Messages\Attributes\ExecutionLayerListener;

class StandardContentProjection {
	public function __construct(private PDO $db) {}

	#[ExecutionLayerListener(later: 5)]
	public function onContentCreated(ContentCreated $event) {
		$prepared = $this->db->prepare('INSERT INTO standard_content
		("content_id", "title", "body", "permalink", "publish_timestamp",
		 "visibility", "author_id", "site_id", "extensions") VALUES
		(:id, :title, :body, :permalink, :publishTimestamp, :visibility, :authorId, :siteId, :extensions)');

		$prepared->execute([
			'id' => $event->id->toByteString(),
			'title' => $event->getNewTitle(),
			'body' => $event->getNewBody(),
			'permalink' => $event->permalink,
			'publishTimestamp' => $event->publishTimestamp?->format(DateTimeInterface::RFC3339_EXTENDED),
			'visibility' => $event->visibility?->value ?? 'draft',
			'authorId' => $event->authorId->toByteString(),
			'siteId' => $event->siteId->toByteString(),
			'extensions' => '{}',
		]);
	}

	#[ExecutionLayerListener(later: 5)]
	public function onContentBodyEdited(ContentBodyEdited $event) {
		$prepared = $this->db->prepare('UPDATE standard_content SET title = ?, body = ? WHERE content_id = ?');
		$prepared->execute([
			$event->getNewTitle(),
			$event->getNewBody(),
			$event->contentId->toByteString(),
		]);
	}

	#[ExecutionLayerListener(later: 5)]
	public function onContentVisibilityChanged(ContentVisibilityChanged $event) {
		$prepared = $this->db->prepare('UPDATE standard_content SET visibility = ? WHERE content_id = ?');
		$prepared->execute([
			$event->visibility->value,
			$event->contentId->toByteString(),
		]);
	}

	#[ExecutionLayerListener(later: 5)]
	public function onContentDeleted(ContentDeleted $event) {
		$prepared = $this->db->prepare('DELETE FROM standard_content WHERE content_id = ?');
		$prepared->execute([$event->contentId->toByteString()]);
	}

	#[ExecutionLayerListener(later: 5)]
	public function onContentBaseAttributeEdited(ContentBaseAttributeEdited $event) {
		$changes = [];
		$query = 'UPDATE standard_content SET';

		if (isset($event->permalink)) {
			$changes['permalink'] = $event->permalink;
			$query .= ' permalink = :permalink';
		}
		if (isset($event->publishTimestamp)) {
			$changes['publishTimestamp'] = $event->publishTimestamp->format(DateTimeInterface::RFC3339_EXTENDED);
			$query .= (count($changes) > 1 ? ',' : '') . ' publish_timestamp = :publishTimestamp';
		}
		if (isset($event->authorId)) {
			$changes['authorId'] = $event->authorId->toByteString();
			$query .= (count($changes) > 1 ? ',' : '') . ' author_id = :authorId';
		}

		$query .= ' WHERE content_id = :id';
		$changes['id'] = $event->contentId->toByteString();

		$prepared = $this->db->prepare($query);
		$prepared->execute($changes);
	}
}
