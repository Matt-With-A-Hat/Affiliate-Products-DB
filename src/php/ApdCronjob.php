<?php

class ApdCronjob {

	/**
	 * name of the cronjob
	 *
	 * @var
	 */
	protected $name;

	/**
	 * new interval of the cronjob
	 *
	 * @var
	 */
	protected $interval;

	/**
	 * @return mixed
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param mixed $name
	 */
	public function setName( $name ) {
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function getInterval() {
		return $this->interval;
	}

	/**
	 * @param mixed $interval
	 */
	public function setInterval( $interval ) {
		$this->interval = $interval;
	}

	public function __construct( $name, $interval ) {
		$this->setName( $name );
		$this->setInterval( $interval );
	}

	/**
	 * Creates a new cronjob of supplied name and kills the old one if it exists and the supplied interval is different from
	 * the current one.
	 *
	 * If the supplied interval equals the current interval, a new cronjob will be
	 * created, if there is no cronjob with that name yet.
	 */
	public function setCronjob() {

		$currentInterval = wp_get_schedule( $this->name );

		if ( $currentInterval != $this->interval ) {
			$timestamp = wp_next_scheduled( $this->name );
			wp_unschedule_event( $timestamp, $this->name );
			wp_schedule_event( time(), $this->interval, $this->name );

		} else if ( $currentInterval == $this->interval ) {
			if ( ! wp_next_scheduled( $this->name ) ) {
				wp_schedule_event( time(), $this->interval, $this->name );
			}

		} else if ( APD_DEBUG ) {
			$error = "Cronjob $this->name couldn't be created";
			print_error( $error, __METHOD__, __LINE__ );
		}
	}
}