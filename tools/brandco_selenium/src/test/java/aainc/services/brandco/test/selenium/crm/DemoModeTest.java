package aainc.services.brandco.test.selenium.crm;

import aainc.services.brandco.test.selenium.utils.DriverManager;
import aainc.services.brandco.test.selenium.utils.FirefoxScreenShotRule;
import aainc.services.brandco.test.selenium.utils.Settings;
import org.junit.Before;
import org.junit.Rule;
import org.junit.Test;

public class DemoModeTest {

    @Rule
    public FirefoxScreenShotRule rule = new FirefoxScreenShotRule();

    @Before
    public void before() {
        DriverManager.getInstance().refrech();
    }

    @Test
    public void testLogin01() throws InterruptedException {
        DriverManager manager = DriverManager.getInstance();
        String brandHomeUrl = Settings.getInstance().getBrandHomeUrl();

        /**
         * prepare
         */
        manager.loginByGooglePlus(Settings.GP_ADMIN_EMAIL, Settings.GP_ADMIN_PASSWORD, brandHomeUrl);
        String[] campaignKeys = manager.openCampaign(brandHomeUrl, false, true);

        /**
         * join the campaign
         */
        manager.verifyCurrentURL(brandHomeUrl + "admin-cp/public_cps");
        String demoURL = manager.getDemoURL(".preview a", brandHomeUrl + "campaigns/" + campaignKeys[0]);
        manager.goTo(demoURL);
        manager.joinCampaignWhenLoggedIn(brandHomeUrl, campaignKeys[0]);

        /**
         * verify
         */
        manager.verifyCampaignCounts(brandHomeUrl, campaignKeys);

        // view the page(for test 09).
        manager.goTo(brandHomeUrl + "campaigns/" + campaignKeys[0]);
        manager.verifyCurrentURL(brandHomeUrl + "messages/thread/" + campaignKeys[0]);
    }
}
