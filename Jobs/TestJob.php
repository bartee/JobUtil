<?php

/**
* 
*/
class TestJob extends CronJob
{
	
	protected $time = '*/3 * * * * *';

	protected function handle(){
		$this->log('Here comes the magic!');
	}
}