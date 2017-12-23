<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$i = 0;

if( $messages->have_posts() ) {
	wp_enqueue_script( 'fep-replies-show-hide' );
	if( fep_get_option( 'block_other_users', 1 ) ){
		wp_enqueue_script( 'fep-block-unblock-script');
	}
	$hide_read = apply_filters( 'fep_filter_hide_message_initially_if_read', true );
	?>
	<div class="fep-message"><?php
		while ( $messages->have_posts() ) {
			$i++;

			$messages->the_post();
			$read_class = ( $hide_read && fep_is_read() ) ? ' fep-hide-if-js' : '';
			$content_class = array();
			$content_class[] = 'fep-message-content';
			$content_class[] = 'fep-message-content-' . get_the_ID();
			//$content_class[] = 'fep-message-content-author-' . get_the_author_meta('ID');
			$per_mgs_class = array();
			$per_mgs_class[] = 'fep-per-message';
			$per_mgs_class[] = 'fep-per-message-' . get_the_ID();
			//$per_mgs_class[] = 'fep-per-message-' . get_the_author_meta('ID');

			if( get_current_user_id() == get_the_author_meta('ID') ){
				$content_class[] = 'fep-message-content-own';
				$per_mgs_class[] = 'fep-per-message-own';
			}
			if( fep_is_user_admin( get_the_author_meta('ID') ) ){
				$content_class[] = 'fep-message-content-admin';
				$per_mgs_class[] = 'fep-per-message-admin';
			}
			if( $hide_read && fep_is_read() ){
				$content_class[] = 'fep-hide-if-js';
				//$per_mgs_class[] = 'fep-hide-if-js';
			}


			fep_make_read();
			fep_make_read( true ); ?>

				<?php if( $i === 1 ){

					$participants = fep_get_participants( get_the_ID() );
					$par = array();
					foreach( $participants as $participant ) {

						if( get_current_user_id() != $participant && fep_get_option( 'block_other_users', 1 ) ){
							$block_unblock_text = fep_is_user_blocked_for_user( get_current_user_id(), $participant ) ? __("Unblock", "front-end-pm") : __("Block", "front-end-pm");
							$par[] = fep_get_userdata( $participant, 'display_name', 'id' ) . '(<a href="#" class="fep_block_unblock_user" data-user_id="' . $participant . '">'. $block_unblock_text . '</a>)';
						} else {
							$par[] = fep_get_userdata( $participant, 'display_name', 'id' );
						}
					} ?>
				<div class="fep-per-message fep-per-message-top fep-per-message-<?php the_ID(); ?>">
					<div class="fep-message-title-heading  subject-title"><?php the_title(); ?></div>
					<?php $artwork_id = get_post_meta( get_the_ID(), '_artwork_id', true );
							if($artwork_id){ ?>
								<span class="label label-default label-feature">Artwork Inquiry</span>
								<?php }; ?>

					<div class="fep-message-title-heading participants"> <?php _e("From", 'front-end-pm'); ?> <?php echo str_replace( 'artAttendant Team, ', '', apply_filters( 'fep_filter_display_participants', implode( ', ', $par ), $par )); ?></div>
					<div class="fep-message-toggle-all fep-align-right"><?php _e("Toggle Messages", 'front-end-pm'); ?></div>
				</div>
				<?php } ?>

				<?php //Start anagram custom
					$artwork_id = get_post_meta( get_the_ID(), '_artwork_id', true );
					if($artwork_id ){
								$artwork_link = get_the_permalink( $artwork_id);
								$artwork_img = anagram_resize_image(array('width'=>260, 'crop'=>false, 'image_id'=> get_post_thumbnail_id( $artwork_id ), 'url'=> true ));
								$artwork_title = get_the_title( $artwork_id ); ?>


						<div class="panel panel-default">
							<div class="panel-body">
								<div class="col-sm-3">
									<a href="<?php echo $artwork_link;  ?>" target="_blank"><img class="" src="<?php echo $artwork_img; ?>" ></a>
								</div>
								<div class="col-sm-6">
									<?php echo anagram_get_public_artwork_info( $artwork_id ); ?>
								</div>
								<div class="col-sm-3">
								<h4 class="upper">Owner Info</h4>
									<?php $owner_id = anagram_user_details( $artwork_id); ?>
									<?php the_author_meta( 'display_name', $owner_id  ); ?>
									<br/>
									<a href="https:/
/artattendant.com/messages/?fepaction=newmessage&to=<?php the_author_meta( 'user_login', $owner_id  ); ?>">Message user</a>
								</div>

							</div>
						</div>
						</div>
				<?php	}; //End anagram custom ?>
			<div id="fep-message-<?php the_ID(); ?>" class="<?php echo fep_sanitize_html_class( $per_mgs_class ); ?> panel panel-default">
				<div class="fep-message-title  panel-heading  fep-message-title-<?php the_ID(); ?>">
					<span class="author"><?php the_author_meta('display_name'); ?></span>
					<span class="date pull-right"><?php the_time(); ?></span>
				</div>
				<div class="<?php echo fep_sanitize_html_class( $content_class ); ?>  panel-body">
					<?php the_content(); ?>

					<?php if( $i === 1 ){
						do_action ( 'fep_display_after_parent_message' );
					} else {
						do_action ( 'fep_display_after_reply_message' );
					} ?>
					<?php do_action ( 'fep_display_after_message', $i ); ?>
				</div>
			</div><?php
		} ?>
	</div><?php
	wp_reset_postdata();

	include( fep_locate_template( 'reply_form.php') );

} else {
	echo "<div class='fep-error'>".__("You do not have permission to view this message!", 'front-end-pm')."</div>";
}
