var aGridExt = {
    renderBadge: function(value, type) {
        return `<span class="badge badge-${type}">${value}</span>`;
    },
    renderYesNo: function(value) {
        return value ? this.renderBadge('Yes', 'success') : this.renderBadge('Yes', 'secondary');
    },
    defaultRowActions: function(params) {
        var idKey = (typeof params.idKey !== 'undefined') ? params.idKey : 'id';
        var hasUpdate = (typeof params.update === 'undefined' || params.update) ? true : false;
        var hasDestroy = (typeof params.destroy === 'undefined' || params.destroy) ? true : false;

        var actions = [];
        if (hasUpdate) {
            actions.push({
                permission: 'update',
                icon: 'fa-edit',
                btn: 'info',
                url: function (column) {
                    let url = params.baseUrl + '/' + column[idKey] + '/edit';
                    return url.replace('//', '/');
                }
            });
        }

        if (hasDestroy) {
            actions.push({
                permission: 'destroy',
                action: 'ajax-delete',
                icon: 'ajax',
                confirm: 'Удалить?',
                icon: 'fa-trash-alt',
                btn: 'danger',
                url: function (column) {
                    let url = params.baseUrl + '/' + column[idKey];
                    return url.replace('//', '/');
                }
            });
        }

        return actions;
    },
    defaultBulkActions: function(params) {
        return [
            {
                permission: 'update',
                type: 'button',
                class: 'btn-success btn-sm',
                label: 'Activate',
                action: 'bulkToggle',
                attribute: 'is_active',
                value: 1,
                url: params.baseUrl + '/bulk-toggle'
            },
            {
                permission: 'update',
                type: 'button',
                class: 'btn-secondary btn-sm',
                label: 'Deactivate',
                action: 'bulkToggle',
                attribute: 'is_active',
                value: 0,
                url: params.baseUrl + '/bulk-toggle'
            },
            {
                permission: 'destroy',
                type: 'button',
                class: 'btn-danger btn-sm',
                confirm: 'Вы действительно хотите удалить?',
                label: 'Remove',
                action: 'bulkDestroy',
                url: params.baseUrl + '/bulk-destroy'
            }
        ];
    }
};

function bulkDestroy(params) {
    $.ajax({
        type: 'DELETE',
        url: params.url,
        data: {
            ids: params.ids,
        },
        headers: {
            'X-CSRF-TOKEN': app.getCsrfToken()
        },
        success: function (data) {
            params.callback();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }
    })
}

function bulkToggle(params) {
    $.ajax({
        type: 'PUT',
        url: params.url,
        data: {
            ids: params.ids,
            attribute: params.attribute,
            value: params.value
        },
        headers: {
            'X-CSRF-TOKEN': app.getCsrfToken()
        },
        success: function (data) {
            params.callback();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }
    })
}

(function ($) {
    'use strict';

    var aGridDefaults = {
        urls: null,
        permissions: {
            update: true,
            destroy: true,
        },
        events: {},
        opacity: '0.6',
        notResultsEmpty: true,
        filterForm: false,
        actions: [],
        bulkForms: [],
        bulkActions: [],
        columns: [],
        additionRow: false,
        selectable: false,
        items: [],
        sort: {
            dir: 'desc',
            attr: 'id',
        },
        count: 0,
        perPage: 20,
        page: 1,
        queryParams: [],
        perPageSelector: true,
        perPageData: [20, 50, 100],
        theadPanelCols: {
            pager: 'col-sm-3',
            actions: 'col-sm-6',
            filter: 'col-sm-3'
        }
    };

    function aGrid(el, options) {

        var opts = $.extend({}, aGridDefaults, options);
        var $aGrid = $(el);
        sendRequest();

        function buildUrl() {
            var urlDel = (opts.url.split("?").length - 1) ? '&' : '?';

            var url = opts.url + urlDel + 'sort_dir=' + opts.sort.dir + '&sort_attr=' + opts.sort.attr;

            $.each(opts.queryParams, function(i, param) {
                url += '&' + param;
            })

            if (opts.page > 1) {
                url += '&page=' + opts.page;
            }

            if (opts.perPage) {
                url += '&per_page=' + opts.perPage;
            }

            if (opts.filterForm !== false) {
                url +=  '&' + $(opts.filterForm).serialize();
            }

            $('.ag-filter-input').each(function(){
                url +=  '&' + $(this).attr('name') + '=' + $(this).val();
            })

            return url.replace('//', '/');
        }

        function hasFilterPanel() {
            return (typeof opts.filterPanel !== 'undefined') && (typeof opts.filterPanel.inputs !== 'undefined');
        }

        function sendRequest() {
            if (typeof opts.events.beforeSendRequest === 'function') {
                opts.events.beforeSendRequest(opts);
            }

            $aGrid.css({opacity: opts.opacity});

            $.ajax({
                method: 'GET',
                url: buildUrl(),
            })
            .done(function (response) {
                renderTable(response);
                toggleTheadPanel();
                $aGrid.css({opacity: '1'});
            })
        }

        function getSortingClass(name) {
            var cls = 'sorting';
            if (name === opts.sort.attr) {
                cls += '_' + opts.sort.dir;
            }

            return cls;
        }

        function toogleDisabledBulkForms(value) {
            $.each(opts.bulkForms, function(i, formSelector) {
                var form = $(formSelector);
                $('[type=submit]', form).prop('disabled', value);
            })
        }

        function attachIdsToBulkForms() {
            var ids = getSelectedRowsIds();
            $.each(opts.bulkForms, function(i, formSelector) {
                var form = $(formSelector);
                $('.js-bulk-id', form).remove();
                $.each(ids, function(j, id){
                    form.append('<input type="hidden" name="ids[]" class="js-bulk-id" value="' + id + '">');
                })
            })
        }

        function renderTable(data) {
            $('.ag-check-all').prop('checked', false);

            renderThead(data);
            renderTbody(data);
            renderTfoot(data);
            if (typeof opts.events.afterRender === 'function') {
                opts.events.afterRender(opts, data);
            }
        }


        function renderTbody(data) {
            $('tbody', $aGrid).remove();

            if (data.count) {
                var tbody = $aGrid.append('<tbody></tbody>');
                $.each(data.items, function(i, item) {
                    var trClass = "ag-main-row";
                    if (opts.additionRow !== false) {
                        trClass += ' ag-has-addition-row';
                    }

                    var tr = $('<tr class="' + trClass + '"></tr>');
                    if (opts.selectable) {
                        tr.append('<td><input type="checkbox" class="ag-checkbox-row" value="' + item.id + '"></td>');
                    }
                    $.each(opts.columns, function(k, column) {
                        if (typeof item[column.name] !== undefined && isColumnAvailable(column)) {
                            var tdValue = (typeof column.render === 'function') ? column.render(item) : item[column.name];
                            if (tdValue === null) {
                                tdValue = '';
                            }
                            tr.append('<td class="js-' + column.name + '" data-name="' + column.name + '">' + tdValue + '</td>');
                        }
                    })
                    tbody.append(tr);

                    // render row actions
                    if (typeof opts.rowActions === 'object') {
                        var rowActionsTD = $('<td></td>');
                        $.each(opts.rowActions, function(i, rowAction){
                            if (opts.permissions[rowAction.permission]) {
                                var href = '#';
                                var onClickClass = 'ag-action';
                                if (typeof rowAction.url !== 'undefined') {
                                    href = (typeof rowAction.url === 'function') ? rowAction.url(item) : rowAction.url;
                                }
                                if (typeof rowAction.onClickClass !== 'undefined') {
                                    onClickClass = rowAction.onClickClass;
                                }

                                var rowActionBtn = $(`<a class="ml-1 btn btn-xs btn-${rowAction.btn} ${onClickClass}" href="${href}">
                                                        <i class="fas ${rowAction.icon}"></i>
                                                    </a>`);

                                if (typeof rowAction.action !== 'undefined') {
                                    rowActionBtn.addClass('js-action-' + rowAction.action);
                                }

                                rowActionsTD.append(rowActionBtn);
                            }
                        })
                        tr.append(rowActionsTD);
                    }

                    // render collspace row
                    if (typeof opts.additionRow.type !== 'undefined') {
                        if (opts.additionRow.type === 'render' && typeof opts.additionRow.render === 'function') {
                            tbody.append(
                                `<tr class="ag-addition-row ag-hidden">
                                    <td id="addition-row-content-${item.id}" colspan="${getCountColumns()}">${opts.additionRow.render(item)}</td>
                                </tr>`
                            );
                        } else if (opts.additionRow.type === 'ajax' && typeof opts.additionRow.url === 'function') {
                            var url = opts.additionRow.url(item);
                            tbody.append(
                                `<tr class="ag-addition-row ag-hidden">
                                    <td class="js-content" colspan="${getCountColumns()}" data-url="${url}"></td>
                                </tr>`
                            );
                        }
                    }
                })
            }
        }

        function renderThead(data) {

            if (!$('thead', $aGrid).length) {
                var theadHtml = '<thead>';
                theadHtml += renderTheadPanel(data);
                theadHtml += '</thead>';
                $aGrid.html(theadHtml)
                $('thead', $aGrid).append(renderTheadAttributes());
                $('thead', $aGrid).append(renderTheadFilter());
                //$('select.select2', $aGrid).select2();
            } else {
                $('.js-pager-summary', $aGrid).html(getPagerSummary(data));
            }

            //$('.js-pager-select', $aGrid).show();
            //$('.js-bulk-action', $aGrid).show();
        }

        function renderTheadAttributes() {
            var html = '<tr class="js-tr-attributes">';
            if (opts.selectable ) {
                html += '<th><input type="checkbox" class="ag-check-all"></th>';
            }

            $.each(opts.columns, function(i, column) {
                if (isColumnAvailable(column)) {
                    var thClass = '';

                    if (column.sortable !== false) {
                        thClass = 'ag-sortable ' + getSortingClass(column.name);
                    }

                    html += '<th class="' + thClass + '" data-name="' + column.name + '">' + getColumnLabel(column) + '</th>';
                }
            })

            if (typeof opts.rowActions === 'object') {
                html += '<th></th>';
            }
            html += '</tr>';

            return html;
        }

        function renderTheadFilter() {
            var html = '<tr class="js-tr-filter">';
            if (opts.selectable ) {
                html += '<th></th>';
            }

            $.each(opts.columns, function(i, column) {
                if (isColumnAvailable(column)) {
                    html += '<th class="ag-filter" data-name="' + column.name + '">' + getColumnFilter(column) + '</th>';
                }
            })

            if (typeof opts.rowActions === 'object') {
                html += '<th></th>';
            }
            html += '</tr>';

            return html;
        }

        $(document).on('keypress', '.ag-filter-input', function(e) {
            if(e.which == 13) {
                sendRequest();
            }
        });
        $(document).on('change', '.ag-filter-input', function(e) {
            sendRequest();
        });

        function getColumnFilter(column) {
            var html = '';
            var formControlClass = 'form-control form-control-sm';
            if (column.filter !== false) {
                var type = (typeof column.filter !== 'undefined' && typeof column.filter.type !== 'undefined') ?
                    column.filter.type : 'text';

                if (type === 'text') {
                    html = '<input class="ag-filter-input ' + formControlClass + '" name="' + column.name + '" type="text">';
                } else if (type === 'select') {
                    var options = (typeof column.filter.options !== 'undefined') ?
                        column.filter.options : {1:'Yes', 0:'No'};

                    html = '<select class="ag-filter-input ' + formControlClass + '" name="' + column.name + '">';
                    html+= '<option value="">-</option>';
                    for (const [key, value] of Object.entries(options)) {
                        html+= '<option value="'+key+'">'+ value +'</option>';
                    }
                    html+= '</select>';
                }
            }

            return html;
        }

        function getPagerSummary(data) {
            if (getCountPage(data) <= 1) {
                return '';
            }

            return data.count ?
                `<label class="ml-5">Showing&nbsp;${data.from}-${data.to} of ${data.count}` :
                `No data ...`;
        }

        function renderTfoot(data) {

            $('tfoot', $aGrid).remove();

            if (getCountPage(data) > 1) {
                var tfoot = '<tfoot><tr><td colspan="' + getCountColumns() + '">';
                tfoot += renderPaginate(data);
                tfoot += '</td></tr></tfoot>';
                $aGrid.append(tfoot);
            }
        }

        function renderTheadPanel(data) {
            // render header panel
            var theadPanel = '<tr><td colspan="' + getCountColumns() + '"><div class="row">';

            theadPanel += '<div class="' + opts.theadPanelCols.pager + '">';

            if (opts.perPageSelector) {
                theadPanel += '<label class="ml-2 js-pager-select">';
                theadPanel += 'Per page: <select name="per_page" class="form-control-sm">';
                $.each(opts.perPageData, function(i, val){
                    var selected = val == opts.perPage ? 'selected="selected"' : '';
                    theadPanel += '<option value="' + val + '" ' + selected + '>' + val + '</option>';
                })
                theadPanel += '</select></label>';
            }

            theadPanel += '<label class="ml-5 js-pager-summary">' + getPagerSummary(data) + '<label>';
            theadPanel += '</div>';

            if (typeof opts.bulkActions === 'object') {
                theadPanel += bulkActions();
            }

            if (hasFilterPanel()) {
                theadPanel += filterPanel();
            }

            theadPanel += '</div></td></tr>';

            return theadPanel;
        }

        function getCountColumns() {
            var count = 0;

            $.each(opts.columns, function(k, column) {
                if (isColumnAvailable(column)) {
                    count++;
                }
            })

            if (opts.selectable) {
                count++;
            }
            if (typeof opts.rowActions === 'object') {
                count++;
            }
            return count;
        }

        function isColumnAvailable(column) {
            return column.available === undefined || column.available();
        }

        function getColumnLabel(column) {
            if (typeof column.label === 'function') {
                return column.label();
            } else {
                return column.label;
            }
        }

        function getCountPage(data) {
            var countPage = 0;

            if (opts.perPage) {
                countPage = (data.count / opts.perPage);
                countPage = parseInt(countPage);
                if (data.count % opts.perPage !== 0) {
                   countPage++;
                }
            }
            return countPage;
        }

        function renderPaginate(data) {

            var html = '';
            var countPage = getCountPage(data);

            if (countPage > 1 && opts.page <= countPage) {
                html += '<ul class="float-sm-right pagination">';

                var startPage = (opts.page > 2) ? opts.page - 2 : 1;
                var endPage = startPage + 4;
                if (endPage - countPage > 0) {
                    startPage = startPage - (endPage - countPage);
                }

                if (opts.page > 3) {
                    html += createPagerLink('page-item', 'page-link', 1, 1);
                }

                if (startPage > 2) {
                    html += '<li class="page-item ml-1 mr-1">...</li>';
                }

                for (var i = 1; i <= countPage; i++) {
                    if ((i >= startPage && i <= endPage) || opts.page == i) {
                        var li_class = 'page-item';
                        var a_class = 'page-link';
                        if (i == opts.page) {
                            li_class += ' active';
                        }
                        html += createPagerLink(li_class, a_class, i, i);
                    }
                }

                if (countPage > endPage) {
                    html += '<li class="page-item ml-1 mr-1">...</li>';
                }


                if (opts.page < countPage - 2) {
                    html += createPagerLink('page-item', 'page-link', countPage, countPage);
                }

                html += '</ul>';
            }

            return html;
        }

        function createPagerLink(li_class, link_class, page, label, is_url = true) {
            var html = '';
            if (is_url) {
                html = '<li class="' + li_class + '"><a class="' + link_class + '" href="#" data-page="' + page + '">' + label + '</a></li>';
            } else {
                html = '<li class="' + li_class + '">' + label + '</li>';
            }

            return html;
        }

        function bulkActions() {
            var html = '<div class="' + opts.theadPanelCols.actions + '">';

            $.each(opts.bulkActions, function(i, bulkAction) {

                if (opts.permissions[bulkAction.permission]) {

                    if (bulkAction.type === 'button') {

                        html += '<button ' +
                                    'class="js-bulk-action btn ' + bulkAction.class + ' ml-5" ' +
                                    attr(bulkAction, 'url', 'data-url') +
                                    attr(bulkAction, 'attribute', 'data-attribute') +
                                    attr(bulkAction, 'value', 'data-value') +
                                    attr(bulkAction, 'label', 'data-label') +
                                    attr(bulkAction, 'action', 'data-action')  +
                                    attr(bulkAction, 'confirm', 'data-confirm') +
                                '>' + bulkAction.label + '</button>';

                    } else if (bulkAction.type === 'select') {
                        var selectClass = 'ag-panel-select form-control-sm';
                        if (typeof bulkAction.class !== 'undefined') {
                            selectClass += ' ' + bulkAction.class;
                        }
                        html += '<label class="ml-5">' + bulkAction.label + ' <select class="js-bulk-action ' + selectClass + '" data-url="' + bulkAction.url + '" data-action="' + bulkAction.action + '">';
                        html += '<option value="">-</option>';
                        $.each(bulkAction.options, function(o_id, o_val) {
                            html += '<option value="' + o_id + '">' + o_val + '</option>';
                        })
                        html += '</select></label>';
                    }
                }
            })
            html += '</div>';

            return html;
        }

        function filterPanel() {
            var conf = opts.filterPanel;

            var html = '<div class="' + opts.theadPanelCols.filter + '"><form id="' + conf.formId + '">';

            $.each(conf.inputs, function(i, input) {
                if (input.type === 'text') {
                    html += '<input class="form-control"' +
                            attr(input, 'type') + ' ' +
                            attr(input, 'id') + ' ' +
                            attr(input, 'id', 'name') + ' ' +
                            attr(input, 'placeholder') + ' ' +
                    ' >';

                } else if (panelItem.type === 'select') {

                } else if (panelItem.type === 'submit') {

                }
            })

            html += '</div></form>';

            return html;
        }

        function attr(obj, k, attrName = false) {
            var val = '';
            if (typeof obj === 'object' && typeof obj[k] !== 'undefined') {
                attrName = attrName ? attrName : k;

                return attrName + '="' + obj[k] + '"';
            }
            return val;
        }

        function formSerialize(form) {
            var items = form.serializeArray();
            var data = [];
            $.each(items, function(i, item) {
                data.push(item.name + '=' + item.value);
            })
            return data;
        }

        function toggleTheadPanel(){
            var disabled = (getSelectedRowsIds().length === 0) ? true : false;
            $('.js-bulk-action, .ag-panel-select').each(function(){
                $(this).prop('disabled', disabled);
            })
            toogleDisabledBulkForms(disabled);
            attachIdsToBulkForms();
        }

        function getSelectedRowsIds() {
            var ids = [];
            $.each($(".ag-checkbox-row:checked"), function(){
                ids.push(parseInt($(this).val()));
            });
            return ids;
        }

        $aGrid.on('change', '.ag-panel-select', function(){
            var val = $(this).val();
            if (val != '') {
                if (typeof $(this).data('action') !== 'undefined') {
                    window[$(this).data('action')](getSelectedRowsIds(), val, sendRequest);
                }
            }
        })

        $aGrid.on('click', '.js-bulk-action', function(){
            var self = $(this);

            if (typeof self.data('confirm') !== 'undefined') {
                if (!confirm(self.data('confirm'))) {
                    return;
                }
            }

            $aGrid.css({opacity: opts.opacity});

            if (self.data('action') === 'bulkDestroy') {
                window[self.data('action')]({
                    url: self.data('url'),
                    ids: getSelectedRowsIds(),
                    callback: sendRequest
                });
            }
            if (self.data('action') === 'bulkToggle') {
                window[self.data('action')]({
                    url: self.data('url'),
                    ids: getSelectedRowsIds(),
                    attribute: self.data('attribute'),
                    value: self.data('value'),
                    callback: sendRequest
                });
            }
        })

        $aGrid.on('click', '.ag-has-addition-row', function(){
            var tdContent = $('.js-content', $(this).next());

            if ($(this).next().hasClass('ag-hidden') && tdContent.html() === '') {
                $.ajax({
                    async: false,
                    url: tdContent.data('url'),
                    headers: {
                        'X-CSRF-TOKEN': window._token
                    }
                })
                .done(function (response) {
                    tdContent.html(response);
                })

            }
            $(this).next().toggleClass('ag-hidden');
        })

        $aGrid.on('click', '.addition-row-toggle', function(){
            $(this).closest('tr').addClass('ag-hidden');
        })

        $aGrid.on('change', '.ag-check-all', function(){
            $('.ag-checkbox-row', $aGrid).prop('checked', $(this).is(':checked'));
            toggleTheadPanel();
        })

        $aGrid.on('click', '.ag-checkbox-row', function(e){
            e.stopPropagation();
            $('.ag-check-all').prop('checked', !$('.ag-checkbox-row:checkbox:not(":checked")', $(this).closest('table')).length);
            toggleTheadPanel();
        })

        $aGrid.on('click', '.page-link', function(e){
            e.preventDefault();
            opts.page = $(this).data('page');
            sendRequest();
        })

        $aGrid.on('change', 'select[name="per_page"]', function(){
            opts.page = 1;
            opts.perPage = $(this).val();
            sendRequest();
        })

        $aGrid.on('click', '.ag-action', function(e){
            e.stopPropagation();
        })

        $aGrid.on('click', '.js-action-ajax-delete', function(e){
            e.preventDefault();
            e.stopPropagation();

            var self = $(this);
            if (!confirm('Удалить?')) {
                return;
            }

            $.ajax({
                url: self.attr('href'),
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': app.getCsrfToken()
                }
            })
            .done(function (response) {
                var tr = self.closest('tr');

                if (opts.additionRow === true) {
                    tr.next().remove();
                }
                tr.remove();
            })
        })

        $aGrid.on('click', '.ag-sortable', function(){
            var dir;
            var cls = $.trim($(this).attr('class').replace('ag-sortable', ''));
            $('.ag-sortable').removeClass(cls).addClass('sorting');

            if (opts.sort.attr == $(this).data('name')) {
                dir = (opts.sort.dir === 'desc') ? 'asc' : 'desc';
            } else {
                dir = 'asc';
            }
            $(this).addClass('sorting_' + dir);

            opts.page = 1;
            opts.sort.attr = $(this).data('name');
            opts.sort.dir = dir;

            sendRequest();
        })

        if (opts.filterForm !== false) {

            $('body').on('submit', opts.filterForm, function(e){
                e.preventDefault();
                if (typeof opts.events.beforeSubmitFilterForm !== 'undefined') {
                    opts.events.beforeSubmitFilterForm($(this));
                }
                opts.page = 1;
                opts.queryParams = formSerialize($(this));
                sendRequest();
            })
        }
    }

    // The actual plugin
    $.fn.aGrid = function (options) {
        if (this.length) {
            this.each(function () {
                aGrid(this, options);
            });
        }
        return this;
    };

})(jQuery);
