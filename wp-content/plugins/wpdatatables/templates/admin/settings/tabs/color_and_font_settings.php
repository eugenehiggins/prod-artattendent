<?php defined('ABSPATH') or die("Cannot access pages directly."); ?>

<?php
/**
 * User: Miljko Milosevic
 * Date: 1/20/17
 * Time: 1:29 PM
 */
?>

<div role="tabpanel" class="tab-pane" id="color-and-font-settings">
    <div class="row">
        <div class="panel-group col-sm-12" role="tablist" aria-multiselectable="true">
            <div class="panel panel-collapse">
                <div class="panel-heading active" role="tab" id="heading-one">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse-one" aria-expanded="true"
                           aria-controls="collapse-one">Font</a>
                    </h4>
                </div>
                <div id="collapse-one" class="collapse in" role="tabpanel" aria-labelledby="heading-one">
                    <div class="panel-body">
                        <div class="col-sm-4">
                            <h5 class="c-black m-b-25">
                                <?php _e('Font', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('This font will be used in rendered tables. Leave blank not to override default theme settings', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="form-group">
                                <div class="fg-line">
                                    <div class="select">
                                        <select id="wdt-table-font" data-name="wdtTableFont" class="selectpicker"
                                                title="Choose font for the table">
                                            <option value=""></option>
                                            <?php foreach (WDTSettingsController::wdtGetSystemFonts() as $font) { ?>
                                                <option value="<?php echo $font ?>"><?php echo $font ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <h5 class="c-black m-b-20">
                                <?php _e('Font size', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('Define the font size', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="form-group">
                                <div class="fg-line">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <input type="number" id="wdt-font-size" data-name="wdtFontSize"
                                                   class="form-control" min="0" value=""/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <h5 class="c-black m-b-20">
                                <?php _e('Font color', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('This color is used for the main font in table cells.', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="cp-container">
                                <div class="form-group">
                                    <div class="fg-line dropdown">
                                        <div id="cp"
                                             class="input-group colorpicker-component colorpicker-element color-picker wpcolorpicker">
                                            <input type="text" id="wdt-table-font-color" data-name="wdtTableFontColor"
                                                   class="form-control cp-value" value=""/>
                                            <span class="input-group-addon wpcolorpicker-icon"><i></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="panel-group col-sm-12" role="tablist" aria-multiselectable="true">
            <div class="panel panel-collapse">
                <div class="panel-heading" role="tab" id="heading-two">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse-two" aria-expanded="true"
                           aria-controls="collapse-two">Header</a>
                    </h4>
                </div>
                <div id="collapse-two" class="collapse" role="tabpanel" aria-labelledby="heading-two">
                    <div class="panel-body">
                        <div class="col-sm-3">
                            <h5 class="c-black m-b-20">
                                <?php _e('Background color', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('The color is used for background of the table header.', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="cp-container">
                                <div class="form-group">
                                    <div class="fg-line dropdown">
                                        <div id="cp"
                                             class="input-group colorpicker-component colorpicker-element color-picker wpcolorpicker">
                                            <input type="text" id="wdt-header-base-color" data-name="wdtHeaderBaseColor"
                                                   class="form-control cp-value" value=""/>
                                            <span class="input-group-addon wpcolorpicker-icon"><i></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <h5 class="c-black m-b-20">
                                <?php _e('Border color', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('This color is used for the border in the table header.', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="cp-container">
                                <div class="form-group">
                                    <div class="fg-line dropdown">
                                        <div id="cp"
                                             class="input-group colorpicker-component colorpicker-element color-picker wpcolorpicker">
                                            <input type="text" id="wdt-header-border-color"
                                                   data-name="wdtHeaderBorderColor" class="form-control cp-value"
                                                   value=""/>
                                            <span class="input-group-addon wpcolorpicker-icon"><i></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <h5 class="c-black m-b-20">
                                <?php _e('Font color', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('This color is used for the font in the table header.', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="cp-container">
                                <div class="form-group">
                                    <div class="fg-line dropdown">
                                        <div id="cp"
                                             class="input-group colorpicker-component colorpicker-element color-picker wpcolorpicker">
                                            <input type="text" id="wdt-header-font-color" data-name="wdtHeaderFontColor"
                                                   class="form-control cp-value" value=""/>
                                            <span class="input-group-addon wpcolorpicker-icon"><i></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <h5 class="c-black m-b-20">
                                <?php _e('Active and hover color	', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('This color is used when you hover the mouse above the table header, or when you choose a column.', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="cp-container">
                                <div class="form-group">
                                    <div class="fg-line dropdown">
                                        <div id="cp"
                                             class="input-group colorpicker-component colorpicker-element color-picker wpcolorpicker">
                                            <input type="text" id="wdt-header-active-color"
                                                   data-name="wdtHeaderActiveColor" class="form-control cp-value"
                                                   value=""/>
                                            <span class="input-group-addon wpcolorpicker-icon"><i></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="panel-group col-sm-12" role="tablist" aria-multiselectable="true">
            <div class="panel panel-collapse">
                <div class="panel-heading" role="tab" id="heading-three">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse-three" aria-expanded="false"
                           aria-controls="collapse-three" class="collapsed">Table border</a>
                    </h4>
                </div>
                <div id="collapse-three" class="collapse" role="tabpanel" aria-labelledby="heading-three">
                    <div class="panel-body">
                        <div class="col-sm-3">
                            <h5 class="c-black m-b-20">
                                <?php _e('Inner border', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('This color is used for the inner border in the table between cells.', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="cp-container">
                                <div class="form-group">
                                    <div class="fg-line dropdown">
                                        <div id="cp"
                                             class="input-group colorpicker-component colorpicker-element color-picker wpcolorpicker">
                                            <input type="text" id="wdt-table-inner-border-color"
                                                   data-name="wdtTableInnerBorderColor" class="form-control cp-value"
                                                   value=""/>
                                            <span class="input-group-addon wpcolorpicker-icon"><i></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <h5 class="c-black m-b-20">
                                <?php _e('Outer border', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('This color is used for the outer border of the whole table body.', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="cp-container">
                                <div class="form-group">
                                    <div class="fg-line dropdown">
                                        <div id="cp"
                                             class="input-group colorpicker-component colorpicker-element color-picker wpcolorpicker">
                                            <input type="text" id="wdt-table-outer-border-color"
                                                   data-name="wdtTableOuterBorderColor" class="form-control cp-value"
                                                   value=""/>
                                            <span class="input-group-addon wpcolorpicker-icon"><i></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="panel-group col-sm-12" role="tablist" aria-multiselectable="true">
            <div class="panel panel-collapse">
                <div class="panel-heading" role="tab" id="heading-four">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse-four" aria-expanded="false"
                           aria-controls="collapse-four" class="collapsed">Row color</a>
                    </h4>
                </div>
                <div id="collapse-four" class="collapse" role="tabpanel" aria-labelledby="heading-four">
                    <div class="panel-body">
                        <div class="col-sm-3">
                            <h5 class="c-black m-b-20">
                                <?php _e('Even row background', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('This color is used for for background in even rows.', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="cp-container">
                                <div class="form-group">
                                    <div class="fg-line dropdown">
                                        <div id="cp"
                                             class="input-group colorpicker-component colorpicker-element color-picker wpcolorpicker">
                                            <input type="text" id="wdt-even-row-color" data-name="wdtEvenRowColor"
                                                   class="form-control cp-value" value=""/>
                                            <span class="input-group-addon wpcolorpicker-icon"><i></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <h5 class="c-black m-b-20">
                                <?php _e('Odd row background', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('This color is used for for background in odd rows.', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="cp-container">
                                <div class="form-group">
                                    <div class="fg-line dropdown">
                                        <div id="cp"
                                             class="input-group colorpicker-component colorpicker-element color-picker wpcolorpicker">
                                            <input type="text" id="wdt-odd-row-color" data-name="wdtOddRowColor"
                                                   class="form-control cp-value" value=""/>
                                            <span class="input-group-addon wpcolorpicker-icon"><i></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <h5 class="c-black m-b-20">
                                <?php _e('Hover row', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('This color is used for to highlight the row when you hover your mouse above it.', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="cp-container">
                                <div class="form-group">
                                    <div class="fg-line dropdown">
                                        <div id="cp"
                                             class="input-group colorpicker-component colorpicker-element color-picker wpcolorpicker">
                                            <input type="text" id="wdt-hover-row-color" data-name="wdtHoverRowColor"
                                                   class="form-control cp-value" value=""/>
                                            <span class="input-group-addon wpcolorpicker-icon"><i></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <h5 class="c-black m-b-20">
                                <?php _e('Background for selected rows', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('This color is used for background in selected rows.', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="cp-container">
                                <div class="form-group">
                                    <div class="fg-line dropdown">
                                        <div id="cp"
                                             class="input-group colorpicker-component colorpicker-element color-picker wpcolorpicker">
                                            <input type="text" id="wdt-selected-row-color"
                                                   data-name="wdtSelectedRowColor" class="form-control cp-value"
                                                   value=""/>
                                            <span class="input-group-addon wpcolorpicker-icon"><i></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="panel-group col-sm-12" role="tablist" aria-multiselectable="true">
            <div class="panel panel-collapse">
                <div class="panel-heading" role="tab" id="heading-five">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse-five" aria-expanded="false"
                           aria-controls="collapse-five" class="collapsed">Cell color</a>
                    </h4>
                </div>
                <div id="collapse-five" class="collapse" role="tabpanel" aria-labelledby="heading-five">
                    <div class="panel-body">
                        <div class="col-sm-3">
                            <h5 class="c-black m-b-20">
                                <?php _e('Sorted columns, even rows', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('This color is used for background in cells which are in the active columns (columns used for sorting) in even rows.', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="cp-container">
                                <div class="form-group">
                                    <div class="fg-line dropdown">
                                        <div id="cp"
                                             class="input-group colorpicker-component colorpicker-element color-picker wpcolorpicker">
                                            <input type="text" id="wdt-active-even-cell-color"
                                                   data-name="wdtActiveEvenCellColor" class="form-control cp-value"
                                                   value=""/>
                                            <span class="input-group-addon wpcolorpicker-icon"><i></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <h5 class="c-black m-b-20">
                                <?php _e('Sorted columns, odd rows', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('This color is used for background in cells which are in the active columns (columns used for sorting) in odd rows.', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="cp-container">
                                <div class="form-group">
                                    <div class="fg-line dropdown">
                                        <div id="cp"
                                             class="input-group colorpicker-component colorpicker-element color-picker wpcolorpicker">
                                            <input type="text" id="wdt-active-odd-cell-color"
                                                   data-name="wdtActiveOddCellColor" class="form-control cp-value"
                                                   value=""/>
                                            <span class="input-group-addon wpcolorpicker-icon"><i></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="panel-group col-sm-12" role="tablist" aria-multiselectable="true">
            <div class="panel panel-collapse">
                <div class="panel-heading" role="tab" id="heading-six">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse-six" aria-expanded="false"
                           aria-controls="collapse-six" class="collapsed">Buttons color</a>
                    </h4>
                </div>
                <div id="collapse-six" class="collapse" role="tabpanel" aria-labelledby="heading-six">
                    <div class="panel-body">
                        <div class="col-sm-3">
                            <h5 class="c-black m-b-20">
                                <?php _e('Background color', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('This color is used for background in buttons.', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="cp-container">
                                <div class="form-group">
                                    <div class="fg-line dropdown">
                                        <div id="cp"
                                             class="input-group colorpicker-component colorpicker-element color-picker wpcolorpicker">
                                            <input type="text" id="wdt-button-color" data-name="wdtButtonColor"
                                                   class="form-control cp-value" value=""/>
                                            <span class="input-group-addon wpcolorpicker-icon"><i></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <h5 class="c-black m-b-20">
                                <?php _e('Border color', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('This color is used for border in buttons.', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="cp-container">
                                <div class="form-group">
                                    <div class="fg-line dropdown">
                                        <div id="cp"
                                             class="input-group colorpicker-component colorpicker-element color-picker wpcolorpicker">
                                            <input type="text" id="wdt-button-border-color"
                                                   data-name="wdtButtonBorderColor" class="form-control cp-value"
                                                   value=""/>
                                            <span class="input-group-addon wpcolorpicker-icon"><i></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <h5 class="c-black m-b-20">
                                <?php _e('Font color', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('This color is used for font in buttons.', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="cp-container">
                                <div class="form-group">
                                    <div class="fg-line dropdown">
                                        <div id="cp"
                                             class="input-group colorpicker-component colorpicker-element color-picker wpcolorpicker">
                                            <input type="text" id="wdt-button-font-color" data-name="wdtButtonFontColor"
                                                   class="form-control cp-value" value=""/>
                                            <span class="input-group-addon wpcolorpicker-icon"><i></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <h5 class="c-black m-b-20">
                                <?php _e('Background hover color', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('This color will be used for button backgrounds when you hover above them.', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="cp-container">
                                <div class="form-group">
                                    <div class="fg-line dropdown">
                                        <div id="cp"
                                             class="input-group colorpicker-component colorpicker-element color-picker wpcolorpicker">
                                            <input type="text" id="wdt-button-background-hover-color"
                                                   data-name="wdtButtonBackgroundHoverColor"
                                                   class="form-control cp-value" value=""/>
                                            <span class="input-group-addon wpcolorpicker-icon"><i></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <h5 class="c-black m-b-20">
                                <?php _e('Hover font color', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('This color will be used for buttons font when you hover above them.', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="cp-container">
                                <div class="form-group">
                                    <div class="fg-line dropdown">
                                        <div id="cp"
                                             class="input-group colorpicker-component colorpicker-element color-picker wpcolorpicker">
                                            <input type="text" id="wdt-button-font-hover-color"
                                                   data-name="wdtButtonFontHoverColor" class="form-control cp-value"
                                                   value=""/>
                                            <span class="input-group-addon wpcolorpicker-icon"><i></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <h5 class="c-black m-b-20">
                                <?php _e('Buttons hover border color', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('This color will be used for button borders when you hover above them.', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="cp-container">
                                <div class="form-group">
                                    <div class="fg-line dropdown">
                                        <div id="cp"
                                             class="input-group colorpicker-component colorpicker-element color-picker wpcolorpicker">
                                            <input type="text" id="wdt-button-border-hover-color"
                                                   data-name="wdtButtonBorderHoverColor" class="form-control cp-value"
                                                   value=""/>
                                            <span class="input-group-addon wpcolorpicker-icon"><i></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 buttons-input-border-radius">
                            <h5 class="c-black m-b-20">
                                <?php _e('Buttons and inputs border radius (in px)', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('This is a border radius for inputs in buttons. Default is 3px.', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="form-group">
                                <div class="fg-line">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <input type="number" id="wdt-border-input-radius"
                                                   data-name="wdtBorderRadius" class="form-control" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="panel-group col-sm-12" role="tablist" aria-multiselectable="true">
            <div class="panel panel-collapse">
                <div class="panel-heading" role="tab" id="heading-seven">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse-seven" aria-expanded="false"
                           aria-controls="collapse-seven" class="collapsed">Modals and overlay color</a>
                    </h4>
                </div>
                <div id="collapse-seven" class="collapse" role="tabpanel" aria-labelledby="heading-seven">
                    <div class="panel-body">
                        <div class="col-sm-3">
                            <h5 class="c-black m-b-20">
                                <?php _e('Modals font color', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('This color will be used for wpDataTable popup (filter, datepicker) fonts.', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="cp-container">
                                <div class="form-group">
                                    <div class="fg-line dropdown">
                                        <div id="cp"
                                             class="input-group colorpicker-component colorpicker-element color-picker wpcolorpicker">
                                            <input type="text" id="wdt-modal-font-color" data-name="wdtModalFontColor"
                                                   class="form-control cp-value" value=""/>
                                            <span class="input-group-addon wpcolorpicker-icon"><i></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <h5 class="c-black m-b-20">
                                <?php _e('Modals background', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('This color will be used for wpDataTable popup (filter, datepicker) background.', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="cp-container">
                                <div class="form-group">
                                    <div class="fg-line dropdown">
                                        <div id="cp"
                                             class="input-group colorpicker-component colorpicker-element color-picker wpcolorpicker">
                                            <input type="text" id="wdt-modal-background-color"
                                                   data-name="wdtModalBackgroundColor" class="form-control cp-value"
                                                   value=""/>
                                            <span class="input-group-addon wpcolorpicker-icon"><i></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <h5 class="c-black m-b-20">
                                <?php _e('Overlay background', 'wpdatatables'); ?>
                                <i class="zmdi zmdi-help-outline" data-toggle="tooltip" data-placement="right"
                                   title="<?php _e('This color will be used for overlay which appears below the plugin popups.', 'wpdatatables'); ?>"></i>
                            </h5>
                            <div class="cp-container">
                                <div class="form-group">
                                    <div class="fg-line dropdown">
                                        <div id="cp"
                                             class="input-group colorpicker-component colorpicker-element color-picker wpcolorpicker">
                                            <input type="text" id="wdt-overlay-color" data-name="wdtOverlayColor"
                                                   class="form-control cp-value" value=""/>
                                            <span class="input-group-addon wpcolorpicker-icon"><i></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
