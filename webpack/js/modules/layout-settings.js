export class LayoutSettingsModule {
    constructor(configuration = {}) {
        this.configuration = configuration;
    };

    initialize() {
        let addEventListener = function () {
            $(window).scroll(function () {
                if ($(this).scrollTop() > 300) {
                    return $('.navigation').addClass('navigation--sticky');
                }

                return $('.navigation').removeClass('navigation--sticky');
            });

            $(document).on('click', '.dashboard__list-item, .js-see-tickets', function () {
                const targetTab = $(this).attr('data-tab');

                $(`.dashboard__tab`).removeClass('active');
                $(`.dashboard__tab[data-tab="${targetTab}"]`).addClass('active');
            });

            $(document).on('click', '.dashboard__list-item', function () {
                $(`.dashboard__list-item`).removeClass('active');
                $(this).addClass('active');
            });
        };

        return addEventListener();
    }
}