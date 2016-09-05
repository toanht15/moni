package aainc.services.brandco.test.selenium.crm;

import aainc.services.brandco.test.selenium.utils.DriverManager;
import aainc.services.brandco.test.selenium.utils.FirefoxScreenShotRule;
import aainc.services.brandco.test.selenium.utils.Settings;
import org.junit.Before;
import org.junit.Rule;
import org.junit.Test;

/**
 * https://docs.google.com/a/aainc.co.jp/spreadsheets/d/13nUExCB8sabiJvJn7bC7YbV0XQymtsSx-TXOHOuS2YQ/edit?usp=sharing
 */
public class CampaignLoginTest {

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

        manager.loginByGooglePlus(Settings.GP_ADMIN_EMAIL, Settings.GP_ADMIN_PASSWORD, brandHomeUrl);
        String[] campaignKeys = manager.openCampaign(brandHomeUrl);
        manager.joinCampaignWhenLoggedIn(brandHomeUrl, campaignKeys[0]);
        manager.verifyCampaignCounts(brandHomeUrl, campaignKeys);
    }

    @Test
    public void testLogin02() throws InterruptedException {
        DriverManager manager = DriverManager.getInstance();
        String brandHomeUrl = Settings.getInstance().getBrandHomeUrl();

        /**
         * prepare
         */
        manager.loginByGooglePlus(Settings.GP_ADMIN_EMAIL, Settings.GP_ADMIN_PASSWORD, brandHomeUrl);
        String[] campaignKeys = manager.openCampaign(brandHomeUrl);

        /**
         * join the campaign
         */
        manager.refrech();
        manager.goTo(brandHomeUrl +"campaigns/" + campaignKeys[0]);
        manager.joinCampaignWhenNotLoggedIn(brandHomeUrl, campaignKeys[0], Settings.GP_ADMIN_EMAIL, Settings.GP_ADMIN_PASSWORD);

        /**
         * verify
         */
        manager.verifyCampaignCounts(brandHomeUrl, campaignKeys);
    }

    @Test
    public void testLogin03_04() throws InterruptedException {
        DriverManager manager = DriverManager.getInstance();
        String brandHomeUrl = Settings.getInstance().getBrandHomeUrl();

        manager.loginByGooglePlus(Settings.GP_ADMIN_EMAIL, Settings.GP_ADMIN_PASSWORD, brandHomeUrl);
        String[] campaignKeys = manager.openCampaign(brandHomeUrl);
        manager.joinCampaignWhenLoggedIn(brandHomeUrl, campaignKeys[0]);

        // log out and participate in the campaign.
        manager.refrech();
        manager.goTo(brandHomeUrl + "campaigns/" + campaignKeys[0]);
        manager.click(".jsOldLoginOauth .btnSnsGp1 a");
        manager.loginByGooglePlus(Settings.GP_ADMIN_EMAIL, Settings.GP_ADMIN_PASSWORD, brandHomeUrl, false);
        manager.verifyCurrentURL(brandHomeUrl + "messages/thread/" + campaignKeys[0]);
        manager.verifyNotErrorPage();

        // view the page(for test 04).
        manager.goTo(brandHomeUrl + "campaigns/" + campaignKeys[0]);
        manager.verifyCurrentURL(brandHomeUrl + "messages/thread/" + campaignKeys[0]);
    }

    @Test
    public void testLogin05_09() throws InterruptedException {
        DriverManager manager = DriverManager.getInstance();
        String brandHomeUrl = Settings.getInstance().getBrandHomeUrl();

        /**
         * prepare
         */
        manager.loginByGooglePlus(Settings.GP_ADMIN_EMAIL, Settings.GP_ADMIN_PASSWORD, brandHomeUrl);
        String[] campaignKeys = manager.openCampaign(brandHomeUrl, true, false);

        /**
         * join the campaign
         */
        manager.goTo(brandHomeUrl +"campaigns/" + campaignKeys[0]);
        manager.joinCampaignWhenLoggedIn(brandHomeUrl, campaignKeys[0], true);

        /**
         * verify
         */
        manager.verifyCampaignCounts(brandHomeUrl, campaignKeys);

        // view the page(for test 09).
        manager.goTo(brandHomeUrl + "campaigns/" + campaignKeys[0]);
        manager.verifyCurrentURL(brandHomeUrl + "messages/thread/" + campaignKeys[0]);
    }

    @Test
    public void testLogin10() throws InterruptedException {
        DriverManager manager = DriverManager.getInstance();
        String brandHomeUrl = Settings.getInstance().getBrandHomeUrl();

        /**
         * prepare
         */
        manager.loginByGooglePlus(Settings.GP_ADMIN_EMAIL, Settings.GP_ADMIN_PASSWORD, brandHomeUrl);
        String[] campaignKeys = manager.openCampaign(brandHomeUrl);
        manager.sendMessageToUser(brandHomeUrl, campaignKeys, Settings.GP_ADMIN_USER_ID);

        /**
         * go to the thread.
         */
        manager.goToUrlUntilElementFound(brandHomeUrl + "messages/thread/" + campaignKeys[0], ".large1");
        manager.joinCampaignWhenLoggedIn(brandHomeUrl, campaignKeys[0]);

        /**
         * verify the counts.
         */
        manager.verifyCampaignCounts(brandHomeUrl, campaignKeys);
    }
}
