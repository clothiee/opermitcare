import { LayoutSettingsModule } from "./modules/layout-settings";

$(function opermitcare(e) {
    $(document).ready(() => {
        let layoutSettingsModule = new LayoutSettingsModule();

        layoutSettingsModule.initialize();
    });
});