<?php defined('ABSPATH') or die("Cannot access pages directly."); ?>

<?php
/**
 * User: Miljko Milosevic
 * Date: 1/20/17
 * Time: 1:08 PM
 */
?>

<div role="tabpanel" class="tab-pane active" id="main-plugin-settings">
    <div class="row">
        <div class="col-sm-4 interface-language">
            <h4 class="c-black m-b-20">
                <?php _e('Interface language', 'wpdatatables'); ?>
                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                   title="<?php _e('Pick the language which will be used in tables interface.', 'wpdatatables'); ?>"></i>
            </h4>
            <div class="form-group">
                <div class="fg-line">
                    <div class="select">
                        <select class="selectpicker" id="wdt-interface-language">
                            <option value=""><?php _e('English (default)', 'wpdatatables'); ?></option>
                            <?php foreach (WDTSettingsController::getInterfaceLanguages() as $language) { ?>
                                <option value="<?php echo $language['file'] ?>">
                                    <?php echo $language['name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4 date-format">
            <h4 class="c-black m-b-20">
                <?php _e('Date format', 'wpdatatables'); ?>
                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                   title="<?php _e('Pick the date format to use in date column type.', 'wpdatatables'); ?>"></i>
            </h4>
            <div class="form-group">
                <div class="fg-line">
                    <div class="select">
                        <select class="selectpicker" name="wdt-date-format" id="wdt-date-format">
                            <option value="d/m/Y"> 15/07/2005 (d/m/Y)</option>
                            <option value="m/d/Y"> 07/15/2005 (m/d/Y)</option>
                            <option value="d.m.Y"> 15.07.2005 (d.m.Y)</option>
                            <option value="m.d.Y"> 07.15.2005 (m.d.Y)</option>
                            <option value="d-m-Y"> 15-07-2005 (d-m-Y)</option>
                            <option value="m-d-Y"> 07-15-2005 (m-d-Y)</option>
                            <option value="d.m.y"> 15.07.05 (d.m.y)</option>
                            <option value="m.d.y"> 07.15.05 (m.d.y)</option>
                            <option value="d-m-y"> 15-07-05 (d-m-y)</option>
                            <option value="m-d-y"> 07-15-05 (m-d-y)</option>
                            <option value="d M Y"> 15 July 2005 (d Mon Y)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4 time-format">
            <h4 class="c-black m-b-20">
                <?php _e('Time format', 'wpdatatables'); ?>
                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                   title="<?php _e('Pick the time format to use in datetime and time column type.', 'wpdatatables'); ?>"></i>
            </h4>
            <div class="form-group">
                <div class="fg-line">
                    <div class="select">
                        <select class="selectpicker" name="wdt-time-format" id="wdt-time-format">
                            <option value="h:i A">1:25 PM (12h)</option>
                            <option value="H:i">13:25 (24h)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4 wdt-parse-shortcodes">
            <h4 class="c-black m-b-20">
                <?php _e('Parse shortcodes', 'wpdatatables'); ?>
                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                   title="<?php _e('Parse shortcodes in strings', 'wpdatatables'); ?>"></i>
            </h4>
            <div class="toggle-switch" data-ts-color="blue">
                <label for="wdt-parse-shortcodes" class="ts-label">Parse shortcodes in strings</label>
                <input type="checkbox" name="wdt-parse-shortcodes" id="wdt-parse-shortcodes"/>
                <label for="wdt-parse-shortcodes" class="ts-helper"></label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4 base-skin">
            <h4 class="c-black m-b-20">
                <?php _e('Base skin', 'wpdatatables'); ?>
                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                   title="<?php _e('Choose the base skin for the plugin.', 'wpdatatables'); ?>"></i>
            </h4>
            <div class="form-group">
                <div class="fg-line">
                    <div class="select">
                        <select class="selectpicker" name="wdt-base-skin" id="wdt-base-skin">
                            <option value="skin0"><?php _e('Material', 'wpdatatables'); ?></option>
                            <option value="skin1"><?php _e('Light', 'wpdatatables'); ?></option>
                            <option value="skin2"><?php _e('Graphite', 'wpdatatables'); ?></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4 number-format">
            <h4 class="c-black m-b-20">
                <?php _e('Number format', 'wpdatatables'); ?>
                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                   title="<?php _e('Pick the number format (thousands and decimals separator)', 'wpdatatables'); ?>"></i>
            </h4>
            <div class="form-group">
                <div class="fg-line">
                    <div class="select">
                        <select class="selectpicker" id="wdt-number-format">
                            <option value="1">15.000,00</option>
                            <option value="2">15,000.00</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4 filter-position">
            <h4 class="c-black m-b-20">
                <?php _e('Render advanced filter', 'wpdatatables'); ?>
                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                   title="<?php _e('Choose where you would like to render the advanced filter for tables where enabled.', 'wpdatatables'); ?>"></i>
            </h4>
            <div class="form-group">
                <div class="fg-line">
                    <div class="select">
                        <select class="selectpicker" id="wp-render-filter">
                            <option value="header"><?php _e('In the header', 'wpdatatables'); ?></option>
                            <option value="footer"><?php _e('In the footer', 'wpdatatables'); ?></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4 decimal-places">
            <h4 class="c-black m-b-20">
                <?php _e('Decimal places', 'wpdatatables'); ?>
                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                   title="<?php _e('Define the amount of decimal places for the float numbers.', 'wpdatatables'); ?>"></i>
            </h4>
            <div class="form-group">
                <div class="fg-line">
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="number" name="wdt-decimal-places" id="wdt-decimal-places"
                                   class="form-control input-sm" min="1" value=""/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4 tablet-width">
            <h4 class="c-black m-b-20">
                <?php _e('Tablet width', 'wpdatatables'); ?>
                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                   title="<?php _e('Here you can specify width of the screen (in pixels) that will be treated as a tablet. You can set it wider if you want responsive effect on desktops.', 'wpdatatables'); ?>"></i>
            </h4>
            <div class="form-group">
                <div class="fg-line">
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="number" name="wdt-tablet-width" id="wdt-tablet-width"
                                   class="form-control input-sm" placeholder="Set tablet width in px" value=""/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4 mobile-width">
            <h4 class="c-black m-b-20">
                <?php _e('Mobile width', 'wpdatatables'); ?>
                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                   title="<?php _e('Here you can specify width (in pixels) will be treated as a mobile..', 'wpdatatables'); ?>"></i>
            </h4>
            <div class="form-group">
                <div class="fg-line">
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="number" name="wdt-mobile-width" id="wdt-mobile-width"
                                   class="form-control input-sm" placeholder="Set mobile width in px" value=""/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4 tables-on-browse">
            <h4 class="c-black m-b-20">
                <?php _e('Tables per admin page', 'wpdatatables'); ?>
                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                   title="<?php _e('How many tables to show in the browse page.', 'wpdatatables'); ?>"></i>
            </h4>
            <div class="form-group">
                <div class="fg-line">
                    <div class="select">
                        <select class="selectpicker" id="wdt-tables-per-page">
                            <?php for ($i = 10; $i <= 50; $i += 10) { ?>
                                <option value="<?php echo $i ?>"><?php echo $i ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4 align-numbers">
            <h4 class="c-black m-b-20">
                <?php _e('Align numbers', 'wpdatatables'); ?>
                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                   title="<?php _e('How "Integer" and "Float" column types will be aligned in the cell', 'wpdatatables'); ?>"></i>
            </h4>
            <div class="toggle-switch" data-ts-color="blue">
                <label for="wdt-numbers-align" class="ts-label">Align numbers to the right</label>
                <input type="checkbox" name="wdt-numbers-align" id="wdt-numbers-align" checked="checked"/>
                <label for="wdt-numbers-align" class="ts-helper"></label>
            </div>
        </div>
    </div>
    <!-- SUM and AVG label settings -->
    <div class="row">

        <div class="col-sm-4 wdt-sum-function-label-block">
            <h4 class="c-black m-b-20">
                <?php _e('Sum functions label', 'wpdatatables'); ?>
                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                   title="<?php _e('Enter a label that will be used for Sum functions. If you leave it blank default label will be Σ =', 'wpdatatables'); ?>"></i>
            </h4>
            <div class="form-group">
                <div class="fg-line">
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="text" name="wdt-sum-function-label" id="wdt-sum-function-label"
                                   class="form-control input-sm" placeholder="Enter the default SUM functions label"
                                   value=""/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-4 wdt-avg-function-label-block">
            <h4 class="c-black m-b-20">
                <?php _e('Average functions label', 'wpdatatables'); ?>
                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                   title="<?php _e('Enter a label that will be used for Average functions. If you leave it blank default label will be Avg =', 'wpdatatables'); ?>"></i>
            </h4>
            <div class="form-group">
                <div class="fg-line">
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="text" name="wdt-avg-function-label" id="wdt-avg-function-label"
                                   class="form-control input-sm" placeholder="Enter the default AVG functions label"
                                   value=""/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- /.row -->
    <!-- MIN and MAX label settings -->
    <div class="row">

        <div class="col-sm-4 wdt-min-function-label-block">
            <h4 class="c-black m-b-20">
                <?php _e('Minimum functions label', 'wpdatatables'); ?>
                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                   title="<?php _e('Enter a label that will be used for Minimum functions. If you leave it blank default label will be Min =', 'wpdatatables'); ?>"></i>
            </h4>
            <div class="form-group">
                <div class="fg-line">
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="text" name="wdt-min-function-label" id="wdt-min-function-label"
                                   class="form-control input-sm" placeholder="Enter the default MIN functions label"
                                   value=""/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-4 wdt-max-function-label-block">
            <h4 class="c-black m-b-20">
                <?php _e('Maximum functions label', 'wpdatatables'); ?>
                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                   title="<?php _e('Enter a label that will be used for Maximum functions. If you leave it blank default label will be Max =', 'wpdatatables'); ?>"></i>
            </h4>
            <div class="form-group">
                <div class="fg-line">
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="text" name="wdt-max-function-label" id="wdt-max-function-label"
                                   class="form-control input-sm" placeholder="Enter the default MAX functions label"
                                   value=""/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-sm-4 purchase-code">
            <h4 class="c-black m-b-20">
                <?php _e('Purchase code', 'wpdatatables'); ?>
                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                   title="<?php _e('Please enter your Envato purchase code to enable plugin auto-updates. Leave blank if you do not want the plugin to auto-update.', 'wpdatatables'); ?>"></i>
            </h4>
            <div class="form-group">
                <div class="fg-line">
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="text" name="wdt-purchase-code" id="wdt-purchase-code"
                                   class="form-control input-sm" placeholder="Please enter your code" value=""/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4 wdt-include-bootstrap-block">
            <h4 class="c-black m-b-20">
                <?php _e('Include bootstrap', 'wpdatatables'); ?>
                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                   title="<?php _e('If bootstrap.js is already included in one of the theme files it is recommended to uncheck this option', 'wpdatatables'); ?>"></i>
            </h4>
            <div class="toggle-switch" data-ts-color="blue">
                <label for="wdt-include-bootstrap" class="ts-label">Include bootstrap.js on the front-end</label>
                <input type="checkbox" name="wdt-include-bootstrap" id="wdt-include-bootstrap" checked="checked"/>
                <label for="wdt-include-bootstrap" class="ts-helper"></label>
            </div>
        </div>
    </div>
</div>
