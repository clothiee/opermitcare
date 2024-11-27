export class LayoutSettingsModule {
    constructor(configuration = {}) {
        this.configuration = configuration;
    };

    initialize() {
        let configuration = JSON.parse(this.configuration);
        let addEventListener = function () {
            switch (configuration.pageName) {
                case 'index':
                    Layout.window.init();
                    break;
                case 'faq':
                    $(document).on('click', '.js-see-content', function () {
                        const contentId = $(this).data('id');
                        $('.js-see-content, .faq__detail-item').removeClass('active');
                        $(`[data-id="${contentId}"]`).addClass('active');
                    });

                    Layout.window.init();
                    break;
                case 'login':
                case 'sign-up':
                    $(document).on('click', '.login__input svg', function () {
                        let targetParent = $(this).closest('.login__input');

                        if (targetParent.hasClass('login__input--show')) {
                            targetParent.find('input').attr('type', 'password');
                            return targetParent.removeClass('login__input--show');
                        }

                        targetParent.find('input').attr('type', 'text');
                        return targetParent.addClass('login__input--show');
                    });

                    Layout.window.init();
                    break;
                case 'dashboard':
                    Dashboard.ticket.refresh.clear();

                    $(document).on('click', '.js-show-password', function () {
                        let targetParent = $(this).closest('.dashboard__input-group');

                        if (targetParent.hasClass('dashboard__input-group--show')) {
                            targetParent.find('input').attr('type', 'password');
                            return targetParent.removeClass('dashboard__input-group--show');
                        }

                        targetParent.find('input').attr('type', 'text');
                        return targetParent.addClass('dashboard__input-group--show');
                    });

                    $(document).on('click', '.dashboard__list-item, .js-see-panel', function () {
                        const targetPanel = $(this).attr('data-panel');

                        $(`.dashboard__panel`).removeClass('active');
                        $(`.dashboard__panel[data-panel="${targetPanel}"]`).addClass('active');

                        $(`.dashboard__tab, .dashboard__tab-content`).removeClass('active');
                        $(`.dashboard__panel[data-panel="${targetPanel}"]`).find('[data-tab="overview"]').addClass('active');
                    });

                    $(document).on('click', '.dashboard__tab', function () {
                        let tab = $(this);
                        let panel = tab.closest('.dashboard__panel');
                        let tabContent = panel.find(`.dashboard__tab-content[data-tab="${tab.attr('data-tab')}"]`);
                        let replyCollection = panel.find('.dashboard__reply-collection');

                        $(`.dashboard__tab, .dashboard__tab-content`).removeClass('active');
                        tab.addClass('active');
                        tabContent.addClass('active');

                        if (typeof replyCollection[0] !== "undefined") {
                            replyCollection.scrollTop(replyCollection[0].scrollHeight);
                        }

                        Dashboard.ticket.refresh.init(tab, panel);
                    });

                    $(document).on('click', '.dashboard__list-item', function () {
                        $(`.dashboard__list-item`).removeClass('active');
                        $(this).addClass('active');
                    });

                    $(document).on('click', '.js-dropdown-id li', function () {
                        const inputName = $(this).closest('.dashboard__dropdown-menu').attr('data-name');
                        const parent = $(this).closest('.dashboard__input-group');

                        parent.find('.dashboard__input-field--text span').text($(this).text());
                        parent.find('[name="' + inputName + '"]').val($(this).data('id'));
                    });

                    $(document).on('click', '.js-dropdown-type li', function () {
                        const inputName = $(this).closest('.dashboard__dropdown-menu').attr('data-name');
                        const parent = $(this).closest('.dashboard__input-group');
                        const form = parent.closest('form');

                        parent.find('.dashboard__input-field--text span').text($(this).text());
                        parent.find('[name="' + inputName + '"]').val($(this).text());
                        form.find('.dashboard__input-row--active').removeClass('dashboard__input-row--active');
                        form.find('.dashboard__input-row[data-type="default"], .dashboard__input-row[data-type="' + $(this).data('type-id') + '"]')
                            .addClass('dashboard__input-row--active');
                    });

                    $('.js-data-table').each(function () {
                        $(this).DataTable({
                            "paging":   true,
                            "ordering": false,
                            "info":     false
                        });
                    });

                    Layout.render();
                    Dashboard.render();
                    break;
            }
        };
        let Layout = {
            render: function () {
                Layout.window.init();
                Layout.form.init();
                Layout.dialog.init();
                Layout.print.init();
            },
            ajax: function (type, endpoint, data) {
                return $.ajax({
                    url: endpoint,
                    type: type,
                    data: data,
                    error: function (jqXHR, textStatus, error) {
                        console.log(textStatus + ': ' + error + "\n" + jqXHR.responseText);
                    }
                });
            },
            window: {
                init: function () {
                    if (window.history.replaceState) {
                        try {
                            window.history.replaceState(null, null, window.location.href);
                        } catch (e) {
                            console.log(e)
                        }
                    }

                    $(window).scroll(function () {
                        if ($(this).scrollTop() > 200) {
                            return $('.navigation').addClass('navigation--sticky');
                        }

                        return $('.navigation').removeClass('navigation--sticky');
                    });
                }
            },
            dialog: {
                init: function () {
                    $(document).on('click', '.js-dialog-close', function () {
                        return Layout.dialog.close();
                    });

                    $(document).on('click', '.js-dialog-show', function () {
                        return Layout.dialog.show($(this));
                    });

                    $(document).on('click', '.js-dialog-confirm', function () {
                        const actions = $(this).closest('.dashboard__actions');
                        const action = actions.data('action');
                        const panel = $(`.dashboard__panel[data-panel="${actions.data('panel')}"]`);

                        switch (action) {
                            case 'Close':
                                return Dashboard.ticket.reply.execute(panel, 'I\'m closing the ticket.', 3);
                            case 'Open':
                                return Dashboard.ticket.reply.execute(panel, 'I\'m re-opening the ticket.', 1);
                            case 'Assess':
                            case 'Reopen':
                            case 'Reject':
                                panel.find('.js-submit').val(action).click();
                                return Layout.dialog.close();
                            default:
                                return false;
                        }
                    });

                    $(document).on('click', '.js-reply', function () {
                        const panel = $(this).closest('.dashboard__panel');
                        const message = panel.find('.dashboard__reply-input').val();

                        return Dashboard.ticket.reply.execute(panel, message, 2);
                    });
                },
                build: function (className, htmlContent) {
                    const dialog = $('dialog');
                    const dialogHeader = `<div class="dialog__header"><span class="js-dialog-close">&times;</span></div>`;
                    const dialogContent = `<div class="dialog__content">${htmlContent}</div>`;
                    const dialogWrapper = `<div class="dialog__wrapper">${dialogHeader}${dialogContent}</div>`;

                    dialog.addClass(`dialog dialog--${className}`).html(dialogWrapper).show();

                    return false;
                },
                close: function () {
                    const dialog = $('dialog');

                    return dialog.attr('class', '').html('').hide();
                },
                show: function (element) {
                    const extension = element.data('file-extension');
                    const fileName = element.data('file-name');
                    let htmlContent = extension === 'pdf'
                        ? `<object data="${fileName}"></object>`
                        : `<img src="${fileName}"/>`;

                    return Layout.dialog.build('preview', htmlContent);
                }
            },
            form: {
                init: function () {
                    $(document).on('click', '.js-reset', function () {
                        Layout.form.reset($(this));
                    });

                    $(document).on('click', '.js-alert-close', function () {
                        $(this).parent().remove();
                    });
                },
                reset: function (element) {
                    const form = element.closest('form');

                    form.find('.dashboard__input-field--text span').text('');
                    form.find('.dashboard__input-field[type="hidden"]').val('');
                    form.find('.dashboard__input-row--active').removeClass('dashboard__input-row--active');
                    form.find('#files-names').html('');
                    form[0].reset();
                }
            },
            paging: {
                init: function (module, panel) {
                    const collection = panel.find('.dashboard__panel-collection');
                    const items = collection.find(`.dashboard__${module}`);
                    const numItems = items.length;
                    const perPage = 9;

                    items.slice(perPage).hide();

                    panel.find('.pagination').pagination({
                        items: numItems,
                        itemsOnPage: perPage,
                        prevText: "&laquo;",
                        nextText: "&raquo;",
                        onPageClick: function (pageNumber) {
                            var showFrom = perPage * (pageNumber - 1);
                            var showTo = showFrom + perPage;
                            items.hide().slice(showFrom, showTo).show();
                        }
                    });
                }
            },
            print: {
                init: function () {
                    $(document).on('click', '.js-print', function () {
                        const module = $(this).data('module');
                        const file = $(this).data('file');

                        return Dashboard[module][file].print();
                    });
                },
                execute: function (element, style) {
                    const htmlContent = `<html><head><style>${style}</style></head><body onload="window.print()">${element.outerHTML}</body></html>`;
                    const printDialog = window.open('', 'Print-View');

                    printDialog.document.open();
                    printDialog.document.write(htmlContent);
                    printDialog.document.close();

                    setTimeout(function () {
                        printDialog.close();
                    }, 10);
                }
            }
        };
        let Dashboard = {
            render: function () {
                Dashboard.attachment.init('ticket');
                Dashboard.attachment.init('permit');
                Dashboard.ticket.init();
                Dashboard.permit.init();
            },
            attachment: {
                init: function (category) {
                    const dt = new DataTransfer();

                    $('#attachment-' + category).on('change', function (e) {
                        for (var i = 0; i < this.files.length; i++) {
                            let fileBloc = $('<span/>', {class: 'file-block'}),
                                fileName = $('<span/>', {class: 'name', text: this.files.item(i).name});
                            fileBloc.append('<span class="file-delete"><span>+</span></span>')
                                .append(fileName);
                            $('#file-list > #files-names').append(fileBloc);
                        }

                        for (let file of this.files) {
                            dt.items.add(file);
                        }

                        this.files = dt.files;

                        $('span.file-delete').click(function () {
                            let name = $(this).next('span.name').text();
                            $(this).parent().remove();
                            for (let i = 0; i < dt.items.length; i++) {
                                if (name === dt.items[i].getAsFile().name) {
                                    dt.items.remove(i);
                                    continue;
                                }
                            }

                            document.getElementById('attachment-' + category).files = dt.files;
                        });
                    });
                }
            },
            dialog: {
                execute: function (element) {
                    const action = element.val();
                    const panel = element.closest('.dashboard__panel').data('panel');
                    const details = panel.split('-');
                    const message = `<p><strong>Are you sure you want to <strong>${action}</strong> this ${details[0]}?</strong></p>`;

                    return Dashboard.dialog.confirmation(panel, action, message);
                },
                confirmation: function (panel, action, message) {
                    const divider = `<div class="dashboard__divider"></div>`;
                    const cancel = `<input class="dashboard__button dashboard__button--secondary js-dialog-close" type="button" value="Cancel"/>`;
                    const confirm = `<input class="dashboard__button dashboard__button--primary js-dialog-confirm" type="button" value="Confirm"/>`;
                    const actions = `<div class="dashboard__actions" data-panel="${panel}" data-action="${action}">${cancel}${confirm}</div>`;
                    const htmlContent = `${message}${divider}${actions}`;

                    return Layout.dialog.build('confirmation', htmlContent);
                }
            },
            ticket: {
                init: function () {
                    Layout.paging.init('ticket', $('[data-panel="my-ticket"]'));

                    $(document).on('click', '.js-ticket-action', function () {
                        return Dashboard.dialog.execute($(this));
                    });
                },
                reply: {
                    execute: function (panel, message, ticketStatusId) {
                        return Layout.ajax('POST', configuration.ajax.replyTicket, {
                            ticketId: panel.data('id'),
                            message: message,
                            ticketStatusId: ticketStatusId
                        }).always(function (data) {
                            return Dashboard.ticket.reply.callback(panel, message, data);
                        });
                    },
                    callback: function (panel, message, data) {
                        const ticketId = panel.data('id');
                        const ticket = $(`.dashboard__ticket[data-id="ticket-${ticketId}"]`);
                        const replyCollection = panel.find('.dashboard__reply-collection');
                        let bubble = data.code === 200
                            ? `<div class="dashboard__reply-bubble">${message}</div>`
                            : `<div class="dashboard__reply-error">${data.response.message}</div>`;
                        let replyHtml = `<div class="dashboard__reply dashboard__reply--right">${bubble}</div>`;

                        if (data.code === 200) {
                            let senderType = `<span class="dashboard__reply-sender--type">${data.response.userType.userTypeName}</span>`;
                            let senderName = `<span class="dashboard__reply-sender--name">You</span>`;
                            let sender = `<div class="dashboard__reply-sender">${senderType}${senderName}</div>`;
                            let time = `<div class="dashboard__reply-time">${data.response.dateCreated}</div>`;
                            replyHtml = `<div class="dashboard__reply dashboard__reply--right">${sender}${bubble}${time}</div>`;

                            ticket.find('.dashboard__ticket-status')
                                .attr('class', 'dashboard__ticket-status')
                                .addClass(`dashboard__ticket-status--${data.response.ticketStatusName}`)
                                .html(data.response.ticketStatusName);
                            panel.find('.dashboard__reply-empty').remove();
                            panel.find('.dashboard__ticket-status')
                                .attr('class', 'dashboard__ticket-status')
                                .addClass(`dashboard__ticket-status--${data.response.ticketStatusName}`)
                                .html(data.response.ticketStatusName);

                            if (data.response.ticketStatusId === 3) {
                                $('.js-ticket-action').attr('value', 'Open');
                            } else {
                                $('.js-ticket-action').attr('value', 'Close');
                            }
                        }

                        $(replyHtml).appendTo(replyCollection);

                        const newReplyCollection = panel.find('.dashboard__reply-collection');

                        if (typeof newReplyCollection[0] !== "undefined") {
                            newReplyCollection.scrollTop(newReplyCollection[0].scrollHeight);
                        }

                        panel.find('.dashboard__reply-input').val('');
                        Layout.dialog.close();
                    },
                },
                refresh: {
                    ajax: false,
                    init: function(tab, panel) {
                        if (configuration.pageName === 'dashboard' && tab.data('tab') === "replies") {
                            window.myInterval = setInterval(function () {
                                if (!Dashboard.ticket.refresh.ajax) {
                                    console.log('Fetching message (s)');
                                    Dashboard.ticket.refresh.execute(tab, panel);
                                }
                            }, 5000);
                        }

                        return false;
                    },
                    clear: function() {
                        if(window.myInterval !== undefined && window.myInterval !== 'undefined'){
                            console.log('Clear interval: ' + window.myInterval);
                            return window.clearInterval(window.myInterval);
                        }

                        return false;
                    },
                    execute: function (tab, panel) {
                        Layout.ajax('POST', configuration.ajax.refreshTicket, {
                            ticketId: panel.data('id'),
                            lastId: panel.find('.js-reply-id').val()
                        }).always(function (data) {
                            if (data.code && data.response) {
                                return Dashboard.ticket.refresh.callback(tab, panel, data.response.data);
                            }
                        });
                    },
                    callback: function (tab, panel, reply) {
                        let replyHtml = '';
                        let replyCollection = panel.find('.dashboard__reply-collection');

                        if (reply !== null) {
                            panel.find('.dashboard__reply-empty').remove();
                            panel.find('.js-reply-id').val(reply.replyId);

                            let bubble = `<div class="dashboard__reply-bubble">${reply.message}</div>`;
                            let senderType = `<span class="dashboard__reply-sender--type">${reply.userTypeId.userTypeName}</span>`;
                            let senderName = `<span class="dashboard__reply-sender--name">${reply.sender.firstName}</span>`;
                            let sender = `<div class="dashboard__reply-sender">${senderType}${senderName}</div>`;
                            let time = `<div class="dashboard__reply-time">${reply.dateCreated}</div>`;
                            replyHtml += `<div class="dashboard__reply dashboard__reply--left">${sender}${bubble}${time}</div>`;
                            $(replyHtml).appendTo(replyCollection);

                            const newReplyCollection = panel.find('.dashboard__reply-collection');

                            if (typeof newReplyCollection[0] !== "undefined") {
                                newReplyCollection.scrollTop(newReplyCollection[0].scrollHeight);
                            }
                        }

                        Dashboard.ticket.refresh.ajax = false;
                    }
                }
            },
            permit: {
                init: function () {
                    Layout.paging.init('permit', $('[data-panel="my-permit"]'));

                    $(document).on('click', '.js-permit-action', function () {
                        return Dashboard.dialog.execute($(this));
                    });
                },
                soa: {
                    print: function () {
                        const element = document.getElementById('statement-of-account');
                        const style = 'ol,ul{padding-left:15px}ol{font-size:12px}ul{padding-bottom:15px}.statement-of-account__table{border-spacing:0;border-collapse:collapse;font-family:\'Open Sans\',sans-serif;font-size:12px;width:100%}.statement-of-account__title{border-bottom:3px solid #000;letter-spacing:1px;padding-top:20px;text-transform:uppercase;width:100%}.statement-of-account__table-category{font-size:10px;padding-top:10px;text-transform:uppercase}.statement-of-account__table-label{font-size:10px;padding-top:5px}.statement-of-account__table-space{height:20px}.statement-of-account__table-summary-title{border:2px solid #000000;font-size:12px;padding:3px;text-align:center}.statement-of-account__table-summary-label{padding:5px}.statement-of-account__table-summary-value{padding:5px;text-align:right}.statement-of-account__table-divider{height:20px;border-top:2px dashed #000}.statement-of-account__table-footer{padding:10px;border:1px solid #000;vertical-align:top}';

                        return Layout.print.execute(element, style);
                    }
                }
            }
        };

        return addEventListener();
    }
}