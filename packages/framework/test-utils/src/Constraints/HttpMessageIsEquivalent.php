<?php

namespace Smolblog\Test\Constraints;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\InvalidArgumentException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use SebastianBergmann\Comparator\ComparisonFailure;

class HttpMessageIsEquivalent extends Constraint {
	public function __construct(private RequestInterface|ResponseInterface $expected) {}

	public function toString(): string { return 'two HTTP messages are equivalent'; }
	protected function failureDescription($other): string { return $this->toString(); }

	private function makeArray(RequestInterface|ResponseInterface $message): array {
		$data = ['type' => ''];

		foreach($message->getHeaders() as $key => $value) {
			$lowerKey = strtolower($key);
			$data[$lowerKey] = $value;
		}

		$bodyString = $message->getBody()->__toString();
		$data['body'] = empty($bodyString) ? null : $bodyString;

		if (is_a($message, RequestInterface::class)) {
			$data['type'] .= 'Request';
			$data['url'] = $message->getUri()->__toString();
			$data['method'] = $message->getMethod();
		}

		if (is_a($message, ResponseInterface::class)) {
			$data['type'] .= 'Response';
			$data['code'] = $message->getStatusCode();
		}

		return $data;
	}

	protected function matches($other): bool {
		if (!is_a($other, MessageInterface::class)) {
			throw new InvalidArgumentException('Object is not an HTTP Message.');
		}

		$expectedData = $this->makeArray($this->expected);
		$actualData = $this->makeArray($other);

		return $expectedData == $actualData;
	}

	protected function fail($other, $description, ?ComparisonFailure $comparisonFailure = null): void
	{
		if ($comparisonFailure === null) {
			$expectedData = $this->makeArray($this->expected);
			$actualData = $this->makeArray($other);

			$comparisonFailure = new ComparisonFailure(
				$this->expected,
				$other,
				$this->exporter()->export($expectedData),
				$this->exporter()->export($actualData),
				false,
				'Failed asserting that two HTTP messages are equivalent.'
			);
		}

		parent::fail($other, $description, $comparisonFailure);
	}
}
