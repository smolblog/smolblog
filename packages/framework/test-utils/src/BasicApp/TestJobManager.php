<?php

namespace Smolblog\Test\BasicApp;

use Psr\Container\ContainerInterface;
use Smolblog\Foundation\Service\Job\JobManager;
use Smolblog\Foundation\Value\Jobs\Job;

final class TestJobManager implements JobManager {
	/**
	 * Basic job queue.
	 *
	 * @var array
	 */
	private array $queue = [];

	public function __construct(private ContainerInterface $container) {
	}

	public function enqueue(Job $job): void {
		$this->queue[] = $job->serializeValue();
	}

	public function run(): void {
		$jobData = \array_shift($this->queue);
		if (isset($jobData)) {
			$job = Job::deserializeValue($jobData);
			\call_user_func(
				[$this->container->get($job->service), $job->method],
				...$job->getParameters()
			);
			$this->run();
		}
	}
}
