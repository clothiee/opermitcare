export class LayoutSettingsModule {
    constructor(configuration = {}) {
        this.configuration = configuration;
    };

    initialize() {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 300) {
                return $('.navigation').addClass('navigation--sticky');
            }

            return $('.navigation').removeClass('navigation--sticky');
        });
    }
}