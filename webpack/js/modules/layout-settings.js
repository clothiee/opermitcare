export class LayoutSettingsModule {
    constructor(configuration = {}) {
        this.configuration = configuration;
    };

    initialize() {
        let addEventListener = function () {
            if (window.history.replaceState) {
                window.history.replaceState( null, null, window.location.href );
            }

            $(window).scroll(function () {
                if ($(this).scrollTop() > 300) {
                    return $('.navigation').addClass('navigation--sticky');
                }

                return $('.navigation').removeClass('navigation--sticky');
                return $('.navigation').removeClass('navigation--sticky');
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
                $(`.dashboard__panel[data-panel="${targetPanel}"]`).find('[data-tab="ticket-overview"]').addClass('active');
            });

            $(document).on('click', '.dashboard__tab', function () {
                const targetTabContent = $(this).attr('data-tab');

                $(`.dashboard__tab, .dashboard__tab-content`).removeClass('active');
                $(this).addClass('active');
                $(`.dashboard__tab-content[data-tab="${targetTabContent}"]`).addClass('active');
            });

            $(document).on('click', '.dashboard__list-item', function () {
                $(`.dashboard__list-item`).removeClass('active');
                $(this).addClass('active');
            });

            $(document).on('click', '.js-dropdown-id li', function () {
                const inputName = $(this).closest('.dashboard__dropdown-menu').attr('data-name');
                const parent = $(this).closest('.dashboard__input-group');

                parent.find('.dashboard__input-field--text span').text($(this).text());
                parent.find('[name="'+ inputName+ '"]').val($(this).data('id'));
            });

            $(document).on('click', '.js-dropdown-type li', function () {
                const inputName = $(this).closest('.dashboard__dropdown-menu').attr('data-name');
                const parent = $(this).closest('.dashboard__input-group');
                const form = parent.closest('form');

                parent.find('.dashboard__input-field--text span').text($(this).text());
                parent.find('[name="'+ inputName+ '"]').val($(this).text());
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
                form[0].reset();
            });

            $(document).on('click', '[value="Reply"]', function () {
                const panel = $(this).closest('.dashboard__panel');
                const replyCollection = panel.find('.dashboard__reply-collection');
                const ticketId = panel.data('ticket-id');
                const message = panel.find('.dashboard__reply-input').val();

                return $.ajax({
                    url: configuration.ajax.replyTicket,
                    type: 'POST',
                    data: {
                        ticketId: ticketId,
                        message: message
                    },
                    success: function (data) {
                        let bubble = data.code === 200
                            ? `<div class="dashboard__reply-bubble">${message}</div>`
                            : `<div class="dashboard__reply-error">${data.response.message}</div>`;
                        let replyHtml = `<div class="dashboard__reply dashboard__reply--right">${bubble}</div>`;

                        $(replyHtml).appendTo(replyCollection);
                    },
                    error: function (jqXHR, textStatus, error) {
                        console.log(textStatus + ': ' + error + "\n" + jqXHR.responseText);
                    }
                });
            });
        };

        return addEventListener();
    }
}