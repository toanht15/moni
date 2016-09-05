package aainc.services.brandco.test.selenium;

import aainc.services.brandco.test.selenium.utils.DriverManager;
import aainc.services.brandco.test.selenium.utils.FirefoxScreenShotRule;
import aainc.services.brandco.test.selenium.utils.Settings;
import org.junit.Before;
import org.junit.Rule;
import org.junit.Test;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

public class UserTest {

    @Rule
    public FirefoxScreenShotRule rule = new FirefoxScreenShotRule();

    @Before
    public void before() {
        DriverManager.getInstance().refrech();
    }

    @Test
    public void testLoginByGooglePlusAndLogout() throws Exception {
        DriverManager manager = DriverManager.getInstance();
        String brandHomeUrl = Settings.getInstance().getBrandHomeUrl();

        // goto login page
        manager.goTo(brandHomeUrl + "my/login");

        // goto google plus login page
        manager.click(".btnSnsGp1 a");

        String currentUrl = manager.getCurrentUrl();
        assertTrue("Invalid URL:" + currentUrl, currentUrl.startsWith("https://accounts.google.com/ServiceLogin"));

        // enter e-mail
        manager.sendKeys("#Email", Settings.GP_EMAIL);
        manager.click("#next");

        // enter password
        manager.sendKeys("#Passwd", Settings.GP_PASSWORD);
        manager.click("#signIn");

        // approve privacy
        manager.untilEnabled("#submit_approve_access");
        manager.click("#submit_approve_access");

        // validate the result
        manager.verifyCurrentURL(brandHomeUrl);

        // logout
        manager.logout(brandHomeUrl);
    }

    @Test
    public void testShowInbox() throws InterruptedException {
        DriverManager manager = DriverManager.getInstance();
        String brandHomeUrl = Settings.getInstance().getBrandHomeUrl();

        // login first.
        manager.loginByGooglePlus(Settings.GP_EMAIL, Settings.GP_PASSWORD, brandHomeUrl);

        // go to inbox.
        manager.goTo(brandHomeUrl + "mypage/inbox");
        String text = manager.getText(".nametext");
        assertEquals("結合試験ユーザーさん", text);
    }
}
