<?php

namespace Smolblog\Core\Content\Types\Article;

use Smolblog\Core\Content\Entities\ContentType;
use Smolblog\Foundation\Value\Fields\Markdown;
use Smolblog\Core\Test\ContentTypeTest;

final class ArticleTest extends ContentTypeTest {
	const string TYPE_KEY = 'article';
	const string SERVICE_CLASS = ArticleService::class;
	const string TYPE_CLASS = Article::class;

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
