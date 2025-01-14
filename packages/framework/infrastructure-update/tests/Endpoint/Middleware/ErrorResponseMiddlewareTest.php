<?php

namespace Smolblog\Infrastructure\Endpoint\Middleware;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Smolblog\Foundation\Exceptions\{CodePathNotSupported, CommandNotAuthorized, EntityNotFound, InvalidValueProperties, ServiceNotRegistered};
use Smolblog\Foundation\Service;
use Smolblog\Foundation\Value\Fields\Url;
use Smolblog\Foundation\Value\Http\HttpResponse;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Test\Constraints\HttpMessageIsEquivalent;
use Smolblog\Test\TestCase;

final class ErrorResponseMiddlewareTest extends TestCase {
	private Stub & ServerRequestInterface $request;
	private MockObject & RequestHandlerInterface $handler;
	private MockObject & AbstractLogger $logger;
	private ErrorResponseMiddleware $service;

	protected function setUp(): void {
		$this->request = $this->createStub(ServerRequestInterface::class);
		$this->request->method('getMethod')->willReturn('POST');
		$this->request->method('getUri')->willReturn(new Url('https://smol.blog/test/method'));
		$this->request->method('getBody')->willReturn($this->createStub(StreamInterface::class));

		$this->logger = $this->getMockBuilder(AbstractLogger::class)->onlyMethods(['log'])->getMock();
		$this->handler = $this->createMock(RequestHandlerInterface::class);
		$this->service = new ErrorResponseMiddleware($this->logger);
	}

	public function testItDoesNothingIfTheHandlerDoesNotThrowAnException() {
		$response = new HttpResponse(body: $this->randomId()->toString());

		$this->handler->expects($this->once())->method('handle')->with($this->request)->willReturn($response);
		$this->logger->expects($this->never())->method('log');

		$this->assertEquals(
			$response,
			$this->service->process($this->request, $this->handler),
		);
	}

	public function testItHandlesCommandNotAuthorized() {
		$thrown = new CommandNotAuthorized(
			originalCommand: $this->createStub(Command::class),
			message: 'You need to be a super-duper admin to do that.',
		);
		$expected = new HttpResponse(code: 403, body: ['error' => 'You need to be a super-duper admin to do that.']);

		$this->handler->method('handle')->willThrowException($thrown);
		$this->logger->expects($this->once())->method('log')->with(
			LogLevel::NOTICE,
			'Unauthorized request to POST /test/method',
			$this->anything()
		);
		$response = $this->service->process($this->request, $this->handler);

		$this->assertThat($response, new HttpMessageIsEquivalent($expected));
	}

	public function testItHandlesEntityNotFound() {
		$thrown = new EntityNotFound(
			entityId: '349602f9-4e3a-49ff-ba8e-507eb693e5d4',
			entityName: 'Smolblog\\Core\\Content',
		);
		$expected = new HttpResponse(
			code: 404,
			body: ['error' => 'No Smolblog\\Core\\Content found with ID 349602f9-4e3a-49ff-ba8e-507eb693e5d4']
		);

		$this->handler->method('handle')->willThrowException($thrown);
		$this->logger->expects($this->once())->method('log')->with(
			LogLevel::INFO,
			'No result at POST /test/method',
			$this->anything()
		);
		$response = $this->service->process($this->request, $this->handler);

		$this->assertThat($response, new HttpMessageIsEquivalent($expected));
	}

	public function testItHandlesServiceNotRegistered() {
		$thrown = new ServiceNotRegistered(
			service: self::class,
			registry: parent::class,
			message: 'The service does not exist.',
		);
		$expected = new HttpResponse(code: 404, body: ['error' => 'The service does not exist.']);

		$this->handler->method('handle')->willThrowException($thrown);
		$this->logger->expects($this->once())->method('log')->with(
			LogLevel::INFO,
			'No result at POST /test/method',
			$this->anything()
		);
		$response = $this->service->process($this->request, $this->handler);

		$this->assertThat($response, new HttpMessageIsEquivalent($expected));
	}

	public function testItHandlesInvalidValueProperties() {
		$thrown = new InvalidValueProperties(
			message: 'That is not a real thing.',
		);
		$expected = new HttpResponse(code: 400, body: ['error' => 'That is not a real thing.']);

		$this->handler->method('handle')->willThrowException($thrown);
		$this->logger->expects($this->once())->method('log')->with(
			LogLevel::INFO,
			'Bad request to POST /test/method',
			$this->anything()
		);
		$response = $this->service->process($this->request, $this->handler);

		$this->assertThat($response, new HttpMessageIsEquivalent($expected));
	}

	public function testItHandlesCodePathNotSupported() {
		$thrown = new CodePathNotSupported(
			message: 'You should not see this message.',
		);
		$expected = new HttpResponse(
			code: 500,
			body: ['error' => 'There is an error in the server code; check the logs for details.']
		);

		$this->handler->method('handle')->willThrowException($thrown);
		$this->logger->expects($this->once())->method('log')->with(
			LogLevel::ERROR,
			'Coding error in system.',
			$this->anything()
		);
		$response = $this->service->process($this->request, $this->handler);

		$this->assertThat($response, new HttpMessageIsEquivalent($expected));
	}
}
