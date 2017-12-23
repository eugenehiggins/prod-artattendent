<?php

/**
 * HelpScout EDD integration.
 * This class takes care of requests coming from HelpScout App Integrations
 *
 * A lot of the code below should be properly accredited to the edd-helpscout project.
 * @see https://github.com/dannyvankooten/edd-helpscout
 */
class HelpScout_EDD_App_Handler extends HelpScout_EDD_App {

	/**
	 * @var array|mixed
	 */
	private $data;

	/**
	 * @var array
	 */
	private $customer_emails = array();

	/**
	 * @var array
	 */
	private $customer_payments = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		// explicitly called
		// $this->process();
	}

	/**
	 * Process the request
	 *  - Read input
	 *  - Validate signature
	 *  - Find purchase data
	 *  - Generate response
	 *
	 * @link http://developer.helpscout.net/custom-apps/style-guide/ HelpScout Custom Apps Style Guide
	 */
	public function process() {

		// get request data
		$this->data = $this->parse_data();

		// validate request
		if ( ! $this->is_signature_valid() ) {
			$this->respond( 'Invalid signature' );
			exit;
		}

		// get customer email(s)
		$this->customer_emails = $this->get_customer_emails();

		// get customer payment(s)
		$this->customer_payments = $this->query_customer_payments();

		// build the final response HTML for HelpScout
		$html = $this->build_response_html();

		// respond with the built HTML string
		$this->respond( $html );
	}

	/**
	 * @return array|mixed
	 */
	private function parse_data() {

		$data_string = file_get_contents( 'php://input' );
		$data = json_decode( $data_string, true );

		return $data;
	}

	/**
	 * Validate the request
	 *
	 * - Validates the payload
	 * - Validates the request signature
	 *
	 * @return bool
	 */
	private function is_signature_valid() {

		// we need at least this
		if ( ! isset( $this->data['customer']['email'] ) && ! isset( $this->data['customer']['emails'] ) ) {
			return false;
		}

		if ( ! isset( $_SERVER['HTTP_X_HELPSCOUT_SIGNATURE'] ) ) {
			return false;
		}
		// check request signature
		$expected_signature = base64_encode( hash_hmac( 'sha1', json_encode( $this->data ), self::$secret_key, true ) );
		if ( $expected_signature === $_SERVER['HTTP_X_HELPSCOUT_SIGNATURE'] ) {
			return true;
		}
		return false;
	}

	/**
	 * Get an array of emails belonging to the customer
	 *
	 * @return array
	 */
	private function get_customer_emails() {

		$customer_data = $this->data['customer'];
		$emails = array();

		if ( isset( $customer_data['emails'] ) && is_array( $customer_data['emails'] ) && count( $customer_data['emails'] ) > 1 ) {
			$emails = array_values( $customer_data['emails'] );
		} elseif ( isset( $customer_data['email'] ) ) {
			$emails = array( $customer_data['email'] );
		}

		$emails = apply_filters( 'helpscout_edd_customer_emails', $emails, $this->data );

		if ( count( $emails ) === 0 ) {
			$this->respond( 'No customer email given.' );
		}

		return $emails;
	}

	/**
	 * Query all payments belonging to the customer (by email)
	 *
	 * @return array
	 */
	private function query_customer_payments() {

		// allows you to perform your own search for customer payments, based on given data.
		$payments = apply_filters( 'helpscout_edd_customer_payments', array(), $this->customer_emails, $this->data );
		if ( ! empty( $payments ) ) {
			return $payments;
		}

		global $wpdb;

		// query by email(s)
		$sql  = 'SELECT p.ID, p.post_status, p.post_date ';
		$sql .= "FROM {$wpdb->posts} p, {$wpdb->postmeta} pm ";
		$sql .= "WHERE pm.meta_key = '_edd_payment_user_email' ";

		if ( count( $this->customer_emails ) > 1 ) {
			$in_clause = rtrim( str_repeat( "'%s', ", count( $this->customer_emails ) ), ', ' );
			$sql .= "AND pm.meta_value IN( $in_clause ) ";
		} else {
			$sql .= "AND pm.meta_value = '%s' ";
		}

		$sql .= 'AND p.ID = pm.post_id GROUP BY p.ID  ORDER BY p.ID DESC';

		$query = $wpdb->prepare( $sql, $this->customer_emails );
		$results = $wpdb->get_results( $query );

		if ( is_array( $results ) ) {
			return $results;
		}

		return array();
	}

	/**
	 * Process the request
	 *  - Find purchase data
	 *  - Generate response
	 *
	 * @TODO: Refactor out loop to find additional order data.
	 *
	 * @link http://developer.helpscout.net/custom-apps/style-guide/ HelpScout Custom Apps Style Guide
	 * @return string
	 */
	private function build_response_html() {

		if ( count( $this->customer_payments ) === 0 ) {
			return apply_filters( 'hsd_edd_app_no_purchases_html', 'No purchase data found', $this );
		}

		// build array of purchases
		$orders = array();
		foreach ( $this->customer_payments as $payment ) {

			$order                   = array();
			$order['id']             = $payment->ID;
			$order['status']         = $payment->post_status;
			$order['date']           = $payment->post_date;
			$order['link']           = '<a target="_blank" href="' . admin_url( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=' . $payment->ID ) . '">#' . $payment->ID . '</a>';
			$order['amount']         = edd_get_payment_amount( $payment->ID );
			$order['payment_method'] = $this->get_payment_method( $payment->ID );
			$order['downloads']      = array();

			$downloads = edd_get_payment_meta_downloads( $payment->ID );
			if ( is_array( $downloads ) && count( $downloads ) > 0 ) {

				foreach ( $downloads as $download ) {

					$id = $download['id'];

					if ( ! $id || empty( $id ) ) {
						continue;
					}

					$download_details = '<div style="background: #fefefe;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;margin-bottom: 1em;padding: .5em .7em;">';

					// generate download string
					$download_details .= '<strong>' . get_the_title( $id ) . '</strong><br />';
					$option_name = edd_get_price_option_name( $id, $download['options']['price_id'] );
					if ( $option_name != '' ) {
						$download_details .= $option_name . '<br />';
					}

					// query license keys if order is completed and has licensing enabled
					if ( $order['status'] === 'publish' &&  get_post_meta( $download['id'], '_edd_sl_enabled', true ) && function_exists( 'edd_software_licensing' ) ) {
						$edd_sl = edd_software_licensing();

						// get license key
						$license = $edd_sl->get_license_by_purchase( $order['id'], $id );

						if ( is_object( $license ) ) {

							$license_key = get_post_meta( $license->ID, '_edd_sl_key', true );
							$license_expires = get_post_meta( $license->ID, '_edd_sl_expiration', true );
							$license_status_html = '';

							if ( $license_expires < time() ) {
								$license_status_html = ' <span style="color:orange; font-weight:bold;">expired</span>';
							}

							// add link to manage_sites for this license
							$manage_license_url = admin_url( 'edit.php?post_type=download&page=edd-licenses&s=' . $license_key );
							$download_details .= '<code style="font-size:.8em;margin-top:-1em;overflow:hidden;
text-overflow: ellipsis;"><a href="' . $manage_license_url . '">' . $license_key . '</a></code>' . $license_status_html;

							// get active sites for this license
							$sites = $edd_sl->get_sites( $license->ID );

							if ( is_array( $sites ) && count( $sites ) > 0 ) {

								// add active sites to the download HTML
								$download_details .= '<div class="toggleGroup">';
								$download_details .= '<a href="" class="toggleBtn"><i class="icon-arrow"></i> Active sites</a>';
								$download_details .= '<div class="toggle indent">';
								$download_details .= '<ul class="unstyled">';

								foreach ( $sites as $site ) {
									$args = array(
										'action'     => self::APP_AJAX_HANDLER,
										'nonce'      => wp_create_nonce( 'hs-edd-deactivate' ),
										self::APP_AJAX_HANDLER  => 'deactivate',
										'license_id' => $license->ID,
										'site_url'   => $site,
									);
									$url = $site;
									if ( ! preg_match( '~^(?:f|ht)tps?://~i', $site ) ) {
										$url = 'http://' . $site;
									}
									$download_details .= '<li><a href="' . esc_url_raw( $url ) . '" target="_blank">' . esc_html( $site ) . '</a> <a href="' . add_query_arg( $args, admin_url( 'admin-ajax.php' ) ) . '" target="_blank"><small>(deactivate)</small></a></li>';
								}

								$download_details .= '</ul>';
								$download_details .= '</div></div>';

							}
						}
					}
					$download_details .= '</div>';
					$order['downloads'][] = $download_details;
				}
			}

			$orders[] = $order;
		}

		// build HTML output
		$html = apply_filters( 'hsd_edd_app_early_html', '', $this, $orders, $downloads );
		foreach ( $orders as $order ) {

			$class = '';

			// open completed purchases by default
			if ( $order['status'] === 'publish' ) {
				$class = ' open';
			}

			$html .= '<div class="toggleGroup' . $class . '">';
			$html .= '<strong><i class="icon-cart"></i> ' . $order['link'] . '</strong> <a class="toggleBtn"><i class="icon-arrow"></i></a>';

			// show status if order wasn't completed. otherwise, show resend receipt icon.
			if ( $order['status'] !== 'publish' ) {
				$html .= '<span style="color:orange;font-weight:bold;">' . $order['status'] . '</span>';
			} else {

				// was this a renewaL?
				if ( '' !== (string) get_post_meta( $order['id'], '_edd_sl_is_renewal', true ) ) {
					$html .= '<span style="color:#008000;font-weight:bold;">renewal</span>';
				}

				// add icon to resend purchase receipt
				$args        = array(
					'action'    => self::APP_AJAX_HANDLER,
					'nonce'     => wp_create_nonce( 'hs-edd-purchase-receipt' ),
					self::APP_AJAX_HANDLER => 'purchase-receipt',
					'order'     => $order['id'],
				);
				$resend_link = '<a style="float:right;margin-top:2px" href="' . add_query_arg( $args, admin_url( 'admin-ajax.php' ) ) . '" target="_blank"><i title="' . __( 'Resend Purchase Receipt', 'edd' ) . '" class="icon-email"></i></a>';
				$html .= $resend_link;
			}

			$html .= '<div class="toggle indent">';
			$html .= '<p><span class="muted">' . $order['date'] . '</span><br/>';
			$html .= trim( edd_currency_filter( $order['amount'] ) ) . ( ( isset( $order['payment_method'] ) && '' !== $order['payment_method'] ) ?  ' - ' . $order['payment_method'] : '' ) . '</p>';

			if ( ! empty( $order['downloads'] ) && count( $order['downloads'] ) > 0 ) {
				// buid list of items with license keys
				$html .= '<ul class="unstyled">';
				foreach ( $order['downloads'] as $download ) {
					$html .= '<li>' . $download . '</li>';
				}
				$html .= '</ul>';
			}
			$html .= '</div></div>';
			$html .= '<div class="divider"></div>';
		}

		return $html;
	}

	/**
	 * Get the payment method used for the given $payment_id. Returns a link to the transaction in Stripe or PayPal if possible.
	 *
	 * @param int $payment_id
	 *
	 * @return string
	 */
	private function get_payment_method( $payment_id ) {

		$payment_method = edd_get_payment_gateway( $payment_id );

		switch ( $payment_method ) {
			case 'paypal':
				$notes = edd_get_payment_notes( $payment_id );
				foreach ( $notes as $note ) {
					if ( preg_match( '/^PayPal Transaction ID: ([^\s]+)/', $note->comment_content, $match ) ) {
						$transaction_id = $match[1];
						$payment_method = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_view-a-trans&id=' . esc_attr( $transaction_id ) . '" target="_blank">PayPal</a>';
						break;
					}
				}
				break;

			case 'stripe':
				$notes = edd_get_payment_notes( $payment_id );
				foreach ( $notes as $note ) {
					if ( preg_match( '/^Stripe Charge ID: ([^\s]+)/', $note->comment_content, $match ) ) {
						$transaction_id = $match[1];
						$payment_method = '<a href="https://dashboard.stripe.com/payments/' . esc_attr( $transaction_id ) . '" target="_blank">Stripe</a>';
						break;
					}
				}
				break;
			case 'manual_purchases':
				$payment_method = 'Manual';
				break;
		}

		return $payment_method;
	}

	/**
	 * Set JSON headers, return the given response string
	 *
	 * @param string $html
	 */
	private function respond( $html ) {
		$response = array( 'html' => $html );

		// clear output, some plugins might have thrown errors by now.
		if ( ob_get_level() > 0 ) {
			ob_end_clean();
		}

		header( 'Content-Type: application/json' );
		echo json_encode( $response );
		die();
	}

}