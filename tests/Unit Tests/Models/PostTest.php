<?php

namespace Smolblog\Core\Models;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Model;
use Smolblog\Core\ModelHelper;
use Smolblog\Core\Definitions\PostStatus;

final class PostTestHelper implements ModelHelper {
	public function findAll(string $forModelClass, array $withProperties = []): array {
		return [];
	}

	public function getData(Model $forModel = null, array $withProperties = []): ?array {
		if (empty($withProperties)) return null;
		return $withProperties;
	}

	public function save(Model $model = null, array $withData = []): bool {
		return true;
	}
}

final class PostTest extends TestCase {
	public function testAllDefinedFieldsCanBeAccessed() {
		$model = new Post(withHelper: new PostTestHelper());

		$testData = [
			'id' => 436,
			'slug' => 'thing',
			'title' => 'thing',
			'import_key' => 'social-thing',
			'content' => 'thing',
			'date' => date(DATE_W3C),
			'status' => PostStatus::Published,
			'user_id' => 5,
			'media' => ['thing' => 'one'],
			'tags' => ['thing'],
			'reblog' => 'https://smol.blog/thing',
			'meta' => ['thing' => 'one'],
		];

		foreach ($testData as $field => $value) {
			$model->$field = $value;
		}
		foreach ($testData as $field => $value) {
			$this->assertEquals($model->$field, $value);
		}
	}
}
