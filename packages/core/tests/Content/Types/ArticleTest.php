<?php

namespace Smolblog\Core\Content\Types\Article;

use Cavatappi\Foundation\Fields\Markdown;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Content\Entities\ContentType;
use Smolblog\Core\Test\ContentTypeTest;

#[AllowMockObjectsWithoutExpectations]
final class ArticleTest extends ContentTypeTest {
	public const string TYPE_KEY = 'article';
	public const string SERVICE_CLASS = ArticleService::class;
	public const string TYPE_CLASS = Article::class;

	protected function createExampleType(): ContentType {
		return new Article(title: 'This is a test', text: new Markdown('This is _only_ a test.'));
	}

	protected function createModifiedType(): ContentType {
		return new Article(title: 'This is a test', text: new Markdown('This is **only** a test.'));
	}

	public function testItUsesTheGivenTitle() {
		$this->assertEquals('This is a test', $this->createExampleType()->getTitle());
	}
}
