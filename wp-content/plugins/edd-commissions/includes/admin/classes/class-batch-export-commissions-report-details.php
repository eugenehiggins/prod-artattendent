<?php
/**
 * Batch Commissions Report Details Export Class.
 *
 * This class handles commissions details report export.
 *
 * @package     EDD
 * @subpackage  Admin/Reports
 * @copyright   Copyright (c) 2017, Chris Klosowski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.4
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * EDD_Commissions_Report_Export Class
 *
 * @since 3.4
 */
class EDD_Batch_Commissions_Report_Details_Export extends EDD_Batch_Export {


	/**
	 * Our export type. Used for export-type specific filters/actions.
	 *
	 * @since       3.4
	 * @access      public
	 * @var         string
	 */
	public $export_type = 'commissions_report_details';

	private $args;


	/**
	 * Set the export headers.
	 *
	 * @since       3.4
	 * @access      public
	 * @return      void
	 */
	public function headers() {
		ignore_user_abort( true );

		if ( ! edd_is_func_disabled( 'set_time_limit' ) ) {
			set_time_limit( 0 );
		}

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . apply_filters( 'edd_commissions_report_export_filename', 'edd-export-' . $this->export_type . '-' . date( 'm' ) . '-' . date( 'Y' ) ) . '.csv' );
		header( "Expires: 0" );
	}


	/**
	 * Output the CSV columns.
	 *
	 * We make use of this function to set up the header of the earnings report.
	 *
	 * @access      public
	 * @since       3.4
	 * @return      string $cols The generated CSV header row
	 */
	public function print_csv_cols() {
		$cols = array(
			__( 'ID', 'eddc' ),
			__( 'User ID', 'eddc' ),
			__( 'User', 'eddc' ),
			sprintf( __( '%s ID', 'eddc' ), edd_get_label_singular() ),
			sprintf( __( '%s Name', 'eddc' ), edd_get_label_singular() ),
			__( 'Payment ID', 'eddc' ),
			__( 'Amount', 'eddc' ),
			__( 'Currency', 'eddc' ),
			__( 'Type', 'eddc' ),
			__( 'Rate', 'eddc' ),
			__( 'Status', 'eddc' ),
			__( 'Date Created', 'eddc' ),
			__( 'Date Paid', 'eddc' ),
		);

		$col_data = implode( ',', $cols ) . "\r\n";
		$this->stash_step_data( $col_data );

		return $col_data;
	}


	/**
	 * Print the CSV rows for the current step.
	 *
	 * @since       3.4
	 * @access      public
	 * @return      mixed false|string $row_data The data for a given row
	 */
	public function print_csv_rows() {
		$row_data = '';

		$data = $this->get_data();

		if ( $data ) {
			foreach ( $data as $commission ) {
				$download = false;
				if ( ! empty( $commission->download_id ) ) {
					$download = new EDD_Download( $commission->download_id );
					$download_name = $download->get_name();
					if ( $download->has_variable_prices() ) {
						$download_name .= ' - ' . edd_get_price_option_name( $download->ID, $commission->price_id, $commission->payment_id );
					}
				}

				$user = get_userdata( $commission->user_id );

				$row_data_array = array(
					'id'            => $commission->id,
					'user_id'       => $commission->user_id,
					'user_name'     => $user->display_name,
					'download_id'   => ! empty( $download ) ? $commission->download_id : __( 'N/A', 'eddc' ),
					'download_name' => ! empty( $download ) ? $download_name : __( 'N/A', 'eddc' ),
					'payment_id'    => $commission->payment_id,
					'amount'        => $commission->amount,
					'currency'      => $commission->currency,
					'type'          => $commission->type,
					'rate'          => $commission->rate,
					'status'        => $commission->status,
					'date_created'  => $commission->date_created,
					'date_paid'     => $commission->date_paid,
				);

				$row_data .= implode( ',', $row_data_array ) . "\r\n";
			}

			$this->stash_step_data( $row_data );

			return $row_data;
		}

		return false;
	}


	/**
	 * Get the Export Data.
	 *
	 * @since       3.4
	 * @access      public
	 * @return      mixed false|array $data The data for the CSV file
	 */
	public function get_data() {
		$commissions = edd_commissions()->commissions_db->get_commissions( $this->args );

		$data = apply_filters( 'edd_export_get_data', $commissions );
		$data = apply_filters( 'edd_export_get_data_' . $this->export_type, $commissions );

		return $data;
	}


	/**
	 * Count the number of months we are dealing with.
	 *
	 * @since       3.4
	 * @access      private
	 * @return      int The calculated number of months for this CSV
	 */
	private function count() {
		$count_args = $this->args;
		$count_args['number'] = -1;
		unset( $count_args['paged'] );

		return edd_commissions()->commissions_db->count( $count_args );
	}


	/**
	 * Return the calculated completion percentage
	 *
	 * @since       3.4
	 * @access      public
	 * @return      int $percentage Percentage of batch processing complete
	 */
	public function get_percentage_complete() {
		$percentage = 100;

		$total = $this->count();

		if ( $total > 0 ) {
			$percentage = ( ( $this->step * 25 ) / $total ) * 100;
		}

		if ( $percentage > 100 ) {
			$percentage = 100;
		}

		return $percentage;
	}


	/**
	 * Set the properties specific to the commissions report.
	 *
	 * @since       3.4
	 * @access      public
	 * @param       array $request The Form Data passed into the batch processing
	 * @return      void
	 */
	public function set_properties( $request ) {
		$this->start = ( isset( $request['start_month'] ) && isset( $request['start_year'] ) ) ? sanitize_text_field( $request['start_year'] ) . '-' . sanitize_text_field( $request['start_month'] ) . '-1' : '';
		$this->end   = ( isset( $request['end_month'] ) && isset( $request['end_year'] ) ) ? sanitize_text_field( $request['end_year'] ) . '-' . sanitize_text_field( $request['end_month'] ) . '-' . cal_days_in_month( CAL_GREGORIAN, sanitize_text_field( $request['end_month'] ), sanitize_text_field( $request['end_year'] ) ) : '';
		$this->status = ( isset( $request['status'] ) ) && 'all' !== $request['status'] ? sanitize_text_field( $request['status'] ) : 'all';

		$start_date = date( 'Y-m-d', strtotime( $this->start ) );
		$end_date   = date( 'Y-m-d', strtotime( $this->end ) );

		if ( strtotime( $start_date ) > strtotime( $end_date ) ) {
			return false;
		}

		$args = array(
			'number'     => 25,
			'paged'      => $this->step,
			'query_args' => array(
				'date_query' => array(
					'after'     => $start_date,
					'before'    => $end_date,
					'inclusive' => true,
				)
			),
			'orderby' => 'id',
			'order'   => 'ASC',
		);

		if ( 'all' !== $this->status ) {
			$args['status'] = $this->status;
		} else {
			$args['status'] = array( 'paid', 'unpaid', 'revoked' );
		}

		$this->args = $args;
	}
}