<?php

/**
* Cronjob manager
*/
class CronjobManager
{
	
	private $stack;

	/**
	 * Constructor
	 **/
	public function __construct()
	{
		$jobs = array_diff(scandir(JOB_DIR), array('.','..'));
		$this->stack = array();

		foreach ($jobs as $job){
			require_once(JOB_DIR.'/'.$job);
			// Test the job. If it is valid, run it.
		}
	}
}