<?php

namespace Smolblog\Core\Importer;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Post\PostReader;

final class RemoveAlreadyImportedTest extends TestCase {
	public function testItRemovesImportedPostsFromTheArray() {
		$post1id = 'http://servi.ce/001';
		$post2id = 'http://servi.ce/002';
		$post3id = 'http://servi.ce/003';
		$post4id = 'http://servi.ce/004';
		$post5id = 'http://servi.ce/005';

		$post1 = new ImportablePost(url: $post1id, postData: []);
		$post2 = new ImportablePost(url: $post2id, postData: []);
		$post3 = new ImportablePost(url: $post3id, postData: []);
		$post4 = new ImportablePost(url: $post4id, postData: []);
		$post5 = new ImportablePost(url: $post5id, postData: []);

		$fullList = [$post1, $post2, $post3, $post4, $post5];
		$newList = [$post1, $post3, $post5];

		$found = [$post2id, $post4id];
		$postReader = $this->createStub(PostReader::class);
		$postReader->method('checkSyndicatedUrls')->willReturn($found);

		$results = (new RemoveAlreadyImported(postReader: $postReader))->run(posts: $fullList);
		$this->assertEquals($newList, $results);
	}
}
