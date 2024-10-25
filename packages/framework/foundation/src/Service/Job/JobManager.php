<?php

namespace Smolblog\Foundation\Service\Job;

use Smolblog\Foundation\Service;
use Smolblog\Foundation\Value\Jobs\Job;

/**
 * Service to add the given Job to a queue and
 */
interface JobManager extends Service {
	/**
	 * Add the given Job to a queue to execute on a separate thread.
	 *
	 * @param Job $job Job to enqueue.
	 * @return void
	 */
	public function enqueue(Job $job): void;
}
