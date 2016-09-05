package aainc.services.brandco.test.selenium.utils.strategies;

import aainc.services.brandco.test.selenium.utils.DriverManager;
import aainc.services.brandco.test.selenium.utils.Settings;

public class MacOSCommandStrategy implements CommandStrategy {

    @Override
    public void openCampaign(String campaignId) {
        DriverManager manager = DriverManager.getInstance();
        String brandHomeUrl = Settings.getInstance().getBrandHomeUrl();
        String url = brandHomeUrl + "campaigns/" + campaignId;
        manager.goToUrlUntilTextNotFound(url, "404");
    }
}
