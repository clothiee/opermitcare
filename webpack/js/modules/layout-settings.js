export class LayoutSettingsModule {
    constructor(configuration = {}) {
        this.configuration = configuration;
    };

    initialize() {
        let addEventListener = function () {
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }

            $(window).scroll(function () {
                if ($(this).scrollTop() > 300) {
                    return $('.navigation').addClass('navigation--sticky');
                }

                return $('.navigation').removeClass('navigation--sticky');
            });

            $(document).on('click', '.login__input svg', function () {
                let targetParent = $(this).closest('.login__input');

                if (targetParent.hasClass('login__input--show')) {
                    targetParent.find('input').attr('type', 'password');
                    return targetParent.removeClass('login__input--show');
                }

                targetParent.find('input').attr('type', 'text');
                return targetParent.addClass('login__input--show');
            });

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
            });

            $(document).on('click', '.dashboard__list-item', function () {
                $(`.dashboard__list-item`).removeClass('active');
                $(this).addClass('active');
            });

            $(document).on('click', '.js-dropdown-id li', function () {
                const inputName = $(this).closest('.dashboard__dropdown-menu').attr('data-name');
                const parent = $(this).closest('.dashboard__input-group');

                parent.find('.dashboard__input-field--text span').text($(this).text());

                console.log($(this).data('id'));

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

            $(document).on('click', '.alert a.button', function () {
                $(this).parent().remove();
            });

            $(document).on('click', '[value="Reset"]', function () {
                const form = $(this).closest('form');

                form.find('.dashboard__input-field--text span').text('');
                form.find('.dashboard__input-field[type="hidden"]').val('');
                form.find('.dashboard__input-row--active').removeClass('dashboard__input-row--active');
                form.find('#files-names').html('');
                form[0].reset();
            });

            $(document).on('click', '[value="Reply"]', function () {
                const panel = $(this).closest('.dashboard__panel');
                const message = panel.find('.dashboard__reply-input').val();

                return replyTicket(panel, message, 1);
            });

            $(document).on('click', '[value="Close"]', function () {
                const panel = $(this).closest('.dashboard__panel');
                const message = 'I\'m closing the ticket.';

                return replyTicket(panel, message, 3);
            });

            $(document).on('click', '[value="Open"]', function () {
                const panel = $(this).closest('.dashboard__panel');
                const message = 'I\'m re-opening the ticket.';

                return replyTicket(panel, message, 1);
            });

            $(document).on('click', '[value="Print"]', function () {
                var divToPrint=document.getElementById('statement-of-account');
                var newWin=window.open('','Print Statement Of Account');
                var style = 'ol,ul{padding-left:15px}ol{font-size:12px}ul{padding-bottom:15px}.statement-of-account__table{border-spacing:0;border-collapse:collapse;font-family:\'Open Sans\',sans-serif;font-size:12px;width:100%}.statement-of-account__title{border-bottom:3px solid #000;letter-spacing:1px;padding-top:20px;text-transform:uppercase;width:100%}.statement-of-account__table-category{font-size:10px;padding-top:10px;text-transform:uppercase}.statement-of-account__table-label{font-size:10px;padding-top:5px}.statement-of-account__table-space{height:20px}.statement-of-account__table-summary-title{border:2px solid #000000;font-size:12px;padding:3px;text-align:center}.statement-of-account__table-summary-label{padding:5px}.statement-of-account__table-summary-value{padding:5px;text-align:right}.statement-of-account__table-divider{height:20px;border-top:2px dashed #000}.statement-of-account__table-footer{padding:10px;border:1px solid #000;vertical-align:top}';
                newWin.document.open();
                newWin.document.write('<html><head><style>'+style+'</style></head><body onload="window.print()">'+divToPrint.outerHTML+'</body></html>');
                newWin.document.close();

                setTimeout(function(){newWin.close();},10);
            });

            attachment('ticket');
            attachment('permit');
        };

        let replyTicket = function (panel, message, ticketStatusId) {
            const ticketId = panel.data('ticket-id');
            const ticket = $(`.dashboard__ticket[data-ticket="ticket-${ticketId}"]`);
            const replyCollection = panel.find('.dashboard__reply-collection');

            return $.ajax({
                url: configuration.ajax.replyTicket,
                type: 'POST',
                data: {
                    ticketId: ticketId,
                    message: message,
                    ticketStatusId: ticketStatusId
                },
                success: function (data) {
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

                        if (data.response.ticketStatusId === 2) {
                            $('.js-ticket-action').attr('value', 'Close');
                        }

                        if (data.response.ticketStatusId === 3) {
                            $('.js-ticket-action').attr('value', 'Open');
                        }
                    }

                    $(replyHtml).appendTo(replyCollection);

                    const newReplyCollection = panel.find('.dashboard__reply-collection');
                    newReplyCollection.scrollTop(newReplyCollection[0].scrollHeight);
                },
                error: function (jqXHR, textStatus, error) {
                    console.log(textStatus + ': ' + error + "\n" + jqXHR.responseText);
                }
            });
        };

        let attachment = function (category) {
            const dt = new DataTransfer();

            $('#attachment-'+category).on('change', function(e){
                for(var i = 0; i < this.files.length; i++){
                    let fileBloc = $('<span/>', {class: 'file-block'}),
                        fileName = $('<span/>', {class: 'name', text: this.files.item(i).name});
                    fileBloc.append('<span class="file-delete"><span>+</span></span>')
                        .append(fileName);
                    $('#file-list > #files-names').append(fileBloc);
                };

                for (let file of this.files) {
                    dt.items.add(file);
                }

                this.files = dt.files;

                $('span.file-delete').click(function(){
                    let name = $(this).next('span.name').text();
                    $(this).parent().remove();
                    for(let i = 0; i < dt.items.length; i++){
                        if(name === dt.items[i].getAsFile().name){
                            dt.items.remove(i);
                            continue;
                        }
                    }

                    document.getElementById('attachment-'+category).files = dt.files;
                });
            });
        };

        return addEventListener();
    }
}