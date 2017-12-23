<?php defined('ABSPATH') or die("Cannot access pages directly."); ?>

<div class="col-sm-12 p-0 wdt-constructor-step hidden" data-step="2-3">

    <div class="card">

        <div class="card-header">
            <h2><?php _e('Preview the query that has been generated for you', 'wpdatatables'); ?></h2>
            <ul class="actions">
                <li class="wdt-constructor-refresh-wp-query">
                    <a>
                        <i class="zmdi zmdi-refresh-alt" data-toggle="tooltip" data-placement="top"
                           title="<?php _e('Click to refresh the table', 'wpdatatables'); ?>"></i>
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body" id="wdt-constructor-preview-wp-query">

        </div>

    </div>
    <!-- /.row -->

    <div class="card">

        <div class="card-header">
            <h2><?php _e('Preview the 5 first result rows', 'wpdatatables'); ?></h2>
        </div>
        <div class="card-body">
            <div class="wdt-constructor-preview-wp-table table-responsive">

            </div>
        </div>

    </div>
    <!-- /.row -->

</div>