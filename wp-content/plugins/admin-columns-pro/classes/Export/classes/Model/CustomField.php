<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property AC_Column_CustomField $column
 * @since NEWVERSION
 */
class ACP_Export_Model_CustomField extends ACP_Export_Model_RawValue {

	public function __construct( AC_Column_CustomField $column ) {
		parent::__construct( $column );
	}

}
