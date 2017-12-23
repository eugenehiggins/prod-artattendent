<?php
class FES_User_Login_Field extends FES_Field {
	/** @var string Version of field */
	public $version = '1.0.0';

	/** @var bool For 3rd parameter of get_post/user_meta */
	public $single = true;

	/** @var array Supports are things that are the same for all fields of a field type. Like whether or not a field type supports jQuery Phoenix. Stored in obj, not db. */
	public $supports = array(
		'multiple'    => false,
		'is_meta'     => false,  // in object as public (bool) $meta;
		'forms'       => array(
			'registration'   => true,
			'submission'     => false,
			'vendor-contact' => false,
			'profile'        => false,
			'login'          => true,
		),
		'position'    => 'specific',
		'permissions' => array(
			'can_remove_from_formbuilder' => true,
			'can_change_meta_key'         => false,
			'can_add_to_formbuilder'      => true,
		),
		'template'    => 'user_login',
		'title'       => 'Username',
		'phoenix'     => false,
	);

	/** @var array Characteristics are things that can change from field to field of the same field type. Like the placeholder between two email fields. Stored in db. */
	public $characteristics = array(
		'name'             => 'user_login',
		'template'         => 'user_login',
		'public'           => true,
		'required'         => true,
		'label'            => '',
		'show_placeholder' => false,
		'default'          => false,
	);

	public function set_title() {
		$this->supports['title'] = apply_filters( 'fes_' . $this->name() . '_field_title', _x( 'Username', 'FES Field title translation', 'edd_fes' ) );
	}

	public function extending_constructor( ) {
		// exclude from profile form in admin
		add_filter( 'fes_templates_to_exclude_render_profile_form_admin', array( $this, 'exclude_field' ), 10, 1  );
		add_filter( 'fes_templates_to_exclude_validate_profile_form_admin', array( $this, 'exclude_field' ), 10, 1  );
		add_filter( 'fes_templates_to_exclude_save_profile_form_admin', array( $this, 'exclude_field' ), 10, 1  );

		// exclude from registration form in admin
		add_filter( 'fes_templates_to_exclude_render_registration_form_admin', array( $this, 'exclude_field' ), 10, 1  );
		add_filter( 'fes_templates_to_exclude_validate_registration_form_admin', array( $this, 'exclude_field' ), 10, 1  );
		add_filter( 'fes_templates_to_exclude_save_registration_form_admin', array( $this, 'exclude_field' ), 10, 1  );
	}

	public function exclude_field( $fields ) {
		array_push( $fields, 'user_login' );
		return $fields;
	}

	/** Returns the User_Login to render a field in frontend */
	public function render_field_frontend( $user_id = -2, $readonly = -2 ) {
		if ( $user_id === -2 ) {
			$user_id = get_current_user_id();
		}

		if ( $readonly === -2 ) {
			$readonly = $this->readonly;
		}
		$user_id   = apply_filters( 'fes_render_user_login_user_id_frontend', $user_id, $this->id );
		$readonly  = apply_filters( 'fes_render_user_login_readonly_frontend', $readonly, $user_id, $this->id );
		$value     = $this->get_field_value_frontend( $this->save_id, $user_id, $readonly );
		$required  = $this->required( $readonly );

		if ( $this->type == 'profile' ) {
			$readonly = true;
		}

		$output        = '';
		$output     .= sprintf( '<div class="fes-el %1s %2s %3s">', $this->template(), $this->name(), $this->css() );
		$output        .= $this->label( $readonly );

		ob_start(); ?>
		<div class="fes-fields">
			<input class="textfield<?php echo $this->required_class( $readonly ); ?>" id="<?php echo $this->form_name . '-' . $this->name(); ?>" type="text" data-required="<?php echo $required; ?>" data-type="text"<?php $this->required_html5( $readonly ); ?> name="<?php echo esc_attr( $this->name() ); ?>" placeholder="<?php echo esc_attr( $this->placeholder() ); ?>" value="<?php echo esc_attr( $value ) ?>" size="<?php echo esc_attr( $this->size() ) ?>" <?php echo $readonly ? 'disabled' : ''; ?> />
		</div>
		<?php
		$output .= ob_get_clean();
		$output .= '</div>';
		return $output;
	}

	/** Returns the User_Login to render a field for the formbuilder */
	public function render_formbuilder_field( $index = -2, $insert = false ) {
		global $post;
		$removable = $this->can_remove_from_formbuilder();
		if ( is_object( $post ) && get_the_ID() == EDD_FES()->helper->get_option( 'fes-registration-form', false )  ) {
			$removable = false;
		}
		ob_start(); ?>
		<li class="user_login">
			<?php $this->legend( $this->title(), $this->get_label(), $removable ); ?>
			<?php FES_Formbuilder_Templates::hidden_field( "[$index][template]", $this->template() ); ?>

			<?php FES_Formbuilder_Templates::field_div( $index, $this->name(), $this->characteristics, $insert ); ?>
				<?php FES_Formbuilder_Templates::public_radio( $index, $this->characteristics, $this->form_name, true ); ?>
				<?php FES_Formbuilder_Templates::standard( $index, $this ); ?>
				<?php FES_Formbuilder_Templates::common_text( $index, $this->characteristics ); ?>
			</div>
		</li>
		<?php
		return ob_get_clean();
	}

	public function sanitize( $values = array(), $save_id = -2, $user_id = -2 ) {
		$name = $this->name();
		if ( !empty( $values[ $name ] ) ) {
			$values[ $name ] = trim( $values[ $name ] );
			$values[ $name ] = sanitize_user( $values[ $name ], true ); // 2nd param: require strict mode on usernames
		}
		return apply_filters( 'fes_sanitize_' . $this->template() . '_field', $values, $name, $save_id, $user_id );
	}
}