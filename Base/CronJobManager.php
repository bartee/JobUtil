<?php

/**
* Cronjob manager
* 
* @author Bart Stroeken
* @package JobUtil
*/
class CronjobManager
{
	
	private $stack;

	/**
	 * Constructor
	 **/
	public function __construct()
	{
		$this->logfile = date('Y-m-d_H-i-s', mktime()).'_cronmanager.txt';
	}

	/**
	* Verifying available jobs
	*/
	public function run(){
		$jobfiles = array_diff(scandir(JOB_DIR), array('.','..'));
		$this->stack = array();
		$start_job_count = count($jobfiles);
		$found_jobs = 0;
		$executed_jobs = 0;
		foreach ($jobfiles as $jobfile){
			require_once(JOB_DIR.'/'.$jobfile);
			$name = str_replace('.php', '', $jobfile);
			$job = new $name;
			if (is_subclass_of($job, 'CronJob')){
				$found_jobs++;
				if ($job->canBeRun()){
					// Test the job. If it is valid, run it.
					$job_id = shell_exec('php -f runner.php '.$name);
					$this->log($job_id . ' runs '.$name);
					$executed_jobs ++; 
				}
			}
			$this->log('Stopping cronmanager. Found '.$start_job_count.' possible, '.$found_jobs .' valid jobs, and executed '.$executed_jobs.' of them');
		}
	}

	protected function log($string){
		$pointer = fopen(LOGDIR . '/' . $this->logfile, 'a');
 		fwrite($pointer, date("H:i:s") . ' - ' . $string."\n");
 		fclose($pointer);
	}
}