<?php

namespace Smolblog\Core\Importer;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Post\PostReader;

final class RemoveAlreadyImportedTest extends TestCase {
	public function testItRemovesImportedPostsFromTheArray() {
		$post1id = 'test|001';
		$post2id = 'test|002';
		$post3id = 'test|003';
		$post4id = 'test|004';
		$post5id = 'test|005';

		$post1 = new ImportablePost(importKey: $post1id, postData: []);
		$post2 = new ImportablePost(importKey: $post2id, postData: []);
		$post3 = new ImportablePost(importKey: $post3id, postData: []);
		$post4 = new ImportablePost(importKey: $post4id, postData: []);
		$post5 = new ImportablePost(importKey: $post5id, postData: []);

		$fullList = [$post1, $post2, $post3, $post4, $post5];
		$newList = [$post1, $post3, $post5];

		$found = [$post2id, $post4id];
		$postReader = $this->createStub(PostReader::class);
		$postReader->method('checkImportIds')->willReturn($found);

		$results = (new RemoveAlreadyImported(postReader: $postReader))(posts: $fullList);
		$this->assertEquals($newList, $results);
	}
}
