<?php

namespace Smolblog\Core\ContentV1\Queries;

use Smolblog\Core\ContentV1\Content;
use Smolblog\Test\TestCase;
use Smolblog\Framework\Objects\Identifier;

final class BaseContentByIdTest extends TestCase {
	public function testItIsAuthorizedByAContentVisibleToUserQuery() {
		$query = new class(
			siteId: $this->randomId(),
			contentId: $this->randomId(),
		) extends BaseContentById {};
		$auth = new ContentVisibleToUser(
			siteId: $query->siteId,
			contentId: $query->contentId,
			userId: null,
		);

		$this->assertEquals($auth, $query->getAuthorizationQuery());
	}

	public function testItCorrectlyGivesTheContentId() {
		$expected = $this->randomId();
		$this->assertEquals($expected, (new class(
			contentId: $expected,
			siteId: $this->randomId(),
		) extends BaseContentById{})->getContentId());
	}

	public function testItHandlesBuildingFoundContent() {
		$query = new class(siteId: $this->randomId(), contentId: $this->randomId()) extends BaseContentById {};

		$query->setResults($this->createStub(Content::class));
		$this->assertInstanceOf(Content::class, $query->results());
	}

	public function testItStopsWhenContentIsNotFound() {
		$query = new class(siteId: $this->randomId(), contentId: $this->randomId()) extends BaseContentById {};

		$query->setResults(null);
		$this->assertTrue($query->isPropagationStopped());
		$this->assertNull($query->results());
	}
}
