        <footer class="footer">
            <div class="container-fluid">
                <nav class="pull-left">
                   	<?php wp_nav_menu(
		                array(
		                    'container' => 0,
		                    'menu_class' => 'nav navbar-nav navbar',
		                    'fallback_cb' => '',
		                    'menu' => 176,
		                    'walker' => new wp_bootstrap_navwalker()
		                )
		            ); ?>
                </nav>
                <div class="copyright pull-right">
                    © <script>document.write(new Date().getFullYear())</script>2017, made with <i class="fa fa-heart heart"></i> by <a href="http://www.creative-tim.com">Creative Tim</a>
                </div>
            </div>
        </footer>

    </div>
</div>
<?php wp_footer(); ?>

</body>
</html>