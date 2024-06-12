<?php

namespace Smolblog\Core\Content\Queries;

use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\TestCase;

final class TestAdaptableContentQuery extends AdaptableContentQuery {
	public function getSiteId(): Identifier {
		return Identifier::fromString('e60ac026-3e1a-4209-9dec-119f1ff41d61');
	}
}

final class AdaptableContentQueryTest extends TestCase {
	public function testTheContentIdAndTypeCanBeSavedAndRetrieved() {
		$query = new TestAdaptableContentQuery();

		$query->setContentInfo(id: Identifier::fromString('788a6a9c-8be9-42ab-8678-abc6f6273997'), type: 'note');
		$this->assertEquals('788a6a9c-8be9-42ab-8678-abc6f6273997', $query->getContentId()->toString());
		$this->assertEquals('note', $query->getContentType());
	}

	public function testTheQueryIsAnonymousByDefault() {
		$this->assertNull((new TestAdaptableContentQuery())->getUserId());
	}
}
