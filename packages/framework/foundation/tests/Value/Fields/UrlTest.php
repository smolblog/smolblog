<?php

namespace Smolblog\Foundation\Value\Fields;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use Psr\Http\Message\UriInterface;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Test\TestCase;

#[CoversClass(Url::class)]
final class UrlTest extends TestCase {
	#[TestDox('It conforms to PSR-7')]
	public function testPsr7() {
		$base = new Url('https://name:pass@test.smol.blog:8080/search?q=birb#page-2');

		$this->assertEquals('name:pass@test.smol.blog:8080', $base->getAuthority());
		$this->assertEquals('page-2', $base->getFragment());
		$this->assertEquals('test.smol.blog', $base->getHost());
		$this->assertEquals('/search', $base->getPath());
		$this->assertEquals('8080', $base->getPort());
		$this->assertEquals('q=birb', $base->getQuery());
		$this->assertEquals('https', $base->getScheme());
		$this->assertEquals('name:pass', $base->getUserInfo());

		$this->assertInstanceOf(UriInterface::class, $base->withFragment('page-4'));
		$this->assertInstanceOf(UriInterface::class, $base->withHost('search.smol.blog'));
		$this->assertInstanceOf(UriInterface::class, $base->withPath('go'));
		$this->assertInstanceOf(UriInterface::class, $base->withPort(null));
		$this->assertInstanceOf(UriInterface::class, $base->withQuery('q=456'));
		$this->assertInstanceOf(UriInterface::class, $base->withScheme('http'));
		$this->assertInstanceOf(UriInterface::class, $base->withUserInfo('snek'));
	}

	public function testItIsStringable() {
		$str = 'https://name:pass@test.smol.blog:8080/search?q=birb#page-2';
		$obj = new Url($str);

		$this->assertEquals($str, strval($obj));
		$this->assertEquals($obj->toString(), Url::fromString($str)->toString());
	}

	public function testItWillThrowAnExceptionIfTheStringIsNotAUrl() {
		$this->expectException(InvalidValueProperties::class);

		new Url('http://');
	}
}
