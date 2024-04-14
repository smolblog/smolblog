<?php

namespace Smolblog\Core\ContentV1\Queries;

use Smolblog\Test\TestCase;
use Smolblog\Core\ContentV1\Types\Note\NoteById;
use Smolblog\Foundation\Value\Fields\Identifier;

final class QueryTest extends TestCase {
	public function testGenericContentByIdCanBeInstantiated() {
		$this->assertInstanceOf(
			GenericContentById::class,
			new GenericContentById(siteId: $this->randomId(), contentId: $this->randomId())
		);
	}

	public function testNoteByIdCanBeInstantiated() {
		$this->assertInstanceOf(
			NoteById::class,
			new NoteById(siteId: $this->randomId(), contentId: $this->randomId())
		);
	}

	public function testContentVisibleToUserCanBeInstantiated() {
		$this->assertInstanceOf(
			ContentVisibleToUser::class,
			new ContentVisibleToUser(
				siteId: $this->randomId(),
				contentId: $this->randomId(),
				userId: $this->randomId()
			)
		);
	}
}
