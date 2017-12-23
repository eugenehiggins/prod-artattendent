<?php
	redirect_login_to_collections();
	get_header('pre'); ?>
		<div class="container main-content">
		<div class="row">
			<div class="col-sm-12">
					<div class="landing-logo"><?php echo anagramLoadFile(get_template_directory_uri()."/img/artattendant-logo.svg"); ?></div>
						<div class="landing-intro-text">
							<h3>collection management <span class="red dot">•</span> made affordable <span class="red dot">•</span> made accessible</h3>
							<strong>An inventory management system that connects you to what you want</strong>

							<div class="button-block text-center" style="margin:40px 0;"><a href="<?php echo get_the_permalink(85); ?>" class="btn btn-default btn-aqua btn-lg" style="margin:0 10px;">Collection</a> <a href="<?php echo get_the_permalink(20); ?>" class="btn btn-default btn-aqua btn-lg" style="margin:0 10px;">Discover</a></div>
						</div><!-- .entry-content -->
				</div><!-- close .*-inner (main-content or sidebar, depending if sidebar is used) -->
		</div><!-- close .row -->
	</div><!-- close .container -->

	<div class="container-fluid landing-block">
		<div class="container">
			<div class="row">
				<div class="col-sm-6 textContainer">
					<h1><span class="red">artAttendant</span> is for everyone</h1>
				</div>
				<div class="col-sm-6">
					<a href="<?php echo get_the_permalink(85); ?>"><?php echo anagramLoadFile(get_template_directory_uri()."/img/artattendant-graphic.svg"); ?></a>
				</div>
			</div>
		</div>
	</div>

		<div class="container-fluid">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<div class="about-block">
					<?php while ( have_posts() ) : the_post(); ?>
							<?php the_content(); ?>



						<?php //echo do_shortcode('[gravityform id="4" title="true" description="false" ajax="true"]'); ?>

						<?php endwhile; // end of the loop. ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php get_footer('pre'); ?>

