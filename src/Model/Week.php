<?php

namespace CommonsBooking\Model;

use CommonsBooking\Plugin;
use DateTime;
use Exception;

class Week {

	/**
	 * @var integer
	 */
	protected $year;

	/**
	 * Week of year.
	 * @var integer
	 */
	protected $dayOfYear;

	/**
	 * @var array
	 */
	protected $locations;

	/**
	 * @var array
	 */
	protected $items;

	/**
	 * @var array
	 */
	protected $types;

	/**
	 * Week constructor.
	 *
	 * @param null $year
	 * @param $dayOfYear
	 * @param array $locations
	 * @param array $items
	 * @param array $types
	 */
	public function __construct( $year, $dayOfYear, array $locations = [], array $items = [], array $types = [] ) {
		if ( $year === null ) {
			$year = date( 'Y' );
		}
		$this->year      = $year;
		$this->dayOfYear = $dayOfYear;
		$this->locations = $locations;
		$this->items     = $items;
		$this->types     = $types;
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	public function getDays() {
		$customId = md5(
			$this->year .
			$this->dayOfYear .
			serialize( $this->locations ) .
			serialize( $this->items ) .
			serialize( $this->types )
		);

		if (Plugin::getCacheItem( $customId ) ) {
			return Plugin::getCacheItem( $customId );
		} else {
			$dto = new DateTime();
			$yearTimestamp = mktime(0,0,0, 1, 1, $this->year);
			$dayOfYear = $this->dayOfYear;
			$timestamp = strtotime("+ $dayOfYear days" , $yearTimestamp);
			$dto->setTimestamp(
				$timestamp
			);

			$days = [];
			for ( $i = 0; $i < 7; $i ++ ) {
				$dayDate = $dto->format( 'Y-m-d' );
				$days[] = new Day( $dayDate, $this->locations, $this->items, $this->types );
				$dayOfWeek = $dto->format( 'w' );
				if($dayOfWeek == "0") {
					break;
				}

				$dto->modify( '+1 day' );
			}

			// set cache expiration to force daily fresh after midnight
			Plugin::setCacheItem( $days, $customId, 'midnight');

			return $days;
		}
	}

}
