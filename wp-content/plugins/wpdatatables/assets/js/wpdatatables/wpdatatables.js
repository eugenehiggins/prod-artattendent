/** New JS controller for wpDataTables **/

var wpDataTables = {};
var wpDataTablesSelRows = {};
var wpDataTablesFunctions = {};
var wpDataTablesUpdatingFlags = {};
var wpDataTablesResponsiveHelpers = {};
var wdtBreakpointDefinition = {
    tablet: 1024,
    phone: 480
};
var wdtCustomUploader = null;

var wdtRenderDataTable = null;

(function ($) {
    $(function () {

        /**
         * Helper function to render a DataTable
         *
         * @param $table jQuery link to the container table object
         * @param tableDescription JSON with the table description
         */
        wdtRenderDataTable = function ($table, tableDescription) {

            // Parse the DataTable init options
            var dataTableOptions = tableDescription.dataTableParams;

            //[<-- Full version -->]//
            /**
             * Responsive-mode related stuff
             */
            if (tableDescription.responsive) {
                wpDataTablesResponsiveHelpers[tableDescription.tableId] = false;
                dataTableOptions.preDrawCallback = function () {
                    if (!wpDataTablesResponsiveHelpers[tableDescription.tableId]) {
                        if (typeof tableDescription.mobileWidth !== 'undefined') {
                            wdtBreakpointDefinition.phone = parseInt(tableDescription.mobileWidth);
                        }
                        if (typeof tableDescription.tabletWidth !== 'undefined') {
                            wdtBreakpointDefinition.tablet = parseInt(tableDescription.tabletWidth);
                        }
                        wpDataTablesResponsiveHelpers[tableDescription.tableId] = new ResponsiveDatatablesHelper($(tableDescription.selector).dataTable(), wdtBreakpointDefinition, {
                            showDetail: function (detailsRow) {
                                if (tableDescription.conditional_formatting_columns) {
                                    var responsive_rows = detailsRow.find('li');
                                    var oSettings = wpDataTables[tableDescription.tableId].fnSettings();
                                    var params = {};

                                    params.thousandsSeparator = tableDescription.number_format == 1 ? '.' : ',';
                                    params.decimalSeparator = tableDescription.number_format == 1 ? ',' : '.';
                                    params.dateFormat = tableDescription.datepickFormat;
                                    params.momentDateFormat = params.dateFormat.replace('dd', 'DD').replace('M', 'MMM').replace('mm', 'MM');
                                    params.momentTimeFormat = tableDescription.timeFormat.replace('H', 'H').replace('i', 'mm');

                                    for (var i = 0; i < tableDescription.conditional_formatting_columns.length; i++) {
                                        var column = oSettings.oInstance.api().column(tableDescription.conditional_formatting_columns[i] + ':name', {search: 'applied'});
                                        var conditionalFormattingRules = oSettings.aoColumns[column.index()].conditionalFormattingRules;
                                        params.columnType = oSettings.aoColumns[column.index()].wdtType;

                                        for (var j in conditionalFormattingRules) {
                                            responsive_rows.each(function () {
                                                $(this).find('.columnValue').contents().filter(function () {
                                                    if (this.nodeType === 8) {
                                                        $(this).remove();
                                                    }
                                                });

                                                var value_cell = $(this).find('.columnValue').html();

                                                var column_index = $(this).data('column');
                                                if (column_index == column.index()) {
                                                    wdtCheckConditionalFormatting(conditionalFormattingRules[j], params, $(this), true);
                                                }
                                            });
                                        }
                                    }
                                }
                            }
                        });
                    }
                    wdtAddOverlay('#' + tableDescription.tableId);
                }
                dataTableOptions.fnRowCallback = function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    wpDataTablesResponsiveHelpers[tableDescription.tableId].createExpandIcon(nRow);
                }
                if (!tableDescription.editable) {
                    dataTableOptions.fnDrawCallback = function () {
                        wpDataTablesResponsiveHelpers[tableDescription.tableId].respond();
                        wdtRemoveOverlay('#' + tableDescription.tableId);
                    }
                }
            } else {
                dataTableOptions.fnPreDrawCallback = function () {
                    wdtAddOverlay('#' + tableDescription.tableId);
                }
            }

            if (tableDescription.editable) {

                if (typeof wpDataTablesFunctions[tableDescription.tableId] === 'undefined') {
                    wpDataTablesFunctions[tableDescription.tableId] = {};
                }

                wpDataTablesSelRows[tableDescription.tableId] = -1;
                dataTableOptions.fnDrawCallback = function () {
                    wdtRemoveOverlay('#' + tableDescription.tableId);
                    if (tableDescription.responsive) {
                        wpDataTablesResponsiveHelpers[tableDescription.tableId].respond();
                    }

                    $('.edit_table[aria-controls="' + tableDescription.tableId + '"]').addClass('disabled');
                    $('.delete_table_entry[aria-controls="' + tableDescription.tableId + '"]').addClass('disabled');

                    if (wpDataTablesSelRows[tableDescription.tableId] == -2) {
                        // -2 means select first row on "next" page
                        var sel_row_index = wpDataTables[tableDescription.tableId].fnSettings()._iDisplayLength - 1;
                        $(tableDescription.selector + ' > tbody > tr').removeClass('selected');
                        wpDataTablesSelRows[tableDescription.tableId] = wpDataTables[tableDescription.tableId].fnGetPosition($(tableDescription.selector + ' > tbody > tr:eq(' + sel_row_index + ')').get(0));
                        $(tableDescription.selector + ' > tbody > tr:eq(' + sel_row_index + ')').addClass('selected');
                    } else if (wpDataTablesSelRows[tableDescription.tableId] == -3) {
                        var sel_row_index = 0;
                        $(tableDescription.selector + ' > tbody > tr').removeClass('selected');
                        wpDataTablesSelRows[tableDescription.tableId] = wpDataTables[tableDescription.tableId].fnGetPosition($(tableDescription.selector + ' > tbody > tr:eq(' + sel_row_index + ')').get(0));
                        $(tableDescription.selector + ' > tbody > tr:eq(' + sel_row_index + ')').addClass('selected');
                    }

                    if ($(tableDescription.selector + '_edit_dialog').is(':visible')) {
                        var data = wpDataTables[tableDescription.tableId].fnGetData(wpDataTablesSelRows[tableDescription.tableId]);
                        wpDataTablesFunctions[tableDescription.tableId].applyData(data);
                    }
                    $(tableDescription.selector + '_edit_dialog').parent().removeClass('overlayed');

                    wpDataTablesUpdatingFlags[tableDescription.tableId] = false;
                };

                /**
                 * Data apply function for editable tables
                 * @param data
                 */
                wpDataTablesFunctions[tableDescription.tableId].applyData = function (data) {
                    $(data).each(function (index, el) {
                        if (el) {
                            var val = el.toString();
                        } else {
                            var val = '';
                        }
                        if (val.indexOf('span') != -1) {
                            val = val.replace(/<span>/g, '').replace(/<\/span>/g, '');
                        }
                        if (val.indexOf('<br/>') != -1 || val.indexOf('<br>') != -1) {
                            val = val.replace(/<br\s*[\/]?>/g, "\n");
                        }


                        var $inputElement = $('#' + tableDescription.tableId + '_edit_dialog .editDialogInput:not(.bootstrap-select):eq(' + index + ')');
                        var inputElementType = $inputElement.data('input_type');
                        var columnType = $inputElement.data('column_type');
                        if (inputElementType == 'multi-selectbox') {
                            var values = val.split(', ');
                            $inputElement.selectpicker();
                            $inputElement.selectpicker('val', values);
                            $inputElement.selectpicker('refresh');
                        } else if (inputElementType == 'selectbox') {
                            $inputElement.selectpicker();
                            if ($inputElement.hasClass('wdt-foreign-key-select') && val != '') {
                                val = $inputElement.find('option[data-label="' + val + '"]').val();
                            }
                            if (val == '') {
                                val = 'possibleValuesAddEmpty';
                            }
                            $inputElement.selectpicker('val', val);
                            $inputElement.selectpicker('refresh');
                        } else {
                            if (inputElementType == 'attachment' || $.inArray(columnType, ['icon']) !== -1) {
                                columnType = $inputElement.parent().data('column_type');
                                if (val != '') {
                                    if ($(val).children('img').first().attr('src') != undefined) {
                                        val = $(val).children('img').first().attr('src') + '||' + $(val).attr('href');
                                    } else if ($(val).attr('href') != undefined) {
                                        val = $(val).attr('href');
                                    } else if ($(val).attr('src') != undefined) {
                                        val = $(val).attr('src');
                                    }

                                    $inputElement.parent().parent().removeClass('fileinput-new').addClass('fileinput-exists');
                                    if (columnType == 'icon') {
                                        $inputElement.parent().parent().parent().removeClass('fileinput-new').addClass('fileinput-exists');
                                        if (val.indexOf('||') != -1) {
                                            $inputElement.parent().parent().parent().find('.fileinput-preview').html('<img src=' + val.substring(val.indexOf('||') + 2, val.length) + '>');
                                        } else {
                                            $inputElement.parent().parent().parent().find('.fileinput-preview').html('<img src=' + val + '>');
                                        }
                                    } else {
                                        $inputElement.parent().parent().find('.fileinput-filename').text(val.split('/').pop());
                                    }
                                } else {
                                    $inputElement.closest('.fileinput').removeClass('fileinput-exists').addClass('fileinput-new');
                                    $inputElement.closest('.fileinput').find('div.fileinput-exists').removeClass('fileinput-exists').addClass('fileinput-new');
                                    $inputElement.closest('.fileinput').find('.fileinput-filename').text('');
                                    $inputElement.closest('.fileinput').find('.fileinput-preview').html('');
                                }
                            } else {
                                if (val.indexOf('<a ') != -1) {
                                    if ($.inArray(columnType, ['link', 'email', 'icon']) !== -1) {
                                        $link = $(val);
                                        if ($link.attr('href').indexOf($link.html()) === -1) {
                                            val = $link.attr('href').replace('mailto:', '') + '||' + $link.html();
                                        } else {
                                            val = $link.html();
                                        }
                                    }
                                }

                                if (inputElementType == 'mce-editor') {
                                    tinymce.execCommand('mceRemoveEditor', true, $inputElement.attr('id'));
                                    tinymce.init({
                                        selector: '#' + $inputElement.attr('id'),
                                        init_instance_callback: function (editor) {
                                            editor.setContent(val);
                                        },
                                        menubar: false
                                    });
                                }
                            }
                            $inputElement.val(val).css('border', '');
                        }
                    });
                };

                /**
                 * Saving of the table data for frontend
                 *
                 * @param forceRedraw
                 * @param closeDialog
                 * @returns {boolean}
                 */
                wpDataTablesFunctions[tableDescription.tableId].saveTableData = function (forceRedraw, closeDialog) {
                    $(tableDescription.selector + '_edit_dialog').closest('.modal-dialog').find('.wdt-preload-layer').animateFadeIn();
                    wpDataTablesUpdatingFlags[tableDescription.tableId] = true;
                    var formdata = {
                        table_id: tableDescription.tableWpId
                    };
                    var aoData = [];
                    var valid = true;
                    var validation_message = '';
                    if (tableDescription.popoverTools) {
                        $('.wpDataTablesPopover.editTools').hide();
                    }

                    //Moves tinymce value to hidden initial textarea
                    if (typeof tinymce != 'undefined') {
                        tinymce.triggerSave();
                    }
                    $(tableDescription.selector + '_edit_dialog .editDialogInput').not('.bootstrap-select').each(function () {
                        // validation
                        if ($(this).data('input_type') == 'email') {
                            if ($(this).val() != '') {
                                var field_valid = wdtValidateEmail($(this).val());
                                if (!field_valid) {
                                    valid = false;
                                    $(this).addClass('error');
                                    validation_message += wpdatatables_frontend_strings.invalid_email + ' <b>' + $(this).data('column_header') + '</b><br>';
                                } else {
                                    $(this).removeClass('error')
                                }
                            }
                        } else if ($(this).data('input_type') == 'link') {
                            if ($(this).val() != '') {
                                field_valid = wdtValidateURL($(this).val());
                                if (!field_valid) {
                                    valid = false;
                                    $(this).addClass('error');
                                    validation_message += wpdatatables_frontend_strings.invalid_link + ' <b>' + $(this).data('column_header') + '</b><br>';
                                } else {
                                    $(this).removeClass('error');
                                }
                            }
                        }
                        if ($(this).hasClass('mandatory')) {
                            if ($(this).val() == '' || $(this).val() == null) {
                                $(this).addClass('error');
                                valid = false;
                                validation_message += '<b>' + $(this).data('column_header') + '</b> ' + wpdatatables_frontend_strings.cannot_be_empty + '<br>';
                            } else {
                                if (valid) {
                                    $(this).removeClass('error');
                                }
                            }
                        }
                        if ($(this).hasClass('datepicker')) {
                            formdata[$(this).data('key')] = $.datepicker.formatDate(tableDescription.datepickFormat, $.datepicker.parseDate(tableDescription.datepickFormat, $(this).val()));
                        } else if ($(this).data('input_type') == 'multi-selectbox') {
                            if ($(this).val()) {
                                formdata[$(this).data('key')] = $(this).val().join(', ');
                            } else {
                                formdata[$(this).data('key')] = '';
                            }
                        } else if ($(this).data('column_type') == 'int') {
                            formdata[$(this).data('key')] = $(this).val().replace(/,/g, '').replace(/\./g, '');
                        } else {
                            formdata[$(this).data('key')] = $(this).val();
                        }
                        aoData.push(formdata[$(this).data('key')]);
                    });
                    if (!valid) {
                        $(tableDescription.selector + '_edit_dialog').closest('.modal-dialog').find('.wdt-preload-layer').animateFadeOut();
                        wdtNotify(wpdatatables_edit_strings.error, validation_message, 'danger');
                        return false;
                    }
                    wpDataTablesUpdatingFlags[tableDescription.tableId] = true;
                    $.ajax({
                        url: tableDescription.adminAjaxBaseUrl,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'wdt_save_table_frontend',
                            wdtNonce: $('#wdtNonceFrontendEdit').val(),
                            formdata: formdata
                        },
                        success: function (returnData) {
                            $(tableDescription.selector + '_edit_dialog').closest('.modal-dialog').find('.wdt-preload-layer').animateFadeOut();
                            if (returnData.error == '') {
                                var insert_id = returnData.success;
                                if (returnData.is_new) {
                                    forceRedraw = true;
                                }
                                if (insert_id) {
                                    $(tableDescription.selector + '_edit_dialog tr.idRow .editDialogInput').val(insert_id);
                                    if (forceRedraw) {
                                        wpDataTables[tableDescription.tableId].fnDraw(false);
                                        $('.edit_table[aria-controls="' + tableDescription.tableId + '"]').addClass('disabled');
                                    }
                                } else {
                                    wpDataTables[tableDescription.tableId].fnDraw(false);
                                    $('.edit_table[aria-controls="' + tableDescription.tableId + '"]').addClass('disabled');
                                }

                                wdtNotify(wpdatatables_edit_strings.success, wpdatatables_edit_strings.dataSaved, 'success');
                                setTimeout(function () {
                                    if (closeDialog) {
                                        $('#wdt-frontend-modal').modal('hide');
                                    } else {
                                        $(tableDescription.selector + '_edit_dialog .editDialogInput').val('');
                                        $('.fileinput').removeClass('fileinput-exists').addClass('fileinput-new');
                                        $('.fileinput').find('div.fileinput-exists').removeClass('fileinput-exists').addClass('fileinput-new');
                                        $('.fileinput').find('.fileinput-filename').text('');
                                        $('.fileinput').find('.fileinput-preview').html('');
                                        if (tinymce.activeEditor)
                                            tinymce.activeEditor.setContent('');
                                    }
                                }, 1000);
                                if (!returnData.is_new && $(tableDescription.selector + ' > tbody > tr.selected').length) {
                                    var cursor = wpDataTables[tableDescription.tableId].fnGetPosition($(tableDescription.selector + ' > tbody > tr.selected').get(0));
                                    wpDataTables[tableDescription.tableId].fnSettings().aoData[cursor]._aData = aoData;
                                }
                            } else {
                                wdtNotify(wpdatatables_edit_strings.error, returnData.error, 'danger');
                            }
                        },
                        error: function () {
                            $(tableDescription.selector + '_edit_dialog').closest('.modal-dialog').find('.wdt-preload-layer').animateFadeOut();
                            wdtNotify(wpdatatables_edit_strings.error, wpdatatables_frontend_strings.databaseInsertError, 'danger');
                        }
                    });
                    return true;
                }
            }

            /**
             * Remove overlay if the table is not responsive nor editable
             */
            if (!tableDescription.responsive
                && !tableDescription.editable) {
                dataTableOptions.fnDrawCallback = function () {
                    wdtRemoveOverlay('#' + tableDescription.tableId);
                }
            }
            //[<--/ Full version -->]//

            /**
             * If aggregate functions shortcode exists on the page add that column to the ajax data
             */
            if ($('.wdt-column-sum[data-table-id="' + tableDescription.tableWpId + '"]').length) {
                var sumColumns = [];
                $('.wdt-column-sum[data-table-id="' + tableDescription.tableWpId + '"]').each(function () {
                    sumColumns.push($(this).data('column-orig-header'));
                });
            }

            if ($('.wdt-column-avg[data-table-id="' + tableDescription.tableWpId + '"]').length) {
                var avgColumns = [];
                $('.wdt-column-avg[data-table-id="' + tableDescription.tableWpId + '"]').each(function () {
                    avgColumns.push($(this).data('column-orig-header'));
                });
            }

            if ($('.wdt-column-min[data-table-id="' + tableDescription.tableWpId + '"]').length) {
                var minColumns = [];
                $('.wdt-column-min[data-table-id="' + tableDescription.tableWpId + '"]').each(function () {
                    minColumns.push($(this).data('column-orig-header'));
                });
            }

            if ($('.wdt-column-max[data-table-id="' + tableDescription.tableWpId + '"]').length) {
                var maxColumns = [];
                $('.wdt-column-max[data-table-id="' + tableDescription.tableWpId + '"]').each(function () {
                    maxColumns.push($(this).data('column-orig-header'));
                });
            }

            if (tableDescription.serverSide) {
                dataTableOptions.ajax.data = function (data) {
                    data.sumColumns = sumColumns;
                    data.avgColumns = avgColumns;
                    data.minColumns = minColumns;
                    data.maxColumns = maxColumns;
                    data.currentUserId = $('#wdt-user-id-placeholder').val();
                    data.currentUserLogin = $('#wdt-user-login-placeholder').val();
                    data.currentPostIdPlaceholder = $('#wdt-post-id-placeholder').val();
                    data.wpdbPlaceholder = $('#wdt-wpdb-placeholder').val();
                };
            }

            /**
             * Show after load if configured
             */
            if (tableDescription.hideBeforeLoad) {
                dataTableOptions.fnInitComplete = function () {
                    $(tableDescription.selector).animateFadeIn();
                }
            }

            /**
             * Init the DataTable itself
             */
            wpDataTables[tableDescription.tableId] = $(tableDescription.selector).dataTable(dataTableOptions);

            //[<-- Full version -->]//
            /**
             * Enable auto-refresh if defined
             */
            if (tableDescription.serverSide) {
                if (parseInt(tableDescription.autoRefreshInterval) > 0) {
                    setInterval(function () {
                            wpDataTables[tableDescription.tableId].fnDraw(false)
                        },
                        parseInt(tableDescription.autoRefreshInterval) * 1000
                    );
                }
            }
            //[<--/ Full version -->]//

            /**
             * Add the draw callback
             * @param callback
             */
            wpDataTables[tableDescription.tableId].addOnDrawCallback = function (callback) {
                if (typeof callback !== 'function') {
                    return;
                }

                var index = wpDataTables[tableDescription.tableId].fnSettings().aoDrawCallback.length + 1;

                wpDataTables[tableDescription.tableId].fnSettings().aoDrawCallback.push({
                    sName: 'user_callback_' + index,
                    fn: callback
                });

            };

            //[<-- Full version -->]//
            /**
             * SUM, AVG, MIN, MAX functions callback
             */
            if (tableDescription.hasSumColumns || tableDescription.hasAvgColumns || tableDescription.hasMinColumns || tableDescription.hasMaxColumns
                || $('.wdt-column-sum').length || $('.wdt-column-avg').length || $('.wdt-column-min').length || $('.wdt-column-max').length) {

                var sumLabel = tableDescription.sumFunctionsLabel ? tableDescription.sumFunctionsLabel : '&#8721; = ';
                var avgLabel = tableDescription.avgFunctionsLabel ? tableDescription.avgFunctionsLabel : 'Avg = ';
                var minLabel = tableDescription.minFunctionsLabel ? tableDescription.minFunctionsLabel : 'Min = ';
                var maxLabel = tableDescription.maxFunctionsLabel ? tableDescription.maxFunctionsLabel : 'Max = ';

                if (tableDescription.serverSide) {
                    // Case with server-side table
                    wpDataTables[tableDescription.tableId].fnSettings().aoDrawCallback.push({
                        sName: 'updateFooterFunctions',
                        fn: function (oSettings) {
                            var api = oSettings.oInstance.api();

                            for (var columnName in api.ajax.json().sumFooterColumns) {
                                if (tableDescription.hasSumColumns) {
                                    $('#' + tableDescription.tableId + ' tfoot tr.wdt-sum-row td.wdt-sum-cell[data-column_header="' + columnName + '"]').html(sumLabel + ' ' + api.ajax.json().sumColumnsValues[columnName]);
                                }
                            }
                            for (columnName in api.ajax.json().avgFooterColumns) {
                                if (tableDescription.hasAvgColumns) {
                                    $('#' + tableDescription.tableId + ' tfoot tr.wdt-avg-row td.wdt-avg-cell[data-column_header="' + columnName + '"]').html(avgLabel + ' ' + api.ajax.json().avgColumnsValues[columnName]);
                                }
                            }
                            for (columnName in api.ajax.json().minFooterColumns) {
                                if (tableDescription.hasMinColumns) {
                                    $('#' + tableDescription.tableId + ' tfoot tr.wdt-min-row td.wdt-min-cell[data-column_header="' + columnName + '"]').html(minLabel + ' ' + api.ajax.json().minColumnsValues[columnName]);
                                }
                            }
                            for (columnName in api.ajax.json().maxFooterColumns) {
                                if (tableDescription.hasMaxColumns) {
                                    $('#' + tableDescription.tableId + ' tfoot tr.wdt-max-row td.wdt-max-cell[data-column_header="' + columnName + '"]').html(maxLabel + ' ' + api.ajax.json().maxColumnsValues[columnName]);
                                }
                            }

                            if ($('.wdt-column-sum').length) {
                                $('.wdt-column-sum[data-table-id="' + tableDescription.tableWpId + '"]').each(function () {
                                    $(this).find('.wdt-column-sum-value').text(api.ajax.json().sumColumnsValues[$(this).data('column-orig-header')]);
                                })
                            }

                            if ($('.wdt-column-avg').length) {
                                $('.wdt-column-avg[data-table-id="' + tableDescription.tableWpId + '"]').each(function () {
                                    $(this).find('.wdt-column-avg-value').text(api.ajax.json().avgColumnsValues[$(this).data('column-orig-header')]);
                                })
                            }

                            if ($('.wdt-column-min').length) {
                                $('.wdt-column-min[data-table-id="' + tableDescription.tableWpId + '"]').each(function () {
                                    $(this).find('.wdt-column-min-value').text(api.ajax.json().minColumnsValues[$(this).data('column-orig-header')]);
                                })
                            }

                            if ($('.wdt-column-max').length) {
                                $('.wdt-column-max[data-table-id="' + tableDescription.tableWpId + '"]').each(function () {
                                    $(this).find('.wdt-column-max-value').text(api.ajax.json().maxColumnsValues[$(this).data('column-orig-header')]);
                                })
                            }

                        }
                    });
                } else {
                    // Case with client-side table
                    wpDataTables[tableDescription.tableId].fnSettings().aoDrawCallback.push({
                        sName: 'updateFooterFunctions',
                        fn: function (oSettings) {
                            var api = oSettings.oInstance.api();

                            var thousandsSeparator = tableDescription.number_format == 1 ? '.' : ',';
                            var decimalSeparator = tableDescription.number_format == 1 ? ',' : '.';

                            for (var i in tableDescription.sumAvgColumns) {

                                var columnData = api.column(tableDescription.sumAvgColumns[i] + ':name', {search: 'applied'}).data();
                                var columnType = oSettings.aoColumns[api.column(tableDescription.sumAvgColumns[i] + ':name').index()].wdtType;

                                var sum = wdtCalculateColumnSum(columnData, thousandsSeparator);

                                var sumStr = wdtFormatNumberByColumnType(parseFloat(sum), columnType, tableDescription.columnsDecimalPlaces[tableDescription.sumAvgColumns[i]],
                                    tableDescription.decimalPlaces, decimalSeparator, thousandsSeparator);

                                if (_.contains(tableDescription.sumColumns, tableDescription.sumAvgColumns[i])) {
                                    $('#' + tableDescription.tableId + ' tfoot tr.wdt-sum-row td.wdt-sum-cell[data-column_header="' + tableDescription.sumAvgColumns[i] + '"]')
                                        .html(sumLabel + ' ' + sumStr);
                                }

                                if (_.contains(tableDescription.avgColumns, tableDescription.sumAvgColumns[i])) {
                                    var avg = sum / api.page.info().recordsDisplay;

                                    var avgStr = wdtFormatNumberByColumnType(avg, 'float', tableDescription.columnsDecimalPlaces[tableDescription.sumAvgColumns[i]],
                                        tableDescription.decimalPlaces, decimalSeparator, thousandsSeparator);

                                    $('#' + tableDescription.tableId + ' tfoot tr.wdt-avg-row td.wdt-avg-cell[data-column_header="' + tableDescription.sumAvgColumns[i] + '"]')
                                        .html(avgLabel + ' ' + avgStr);
                                }

                            }
                            for (i in tableDescription.minColumns) {

                                columnData = api.column(tableDescription.minColumns[i] + ':name', {search: 'applied'}).data();
                                columnType = oSettings.aoColumns[api.column(tableDescription.minColumns[i] + ':name').index()].wdtType;

                                var min = wdtCalculateColumnMin(columnData, thousandsSeparator);

                                var minStr = wdtFormatNumberByColumnType(parseFloat(min), columnType, tableDescription.columnsDecimalPlaces[tableDescription.minColumns[i]],
                                    tableDescription.decimalPlaces, decimalSeparator, thousandsSeparator);

                                $('#' + tableDescription.tableId + ' tfoot tr.wdt-min-row td.wdt-min-cell[data-column_header="' + tableDescription.minColumns[i] + '"]')
                                    .html(minLabel + ' ' + minStr);
                            }
                            for (i in tableDescription.maxColumns) {

                                columnData = api.column(tableDescription.maxColumns[i] + ':name', {search: 'applied'}).data();
                                columnType = oSettings.aoColumns[api.column(tableDescription.maxColumns[i] + ':name').index()].wdtType;

                                var max = wdtCalculateColumnMax(columnData, thousandsSeparator);

                                var maxStr = wdtFormatNumberByColumnType(parseFloat(max), columnType, tableDescription.columnsDecimalPlaces[tableDescription.maxColumns[i]],
                                    tableDescription.decimalPlaces, decimalSeparator, thousandsSeparator);

                                $('#' + tableDescription.tableId + ' tfoot tr.wdt-max-row td.wdt-max-cell[data-column_header="' + tableDescription.maxColumns[i] + '"]')
                                    .html(maxLabel + ' ' + maxStr);
                            }

                            // Update values from wpdatatables_{func} shortcode
                            if ($('.wdt-column-sum').length) {
                                $('.wdt-column-sum[data-table-id="' + tableDescription.tableWpId + '"]').each(function () {
                                    var columnData = api.column($(this).data('column-orig-header') + ':name', {search: 'applied'}).data();
                                    var columnType = oSettings.aoColumns[api.column($(this).data('column-orig-header') + ':name').index()].wdtType;

                                    var sum = wdtCalculateColumnSum(columnData, thousandsSeparator);

                                    var sumStr = wdtFormatNumberByColumnType(parseFloat(sum), columnType, tableDescription.columnsDecimalPlaces[$(this).data('column-orig-header')],
                                        tableDescription.decimalPlaces, decimalSeparator, thousandsSeparator);

                                    $(this).find('.wdt-column-sum-value').text(sumStr);
                                })
                            }

                            if ($('.wdt-column-avg').length) {
                                $('.wdt-column-avg[data-table-id="' + tableDescription.tableWpId + '"]').each(function () {
                                    var columnData = api.column($(this).data('column-orig-header') + ':name', {search: 'applied'}).data();

                                    var avg = wdtCalculateColumnSum(columnData, thousandsSeparator) / api.page.info().recordsDisplay;

                                    var avgStr = wdtFormatNumberByColumnType(parseFloat(avg), 'float', tableDescription.columnsDecimalPlaces[$(this).data('column-orig-header')],
                                        tableDescription.decimalPlaces, decimalSeparator, thousandsSeparator);

                                    $(this).find('.wdt-column-avg-value').text(avgStr);
                                })
                            }

                            if ($('.wdt-column-min').length) {
                                $('.wdt-column-min[data-table-id="' + tableDescription.tableWpId + '"]').each(function () {
                                    var columnData = api.column($(this).data('column-orig-header') + ':name', {search: 'applied'}).data();
                                    var columnType = oSettings.aoColumns[api.column($(this).data('column-orig-header') + ':name').index()].wdtType;

                                    var min = wdtCalculateColumnMin(columnData, thousandsSeparator);

                                    var minStr = wdtFormatNumberByColumnType(parseFloat(min), columnType, tableDescription.columnsDecimalPlaces[$(this).data('column-orig-header')],
                                        tableDescription.decimalPlaces, decimalSeparator, thousandsSeparator);

                                    $(this).find('.wdt-column-min-value').text(minStr);
                                })
                            }

                            if ($('.wdt-column-max').length) {
                                $('.wdt-column-max[data-table-id="' + tableDescription.tableWpId + '"]').each(function () {
                                    var columnData = api.column($(this).data('column-orig-header') + ':name', {search: 'applied'}).data();
                                    var columnType = oSettings.aoColumns[api.column($(this).data('column-orig-header') + ':name').index()].wdtType;

                                    var max = wdtCalculateColumnMax(columnData, thousandsSeparator);

                                    var maxStr = wdtFormatNumberByColumnType(parseFloat(max), columnType, tableDescription.columnsDecimalPlaces[$(this).data('column-orig-header')],
                                        tableDescription.decimalPlaces, decimalSeparator, thousandsSeparator);

                                    $(this).find('.wdt-column-max-value').text(maxStr);
                                })
                            }
                        }
                    });

                }
            }

            /**
             * Conditional formatting
             */
            if (tableDescription.conditional_formatting_columns) {
                wpDataTables[tableDescription.tableId].fnSettings().aoDrawCallback.push({
                    sName: 'updateConditionalFormatting',
                    fn: function (oSettings) {
                        for (var i = 0; i < tableDescription.conditional_formatting_columns.length; i++) {
                            var params = {};
                            var column = oSettings.oInstance.api().column(tableDescription.conditional_formatting_columns[i] + ':name', {search: 'applied'});
                            var conditionalFormattingRules = oSettings.aoColumns[column.index()].conditionalFormattingRules;
                            params.columnType = oSettings.aoColumns[column.index()].wdtType;
                            params.thousandsSeparator = tableDescription.number_format == 1 ? '.' : ',';
                            params.decimalSeparator = tableDescription.number_format == 1 ? ',' : '.';
                            params.dateFormat = tableDescription.datepickFormat;
                            params.momentDateFormat = params.dateFormat.replace('dd', 'DD').replace('M', 'MMM').replace('mm', 'MM').replace('yy', 'YYYY');
                            params.momentTimeFormat = tableDescription.timeFormat.replace('H', 'H').replace('i', 'mm');
                            for (var j in conditionalFormattingRules) {
                                var nodes = column.nodes();
                                column.nodes().to$().each(function () {
                                    wdtCheckConditionalFormatting(conditionalFormattingRules[j], params, $(this));
                                });
                            }
                        }
                    }
                });
                if (!tableDescription.serverSide) {
                    wpDataTables[tableDescription.tableId].fnDraw();
                }
            }

            /**
             * Init the callback for checking if the selected row is first/last in the dataset
             */
            wpDataTables[tableDescription.tableId].checkSelectedLimits = function () {
                if (wpDataTablesUpdatingFlags[tableDescription.tableId]) {
                    return;
                }
                var sel_row_index = $(tableDescription.selector + ' > tbody > tr.selected').index();
                if (sel_row_index + wpDataTables[tableDescription.tableId].fnSettings()._iDisplayStart == wpDataTables[tableDescription.tableId].fnSettings()._iRecordsDisplay - 1) {
                    $(tableDescription.selector + '_next_edit_dialog').prop('disabled', true)
                } else {
                    $(tableDescription.selector + '_next_edit_dialog').prop('disabled', false)
                }
                if ((sel_row_index == 0 && wpDataTables[tableDescription.tableId].fnSettings()._iDisplayStart == 0) || wpDataTables[tableDescription.tableId].fnSettings()._iRecordsDisplay == 0) {
                    $(tableDescription.selector + '_prev_edit_dialog').prop('disabled', true)
                } else {
                    $(tableDescription.selector + '_prev_edit_dialog').prop('disabled', false)
                }
            };
            //[<--/ Full version -->]//

            /**
             * Init row grouping if enabled
             */
            if ((tableDescription.columnsFixed == 0) && (tableDescription.groupingEnabled)) {
                wpDataTables[tableDescription.tableId].rowGrouping({iGroupingColumnIndex: tableDescription.groupingColumnIndex});
            }

            //[<-- Full version -->]//
            /**
             * Init the advanced filtering if enabled
             */
            if (tableDescription.advancedFilterEnabled) {
                $('#' + tableDescription.tableId).dataTable().columnFilter(tableDescription.advancedFilterOptions);
                wdtAttachClearFiltersEvent();
            }

            if (tableDescription.editable) {

                /**
                 * Previous button in edit dialog
                 */
                $(document).on('click', tableDescription.selector + '_prev_edit_dialog', function (e) {
                    e.preventDefault();
                    var sel_row_index = $(tableDescription.selector + ' > tbody > tr.selected').index();
                    if (sel_row_index > 0) {
                        $(tableDescription.selector + ' > tbody > tr.selected').removeClass('selected');
                        $(tableDescription.selector + ' > tbody > tr:eq(' + (sel_row_index - 1) + ')').addClass('selected', 300);
                        wpDataTablesSelRows[tableDescription.tableId] = wpDataTables[tableDescription.tableId].fnGetPosition($(tableDescription.selector + ' > tbody > tr.selected').get(0));
                        var data = wpDataTables[tableDescription.tableId].fnGetData(wpDataTablesSelRows[tableDescription.tableId]);
                        wpDataTablesFunctions[tableDescription.tableId].applyData(data);
                    } else {
                        var cur_page = Math.ceil(wpDataTables[tableDescription.tableId].fnSettings()._iDisplayStart / wpDataTables[tableDescription.tableId].fnSettings()._iDisplayLength) + 1;
                        if (cur_page == 1)
                            return;
                        wpDataTablesSelRows[tableDescription.tableId] = -2;
                        wpDataTablesUpdatingFlags[tableDescription.tableId] = true;
                        wpDataTables[tableDescription.tableId].fnPageChange('previous');
                    }
                    wpDataTables[tableDescription.tableId].checkSelectedLimits();
                });

                /**
                 * Next button in edit dialog
                 */
                $(document).on('click', tableDescription.selector + '_next_edit_dialog', function (e) {
                    e.preventDefault();
                    if (wpDataTables[tableDescription.tableId].fnSettings()._iDisplayLength == -1) {
                        wpDataTables[tableDescription.tableId].fnSettings()._iDisplayLength = wpDataTables[tableDescription.tableId].fnSettings()._iRecordsTotal
                    }
                    var sel_row_index = $(tableDescription.selector + ' > tbody > tr.selected').index();
                    if (sel_row_index < wpDataTables[tableDescription.tableId].fnSettings()._iDisplayLength - 1) {
                        $(tableDescription.selector + ' > tbody > tr.selected').removeClass('selected');
                        $(tableDescription.selector + ' > tbody > tr:eq(' + (sel_row_index + 1) + ')').addClass('selected', 300);
                        wpDataTablesSelRows[tableDescription.tableId] = wpDataTables[tableDescription.tableId].fnGetPosition($(tableDescription.selector + ' > tbody > tr.selected').get(0));
                        var data = wpDataTables[tableDescription.tableId].fnGetData(wpDataTablesSelRows[tableDescription.tableId]);
                        wpDataTablesFunctions[tableDescription.tableId].applyData(data);
                    } else {
                        var cur_page = Math.ceil(wpDataTables[tableDescription.tableId].fnSettings()._iDisplayStart / wpDataTables[tableDescription.tableId].fnSettings()._iDisplayLength) + 1;
                        var total_pages = Math.ceil(wpDataTables[tableDescription.tableId].fnSettings()._iRecordsTotal / wpDataTables[tableDescription.tableId].fnSettings()._iDisplayLength);
                        if (cur_page == total_pages)
                            return;
                        wpDataTablesSelRows[tableDescription.tableId] = -3;
                        wpDataTablesUpdatingFlags[tableDescription.tableId] = true;
                        wpDataTables[tableDescription.tableId].fnPageChange('next');
                        wpDataTables[tableDescription.tableId].fnDraw(false);
                    }
                    wpDataTables[tableDescription.tableId].checkSelectedLimits();
                });

                /**
                 * Apply button in edit dialog
                 */
                $(document).on('click', tableDescription.selector + '_apply_edit_dialog', function (e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    e.preventDefault();
                    wpDataTablesFunctions[tableDescription.tableId].saveTableData(true, false);
                });

                /**
                 * OK button in edit dialog
                 */
                $(document).on('click', tableDescription.selector + '_ok_edit_dialog', function (e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    e.preventDefault();
                    wpDataTablesFunctions[tableDescription.tableId].saveTableData(true, true);
                });

                /**
                 * Toggle OK when enter pressed in inputs (but not selectboxes or textareas)
                 */
                $(document).on('keyup', tableDescription.selector + '_edit_dialog input', function (e) {
                    if (e.which == 13) {
                        $(tableDescription.selector + '_ok_edit_dialog').click();
                    }
                });

                /**
                 * Apply maskmoney for Float column types and apply thousands separator and decimal places
                 * based on table description
                 */
                $(tableDescription.selector + '_edit_dialog input.wdt-maskmoney[data-column_type="float"]').each(function (i) {
                    var decimalPlaces = tableDescription.columnsDecimalPlaces[$(this).data('key')] != -1 ?
                        tableDescription.columnsDecimalPlaces[$(this).data('key')] :
                        parseInt(tableDescription.decimalPlaces);
                    $(this).maskMoney({
                        thousands: tableDescription.number_format == 1 ? '.' : ',',
                        decimal: tableDescription.number_format == 1 ? ',' : '.',
                        precision: decimalPlaces,
                        allowNegative: true,
                        allowEmpty: true,
                        allowZero: true
                    })
                });

                /**
                 * Apply maskmoney for Input column types
                 */
                $(tableDescription.selector + '_edit_dialog input.wdt-maskmoney[data-column_type="int"]').each(function (i) {
                    var thousandsSeparator = tableDescription.number_format == 1 ? '.' : ',';
                    if (tableDescription.columnsThousandsSeparator[$(this).data('key')] == 0) {
                        thousandsSeparator = '';
                    }
                    $(this).maskMoney({
                        thousands: thousandsSeparator,
                        precision: 0,
                        allowNegative: true,
                        allowEmpty: true,
                        allowZero: true
                    });
                });

                /**
                 * Apply fileuploaders
                 */
                var fileUploadInit = function (selector) {
                    if ($('.fileupload-' + selector).length) {

                        var attachment = null;
                        // Extend the wp.media object
                        wdtCustomUploader = wp.media({
                            title: wpdatatables_frontend_strings.select_upload_file,
                            button: {
                                text: wpdatatables_frontend_strings.choose_file
                            },
                            multiple: false
                        });


                        $('span.fileupload-' + selector).click(function (e) {
                            e.preventDefault();
                            var $button = $(this);
                            var $relInput = $('#' + $button.data('rel_input'));

                            wdtCustomUploader = wp.media({
                                title: wpdatatables_frontend_strings.select_upload_file,
                                button: {
                                    text: wpdatatables_frontend_strings.choose_file
                                },
                                multiple: false,
                                library: {
                                    type: $button.data('column_type') == 'icon' ? 'image' : ''
                                }
                            });
                            if ($button.data('column_type') == 'icon') {
                                wdtCustomUploader.off('select').on('select', function () {
                                    attachment = wdtCustomUploader.state().get('selection').first().toJSON();

                                    var val = attachment.url;

                                    $relInput.parent().parent().parent().find('.fileinput-preview').html('<img src=' + val + '>');
                                    $relInput.parent().parent().parent().removeClass('fileinput-new').addClass('fileinput-exists');
                                    $relInput.parent().parent().removeClass('fileinput-new').addClass('fileinput-exists');

                                    if (attachment.sizes.thumbnail) {
                                        val = attachment.sizes.thumbnail.url + '||' + val;
                                    }

                                    $relInput.val(val);
                                });
                            } else {
                                // For columns that are not image column type, grab the URL and set it as the text field's value
                                wdtCustomUploader.off('select').on('select', function () {
                                    var attachment = wdtCustomUploader.state().get('selection').first().toJSON();
                                    $relInput.val(attachment.url);
                                    $relInput.parent().parent().removeClass('fileinput-new').addClass('fileinput-exists');
                                    $relInput.parent().parent().find('.fileinput-filename').text(attachment.filename);
                                });
                            }
                            // Open the uploader dialog
                            wdtCustomUploader.open();


                        });
                    }
                };

                fileUploadInit(tableDescription.tableId);

                /**
                 * Show edit dialog
                 */
                $('.edit_table[aria-controls="' + tableDescription.tableId + '"]').click(function () {
                    var modal = $('#wdt-frontend-modal');

                    if ($(this).hasClass('disabled'))
                        return false;

                    $('.wpDataTablesPopover.editTools').hide();

                    modal.find('.modal-title').html('Edit entry');
                    modal.find('.modal-body').html('');

                    var row = $(tableDescription.selector + ' tr.selected').get(0);
                    var data = wpDataTables[tableDescription.tableId].fnGetData(row);
                    wpDataTablesFunctions[tableDescription.tableId].applyData(data);
                    wpDataTables[tableDescription.tableId].checkSelectedLimits();
                    modal.find('.modal-body').append($(tableDescription.selector + '_edit_dialog').show());
                    modal.find('.modal-footer').html('').append($(tableDescription.selector + '_edit_dialog_buttons').show());

                    $('#wdt-frontend-modal .editDialogInput').each(function (index) {
                        if ($(this).data('input_type') == 'mce-editor') {
                            if ($(this).siblings().length) {
                                tinymce.execCommand('mceRemoveEditor', true, $(this).attr('id'));
                            }
                            tinymce.init({
                                selector: '#' + $(this).attr('id'),
                                menubar: false
                            });
                        }
                    });

                    modal.modal('show');

                    // Show 'No inputs selected' alert
                    if (modal.find('.wdt-edit-dialog-fields-block').find('.form-group').length == 0)
                        $('#wdt-frontend-modal div.wdt-no-editor-inputs-selected-alert').show();

                });

                /**
                 * Init inline editing
                 */
                if (tableDescription.inlineEditing) {
                    new inlineEditClass(tableDescription, dataTableOptions, $);
                }

                /**
                 * Add new entry dialog
                 */
                $('.new_table_entry[aria-controls="' + tableDescription.tableId + '"]').click(function () {
                    var modal = $('#wdt-frontend-modal');

                    $('.wpDataTablesPopover.editTools').hide();

                    modal.find('.modal-title').html('Add new entry');
                    modal.find('.modal-body').html('')
                        .append($(tableDescription.selector + '_edit_dialog').show());
                    modal.find('.modal-footer').html('')
                        .append($(tableDescription.selector + '_edit_dialog_buttons').show());

                    $('#wdt-frontend-modal .editDialogInput').val('').css('border', '');
                    $('#wdt-frontend-modal tr.idRow .editDialogInput').val('0');

                    $('#wdt-frontend-modal .editDialogInput').each(function (index) {
                        if ($(this).data('input_type') == 'mce-editor') {
                            if (tinymce.activeEditor)
                                tinymce.activeEditor.setContent('');
                            tinymce.execCommand('mceRemoveEditor', true, $(this).attr('id'));
                            tinymce.init({
                                selector: '#' + $(this).attr('id'),
                                menubar: false
                            });
                        }
                    });

                    wpDataTables[tableDescription.tableId].checkSelectedLimits();

                    // Reset selectpickers values
                    $('#wdt-frontend-modal .selectpicker').selectpicker('deselectAll').selectpicker('refresh');

                    // Set the default values
                    for (var i in tableDescription.advancedEditingOptions.aoColumns) {
                        var defaultValue = tableDescription.advancedEditingOptions.aoColumns[i].defaultValue;
                        var editorInputType = tableDescription.advancedEditingOptions.aoColumns[i].editorInputType;
                        if (defaultValue) {
                            if ($.inArray(editorInputType, ['selectbox', 'multi-selectbox']) !== -1) {
                                defaultValue = editorInputType == 'multi-selectbox' ? defaultValue.split('|') : defaultValue;
                                $('#wdt-frontend-modal .editDialogInput:not(.bootstrap-select):eq(' + i + ')').selectpicker('val', defaultValue);
                            } else {
                                $('#wdt-frontend-modal .editDialogInput:not(.bootstrap-select):eq(' + i + ')').val(defaultValue);
                            }
                        }
                    }

                    // Reset attachment editor
                    if ($('.fileupload-' + tableDescription.tableId).length) {
                        var $fileUploadEl = $('.fileupload-' + tableDescription.tableId);
                        $($fileUploadEl).each(function () {
                            $(this).parent().find('input.editDialogInput').val('');
                            if ($(this).data('column_type') == 'icon') {
                                $(this).parent().parent().find('.fileinput-preview').html('');
                                $(this).parent().removeClass('fileinput-exists').addClass('fileinput-new');
                                $(this).parent().parent().removeClass('fileinput-exists').addClass('fileinput-new');
                            } else {
                                $(this).parent().find('.fileinput-filename').text('');
                                $(this).parent().removeClass('fileinput-exists').addClass('fileinput-new');
                            }
                        });
                    }

                    // Show 'No editor inputs selected' alert
                    if (modal.find('.wdt-edit-dialog-fields-block').find('.form-group').length == 0)
                        $('#wdt-frontend-modal div.wdt-no-editor-inputs-selected-alert').show();

                    modal.modal('show');

                });

                /**
                 * Hide modal dialog on Esc button
                 */
                $(document).on('keyup', '#wdt-frontend-modal', function (e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    if (e.which == 27) {
                        $('#wdt-frontend-modal').modal('hide');
                    }
                });

                /**
                 * When the hide instance method has been called append modal to related table
                 */
                $('#wdt-frontend-modal').on('hidden.bs.modal', function (e) {
                    $(tableDescription.selector + '_wrapper').append($(tableDescription.selector + '_edit_dialog').hide());
                    $(tableDescription.selector + '_wrapper').append($(tableDescription.selector + '_edit_dialog_buttons').hide());
                });

                /**
                 * Delete an entry dialog
                 */
                $('.delete_table_entry[aria-controls="' + tableDescription.tableId + '"]').click(function () {
                    if ($(this).hasClass('disabled')) {
                        return false;
                    }

                    $('.wpDataTablesPopover.editTools').hide();

                    $('#wdt-delete-modal').modal('show');

                    $('#wdt-browse-delete-button').click(function (e) {
                        e.preventDefault();
                        e.stopImmediatePropagation();

                        var row = $(tableDescription.selector + ' tr.selected').get(0);
                        var data = wpDataTables[tableDescription.tableId].fnGetData(row);
                        var id_val = data[tableDescription.idColumnIndex];
                        $.ajax({
                            url: tableDescription.adminAjaxBaseUrl,
                            type: 'POST',
                            data: {
                                action: 'wdt_delete_table_row',
                                id_key: tableDescription.idColumnKey,
                                id_val: id_val,
                                table_id: tableDescription.tableWpId,
                                wdtNonce: $('#wdtNonceFrontendEdit').val()
                            },
                            success: function () {
                                wpDataTables[tableDescription.tableId].fnDraw(false);
                                $('#wdt-delete-modal').modal('hide');
                                wdtNotify(wpdatatables_edit_strings.success, wpdatatables_edit_strings.rowDeleted, 'success')
                            }
                        });
                    });
                });

                /**
                 * Add a popover that includes edit elements
                 */
                if (tableDescription.popoverTools) {
                    $(tableDescription.selector + '_wrapper').css('position', 'relative');
                    $('<div class="wpDataTablesPopover editTools ' + tableDescription.tableId + '"></div>').prependTo(tableDescription.selector + '_wrapper').hide();
                    $('.new_table_entry[aria-controls="' + tableDescription.tableId + '"]').prependTo(tableDescription.selector + '_wrapper .wpDataTablesPopover.editTools').css('float', 'right');
                    $('.edit_table[aria-controls="' + tableDescription.tableId + '"]').prependTo(tableDescription.selector + '_wrapper .wpDataTablesPopover.editTools').css('float', 'right');
                    $('.delete_table_entry[aria-controls="' + tableDescription.tableId + '"]').prependTo(tableDescription.selector + '_wrapper .wpDataTablesPopover.editTools').css('float', 'right');
                }

                /**
                 * Select table row on click
                 * @param e
                 * @returns {boolean}
                 */
                var clickEvent = function (e) {

                    // Fix if td is URL Link
                    if (!$(e.target).is('a')){
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        e.preventDefault();
                    }

                    if ($(this).hasClass('group')) {
                        return false;
                    }
                    // Set controls popover position
                    var popoverVerticalPosition = $(this).offset().top - $(tableDescription.selector + '_wrapper').offset().top - $('.wpDataTablesPopover.editTools').outerHeight() - 7;
                    // Check a cell is edited
                    var editedRow = ($(this).children('').hasClass('editing')) ? true : false;

                    if ($(this).hasClass('selected')) {
                        $(tableDescription.selector + ' tbody tr').removeClass('selected');
                        wpDataTablesSelRows[tableDescription.tableId] = -1;
                    } else if (!$(this).find('td').hasClass('dataTables_empty') || tableDescription.popoverTools) {
                        $(tableDescription.selector + '  tbody tr').removeClass('selected');
                        $(this).addClass('selected');
                        wpDataTablesSelRows[tableDescription.tableId] = wpDataTables[tableDescription.tableId].fnGetPosition($(tableDescription.selector + ' tbody tr.selected').get(0));
                    }
                    if ($(tableDescription.selector + ' tbody tr.selected').length > 0) {
                        $('.edit_table[aria-controls="' + tableDescription.tableId + '"]').removeClass('disabled');
                        $('.delete_table_entry[aria-controls="' + tableDescription.tableId + '"]').removeClass('disabled');
                        if (!editedRow) {
                            $('.wpDataTablesPopover.editTools.' + tableDescription.tableId + '').show().css('top', popoverVerticalPosition);
                        } else {
                            return false;
                        }
                    } else {
                        $('.edit_table[aria-controls="' + tableDescription.tableId + '"]').addClass('disabled');
                        $('.delete_table_entry[aria-controls="' + tableDescription.tableId + '"]').addClass('disabled');
                        $('.wpDataTablesPopover.editTools.' + tableDescription.tableId + '').hide();

                    }
                };

                var ua = navigator.userAgent,
                    event = (ua.match(/iPad/i)) ? "touchstart" : "click";

                $(document).off(event, tableDescription.selector + ' tbody tr').on(event, tableDescription.selector + ' tbody tr', clickEvent);

                /**
                 * Detached the chosen attachment
                 */
                $(document).on('click', tableDescription.selector + '_edit_dialog a.wdt-detach-attachment-file, a.wdt-detach-attachment-file', function (e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    if ($(this).parent().find('span.fileupload-' + tableDescription.tableId).data('column_type') == 'icon') {
                        $(this).parent().find('input.editDialogInput').val('');
                        $(this).parent().parent().find('.fileinput-preview').html('');
                        $(this).parents('.fileinput-exists').removeClass('fileinput-exists').addClass('fileinput-new');
                    } else {
                        $(this).parent().find('input.editDialogInput').val('');
                        $(this).parent().find('.fileinput-filename').text('');
                        $(this).parent().removeClass('fileinput-exists').addClass('fileinput-new');
                    }
                });

            }

            /**
             * Show the filter box if enabled in the widget if it is present
             */
            if (tableDescription.externalFilter == true) {
                if ($('#wdt-filter-widget').length) {
                    $('.wpDataTablesFilter').appendTo('#wdt-filter-widget');
                }
            }
            //[<--/ Full version -->]//

            return wpDataTables[tableDescription.tableId];

        };

        /**
         * Loop through all tables on the page and render the wpDataTables elements
         */
        $('table.wpDataTable').each(function () {
            var tableDescription = $.parseJSON($('#' + $(this).data('described-by')).val());
            wdtRenderDataTable($(this), tableDescription);
        });

    });

})(jQuery);

/**
 * Apply cell action for conditional formatting rule
 *
 * @param $cell
 * @param action
 * @param setVal
 */
function wdtApplyCellAction($cell, action, setVal) {
    switch (action) {
        case 'setCellColor':
            $cell.attr('style', 'background-color: ' + setVal + ' !important');
            break;
        case 'defaultCellColor':
            $cell.attr('style', 'background-color: "" !important');
            break;
        case 'setCellContent':
            $cell.html(setVal);
            break;
        case 'setCellClass':
            $cell.addClass(setVal);
            break;
        case 'removeCellClass':
            $cell.removeClass(setVal);
            break;
        case 'setRowColor':
            $cell.closest('tr').find('td').attr('style', 'background-color: ' + setVal + ' !important');
            break;
        case 'defaultRowColor':
            $cell.closest('tr').find('td').attr('style', 'background-color: "" !important');
            break;
        case 'setRowClass':
            $cell.closest('tr').addClass(setVal);
            break;
        case 'addColumnClass':
            var index = $cell.index() + 1;
            $cell
                .closest('table.wpDataTable')
                .find('tbody td:nth-child(' + index + ')')
                .addClass(setVal);
            break;
        case 'setColumnColor':
            var index = $cell.index() + 1;
            $cell
                .closest('table.wpDataTable')
                .find('tbody td:nth-child(' + index + ')')
                .attr('style', 'background-color: ' + setVal + ' !important');
            break;
    }
}

function wdtDialog(str, title) {
    var dialogId = Math.floor((Math.random() * 1000) + 1);
    var editModal = jQuery('.wdt-frontend-modal').clone();

    editModal.attr('id', 'remodal-' + dialogId);
    editModal.find('.modal-title').html(title);
    editModal.find('.modal-header').append(str);

    return editModal;
}

function wdtAddOverlay(table_selector) {
    jQuery(table_selector).addClass('overlayed');
}

function wdtRemoveOverlay(table_selector) {
    jQuery(table_selector).removeClass('overlayed');
}

//[<-- Full version -->]//
/**
 * Function that attach event on clear filters button
 */
function wdtAttachClearFiltersEvent() {
    jQuery('.wdt-clear-filters-button, .wdt-clear-all-filters-button').click(function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        e.preventDefault();

        var button = jQuery(e.target);
        if (button.is('.wdt-clear-all-filters-button')) {
            jQuery('.filter_column input:text').val('');
            jQuery('.filter_column select').selectpicker('val', '');
            jQuery('.filter_column input:checkbox').removeAttr('checked');
            jQuery('.wdtFilterDialog input:checkbox').removeAttr('checked');

            for (var i in wpDataTables) {
                wpDataTables[i].fnFilterClear();
            }
        } else {
            jQuery(this).closest('.wpDataTables').find('.filter_column input:text').val('');
            jQuery(this).closest('.wpDataTables').find('.filter_column select').selectpicker('val', '');
            jQuery(this).closest('.wpDataTables').find('.filter_column input:checkbox').removeAttr('checked');
            jQuery(this).closest('.wpDataTables').find('.wdtFilterDialog input:checkbox').removeAttr('checked');
            var selecter = '';
            if (jQuery(this).parent().is('#wdt-clear-filters-button-block')) {
                selecter = jQuery(this).closest('.wpdt-c').find('table.wpDataTable').prop('id');
            } else {
                selecter = jQuery(this).closest('.wpDataTablesWrapper').find('table.wpDataTable').prop('id');
            }
            wpDataTables[selecter].fnFilterClear();
        }
    })
}
//[<--/ Full version -->]//

/**
 * Get cell value cleared from neighbour html tags
 * @param element
 * @param responsive
 * @returns {*}
 */
function getPurifiedValue(element, responsive) {
    if (responsive) {
        var cellVal = element.children('.columnValue').html();
    } else {
        cellVal = element.clone().children().remove().end().html();
    }

    return cellVal;
}

/**
 * Conditional formatting
 * @param conditionalFormattingRules
 * @param params
 * @param element
 * @param responsive
 */
function wdtCheckConditionalFormatting(conditionalFormattingRules, params, element, responsive) {

    var cellVal = '';
    var ruleVal = '';
    var ruleMatched = false;
    if (( params.columnType == 'int' ) || ( params.columnType == 'float' )) {
        // Process numeric comparison
        cellVal = parseFloat(wdtUnformatNumber(getPurifiedValue(element, responsive), params.thousandsSeparator, params.decimalSeparator, true))
        ruleVal = conditionalFormattingRules.cellVal;
    } else if (params.columnType == 'date') {
        cellVal = moment(getPurifiedValue(element, responsive), params.momentDateFormat).toDate();
        if (conditionalFormattingRules.cellVal == '%TODAY%') {
            ruleVal = moment().startOf('day').toDate();
        } else {
            ruleVal = moment(conditionalFormattingRules.cellVal, params.momentDateFormat).toDate();
        }
    } else if (params.columnType == 'datetime') {
        if (conditionalFormattingRules.cellVal == '%TODAY%') {
            cellVal = moment(getPurifiedValue(element, responsive), params.momentDateFormat + ' ' + params.momentTimeFormat).startOf('day').toDate();
            ruleVal = moment().startOf('day').toDate();
        } else {
            cellVal = moment(getPurifiedValue(element, responsive), params.momentDateFormat + ' ' + params.momentTimeFormat).toDate();
            ruleVal = moment(conditionalFormattingRules.cellVal, params.momentDateFormat + ' ' + params.momentTimeFormat).toDate();
        }
    } else if (params.columnType == 'time') {
        cellVal = moment(getPurifiedValue(element, responsive), params.momentTimeFormat).toDate();
        ruleVal = moment(conditionalFormattingRules.cellVal, params.momentTimeFormat).toDate();
    } else {
        // Process string comparison
        cellVal = getPurifiedValue(element, responsive);
        ruleVal = conditionalFormattingRules.cellVal;
    }

    switch (conditionalFormattingRules.ifClause) {
        case 'lt':
            ruleMatched = cellVal < ruleVal;
            break;
        case 'lteq':
            ruleMatched = cellVal <= ruleVal;
            break;
        case 'eq':
            if (params.columnType == 'date'
                || params.columnType == 'datetime'
                || params.columnType == 'time') {
                cellVal = cellVal != null ? cellVal.getTime() : null;
                ruleVal = ruleVal != null ? ruleVal.getTime() : null;
            }
            ruleMatched = cellVal == ruleVal;
            break;
        case 'neq':
            if (params.columnType == 'date' || params.columnType == 'datetime') {
                cellVal = cellVal != null ? cellVal.getTime() : null;
                ruleVal = ruleVal != null ? ruleVal.getTime() : null;
            }
            ruleMatched = cellVal != ruleVal;
            break;
        case 'gteq':
            ruleMatched = cellVal >= ruleVal;
            break;
        case 'gt':
            ruleMatched = cellVal > ruleVal;
            break;
        case 'contains':
            ruleMatched = cellVal.indexOf(ruleVal) !== -1;
            break;
        case 'contains_not':
            ruleMatched = cellVal.indexOf(ruleVal) == -1;
            break;
    }

    if (ruleMatched) {
        wdtApplyCellAction(element, conditionalFormattingRules.action, conditionalFormattingRules.setVal);
    }
}

jQuery.fn.dataTableExt.oStdClasses.sWrapper = "wpDataTables wpDataTablesWrapper";
jQuery.fn.dataTable.ext.classes.sLengthSelect = 'selectpicker length_menu';
jQuery.fn.dataTable.ext.classes.sFilterInput = 'form-control';
