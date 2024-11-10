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

            $(document).on('click', '.dashboard__dropdown-menu li', function () {
                const parent = $(this).closest('.dashboard__input-group');

                parent.find('.dashboard__input-field--text span').text($(this).text());
                parent.find('[name="problemTypeId"]').val($(this).data('id'));
            });

            $(document).on('click', '.alert a.button', function () {
                $(this).parent().remove();
            });

            $(document).on('click', '[value="Reset"]', function () {
                const form = $(this).closest('form');

                form.find('.dashboard__input-field--text span').text('');
                form.find('[name="problemTypeId"]').val('');
                form[0].reset();
            });
        };

        return addEventListener();
    }
}