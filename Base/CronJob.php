<?php
/**
 * Abstract CronJob
 * - Locks the job when starting to run
 * - can automatically log to a file
 * - Renames the lock-file to the logfile, if possible
 *
 * Requirements
 * - PHP 5.3.0
 * - Defined LOCKDIR, LOGDIR
 * - Both dirs have to be writable
 *
 * @todo find out if it's costlier to keep the file pointer open or to re-open it every time.
 * @author Bart Stroeken
 * @package Cronjob
*/
abstract class CronJob
{

	/*
	 * Cron formatted time of running the job. Calculated from start-run
	*/
	protected $time = false;

	/**
	 * Start time, will be set automatically
	 */ 
	protected $start_time;

	/**
	 * Name of the lockfile.
	 */
	protected $lockfile;

	/**
	 * Check if the job is valid for running
	 */
	public $valid;

	/**
	 * Constructor. Verify if the whole thing can be run.
	 **/
	public function __construct() {
		if ($this->isValidTime()){
			$this->lockfile = strtolower(get_called_class()).'.lock';
			$this->valid = true;
			if (!is_dir(LOGDIR.'/'.get_called_class())){
				mkdir(LOGDIR.'/'.get_called_class());
			}
			return true;
		}
		return false;
	}

	/*
	 * Verify if the job is locked
	 */
	public final function locked(){
		$locked = file_exists(LOCKDIR . '/'. $this->lockfile);
		return $locked;
	}

	/**
	* Execute the cronjob.
	*/
	public final function execute(){
		if (!$this->valid) {
			echo 'SKIP Job '.get_called_class().': Job is INVALID';
			return true;
		}
		if ($this->locked()){
			// still running the job.
			echo 'SKIP Job '.get_called_class().': job is still running';
			return true;
		}
		$time = $this->getLastRunTime();

		if ($time){
			if (!$this->isScheduled($time)){
				echo 'SKIP Job '.get_called_class().': job is not due';
				return true;
			}
		}
		$this->start_time = mktime();
		$this->lock();
		$this->handle();
		$this->release();
	}

	/**
	 * Handle the thing
	 */
	protected function handle(){
		$this->log('The handle function of '.get_called_class(). ' was not yet defined');
	}

	/**
	 * Return the name of the class
	 */
	protected function getLogName(){
		return date('Y-m-d-H-i-s', $this->start_time).'.txt';
	}

	/**
	 * 
	 */
	protected final function lock(){
 		$pointer = fopen(LOCKDIR . '/'.  $this->lockfile, 'w');
 		$string = 'Locked job at ' . date("M d Y H:i:s")."\n\n";
 		fwrite($pointer, $string);
 		fclose($pointer);
	}

	/**
	 * Unlock the lock file, thus releasing the job for a next run.
	 */
	protected final function release(){
		$pointer = fopen(LOCKDIR . '/'. $this->lockfile, 'a');
 		$string = 'Released job at ' . date("H:i:s")."\n";
 		fwrite($pointer, $string);
 		fclose($pointer);
		rename(LOCKDIR . '/'. $this->lockfile, LOGDIR . '/' . get_called_class() . '/'.$this->getLogName());
	}

	/**
	 * Log a line to a file
	 **/
	protected function log($string){
		$pointer = fopen(LOCKDIR . '/' . $this->lockfile, 'a');
 		fwrite($pointer, date("H:i:s") . ' - ' . $string."\n");
 		fclose($pointer);
	}

	/**
	 * Validate the set cron time.
	 * 
	 * @see http://www.nncron.ru/help/EN/working/cron-format.htm
	 **/
	protected final function isValidTime(){
		if (!$this->time){
			return false;
		}

		$timestamp = explode(' ', $this->time);
		if (count($timestamp) != 6) {
			return false;
		}
		return true;
	}

	/**
	 * Retrieve the last run time of the job
	 * 
	 * @return array
	 */ 
	protected function getLastRunTime(){
		// retrieve the last run time from the most recent file in the logdir
		$files = array_diff(scandir(LOGDIR . '/' . get_called_class()), array('.','..'));
		rsort($files, SORT_STRING);
		if (count($files) == 0) {
			return false;
		}
		
		$file_name = str_replace('.txt','', $files[0]);
		$time = explode('-',$file_name);
		return mktime($time[3],$time[4],$time[5], intval($time[1]), intval($time[2]), intval($time[0]));
	}

	/**
	 * Verify if the job is due
	 * 
	 * @param array $timestamp
	 * @return boolean
	 */
	protected function isScheduled($timestamp){
		// See if it matches the schedule
			// @TODO finish this scheduling.
		return true;
	}
}