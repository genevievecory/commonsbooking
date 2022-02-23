<?php

namespace CommonsBooking\CB;

use CommonsBooking\Helper\Helper;
use CommonsBooking\Wordpress\CustomPostType\Booking;
use WP_Post;
use function get_user_by;

class CB {

	protected static $INTERNAL_DATE_FORMAT = 'd.m.Y';

	public static function getInternalDateFormat(): string {
		return static::$INTERNAL_DATE_FORMAT;
	}

	/**
	 * Returns property of (custom) post by class key and property.
	 *
	 * @param mixed $key
	 * @param mixed $property
	 * @param int|null $post
	 * @param mixed $args
	 *
	 * @return mixed
	 */
	public static function get( $key, $property, ?int $postId = null, $args = null ) {

		// first we need to check if we are dealing with a post and set the post object properly
		if ( ! $postId ) {
			$postId = self::getPostId( $key );
			$post   = get_post( $postId );
		} else {
			$post = get_post( $postId );
		}

		// If possible cast to CB Custom Post Type Model to get additional functions
		$post = Helper::castToCBCustomType($post, $key);

		$result     = self::lookUp( $key, $property, $post, $args );  // Find matching methods, properties or metadata
		$filterName = sprintf( 'cb_tag_%s_%s', $key, $property );

		return apply_filters( $filterName, $result );
	}

	/**
	 * Returns post id by class name of (custom) post.
	 *
	 * @param string $key
	 *
	 * @return int|null
	 */
	private static function getPostId( string $key ): ?int {
		$postId = null;

		// Set WP Post
		global $post;

		// we read the post object from the global post if no postID is set
		$initialPost = $post;

		// we check if we are dealing with a timeframe then get the time timeframe-object as post
		if ( isset( $_GET['cb_timeframe'] ) ) {
			$initialPost = get_page_by_path( sanitize_text_field( $_GET['cb_timeframe'] ), OBJECT, 'cb_timeframe' );
		}

		if ( ! is_null( $initialPost ) ) {
			// Check post type
			$initialPostType = get_post_type( $initialPost );

			// If we are dealing with a timeframe and key ist not booking, we may need to look up the CHILDs post meta, not the parents'
			if ( $initialPostType == 'cb_timeframe' &&
			     $key != Booking::$postType &&
			     $key != 'user'
			) {
				$subPostID = get_post_meta( $initialPost->ID, $key . '-id', true );    // item-id, location-id
				if ( get_post_status( $subPostID ) ) { // Post with that ID exists
					$postId = $subPostID; // we will query the sub post
				}
			} else { // Not a timeframe, look at original post meta
				$postId = $initialPost->ID;
			}
		}

		return $postId;
	}

	/**
	 * @param string $key
	 * @param string $property
	 * @param $post
	 * @param $args
	 *
	 * @return string|null
	 */
	public static function lookUp( string $key, string $property, $post, $args ): ?string {
		// in any case we need the post object, otherwise we cannot return anything
		if ( ! $post ) {
			return null;
		}

		if ( $key == 'user' ) {
			$result = self::getUserProperty( $post, $property, $args );
		} else {
			$result = self::getPostProperty( $post, $property, $args );
		}

		if ( $result ) {
			// sanitize output
			return commonsbooking_sanitizeHTML( $result );
		}

		return $result;
	}

	/**
	 * Tries to get a property of a post with different approaches.
	 * @param $post
	 * @param $property
	 * @param $args
	 *
	 * @return mixed|null
	 */
	private static function getPostProperty( $post, $property, $args ) {
		$result = null;

		$postId = is_int($post) ? $post : $post->ID;

		if ( get_post_meta( $postId, $property, true ) ) { // Post has meta fields
			$result = get_post_meta( $postId, $property, true );
		}

		if ( method_exists( $post, $property ) ) {
			$result = $post->$property( $args );
		}

		$prefixedProperty = 'get' . ucfirst( $property );
		if ( ! $result && method_exists( $post, $prefixedProperty ) ) {
			$result = $post->$prefixedProperty( $args );
		}

		if ( ! $result && $post->$property ) {
			$result = $post->$property;
		}

		return $result;
	}

	/**
	 * Tries to get a property of a user with different approaches.
	 *
	 * @param WP_Post $post
	 * @param string $property
	 * @param $args
	 *
	 * @return int|mixed|null
	 */
	private static function getUserProperty( WP_Post $post, string $property, $args ) {
		$result = null;

		$userID  = intval( $post->post_author );
		$cb_user = get_user_by( 'ID', $userID );

		if ( method_exists( $cb_user, $property ) ) {
			$result = $cb_user->$property( $args );
		}

		if ( ! $result && $cb_user->$property ) {
			$result = $cb_user->$property;
		}

		if ( ! $result && get_user_meta( $cb_user->ID, $property, true ) ) { // User has meta fields
			$result = get_user_meta( $cb_user->ID, $property, true );
		}

		return $result;
	}

}
