<?php
/**
 * Base class for our endpoints.
 *
 * @package   artattendant\artattendant_api\
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace artattendant\artattendant_api\routes;


abstract class endpoints extends \WP_REST_Posts_Controller {
	/**
	 * @param string $post_type Name of post type this route is for.
	 * @param string $base Base URL for this API.
	 */
	public function __construct( $post_type, $base, $post_status ) {
		$this->post_type = $post_type;
		$this->base      = $base;
		$this->post_status = $post_status;
	}

	/**
	 * Create WP_Query args from request
	 *
	 * @since 0.0.1
	 *
	 * @access protected
	 *
	 * @param array $params $params from request
	 *
	 * @return array
	 */
	protected function query_args( $params ) {

		$per_page = $params['per_page'];
		$user_id = $params['user'];

		$args = array(
			'posts_per_page' => $per_page,
			'author'         => $user_id,
			'paged'          => $params[ 'page' ],
			'post_type'      => $this->post_type,
			'orderby'        => 'meta_value_num',
			'meta_key'       => 'order',
			'post_status'      => $this->post_status,
		);

		if ( isset( $params[ 'soon' ] ) && 1 == $params[ 'soon' ] ) {
			$args[ 'meta_key' ] = 'edd_coming_soon';
			$args[ 'meta_value' ] = true;
		}

		return $args;

	}

	/**
	 * Query for products and artworks and create response
	 *
	 * @since 0.0.1
	 *
	 * @access protected
	 *
	 * @param \WP_REST_Request $request Full details about the request
	 * @param array $args WP_Query args.
	 * @param bool $respond. Optional. Whether to create a response, the default, or just return the data.
	 *
	 * @return \WP_HTTP_Response
	 */
	protected function do_query( $request, $args, $respond = true) {
		$posts_query  = new \WP_Query();
		$args[ 'post_type' ] = $this->post_type;
		$args[ 'post_status' ] = array('publish', 'pending', 'draft', 'private' , 'archive' );//$this->post_status; //anagram / geet edited for all status
		//$args[ 'posts_per_page' ] = $args->posts_per_page; //anagram / geet edited for all status
		//$args[ 'author' ] = get_current_user_id();//$this->post_status //anagram / geet edited for all status

		$query_result = $posts_query->query( $args );

		$data = array();
		if ( ! empty( $query_result ) ) {
			foreach ( $query_result as $post ) {
				$data = $this->make_data( $post, $data );
			}
		}

		if ( $respond ) {
			return $this->create_response( $request, $args, $data );
		} else {
			return $data;
		}

	}

	/**
	 * Create the response.
	 *
	 * @since 0.0.1
	 *
	 * @access protected
	 *
	 * @param \WP_REST_Request $request Full details about the request
	 * @param array $args WP_Query Args
	 * @param array $data Raw response data
	 *
	 * @return \WP_Error|\WP_HTTP_ResponseInterface|\WP_REST_Response
	 */
	protected function create_response( $request, $args, $data ) {
		$response    = rest_ensure_response( $data );
		$count_query = new \WP_Query();
		unset( $args['paged'] );
		$query_result = $count_query->query( $args );
		$total_posts  = $count_query->found_posts;
		$response->header( 'X-WP-Total', (int) $total_posts );
		if( 0 == absint( $request[ 'per_page' ] ) ){
			$max_pages = 1;
		}else{
			$max_pages = ceil( $total_posts / $request[ 'per_page' ] );
		}

		$response->header( 'X-WP-TotalPages', (int) $max_pages );


		if ( $request['page'] > 1 ) {
			$prev_page = $request['page'] - 1;
			if ( $prev_page > $max_pages ) {
				$prev_page = $max_pages;
			}
			$prev_link = add_query_arg( 'page', $prev_page, rest_url( $this->base ) );
			$response->link_header( 'prev', $prev_link );
		}

		if ( $max_pages > $request['page'] ) {
			$next_page = $request['page'] + 1;
			$next_link = add_query_arg( 'page', $next_page, rest_url( $this->base ) );
			$response->link_header( 'next', $next_link );
		}

		return $response;

	}

}
