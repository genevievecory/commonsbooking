<?php

namespace CommonsBooking\Helper;

use function get_pages;

class Wordpress {

	/**
	 * @return array
	 */
	public static function getPageListTitle(): array {
		$pages    = get_pages();
		$pagelist = [];

		if ( $pages ) {
			foreach ( $pages as $value ) {
				$pagelist[ $value->ID ] = $value->post_title;
			}
		}

		return $pagelist;
	}

	/**
	 * Flatten array and return it.
	 *
	 * @param $posts
	 *
	 * @return array|array[]|null[]|WP_Post[]
	 */
	public static function flattenWpdbResult( $posts ): array {
		return array_map( function ( $post ) {
			return get_post( $post[0] );
		}, $posts );
	}

	/**
	 * @param $dateString
	 *
	 * @return bool|false
	 */
	public static function isValidDateString($dateString): bool {
		return preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/i',$dateString) === 1;
	}

}
