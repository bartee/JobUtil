<?php

/**
* 
*/
class TestJob extends CronJob
{
	
	protected $time = '* * * * * *';

	protected function handle(){
		$this->log('Here comes the magic!');
	}
}