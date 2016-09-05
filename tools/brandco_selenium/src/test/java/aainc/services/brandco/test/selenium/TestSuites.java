package aainc.services.brandco.test.selenium;

import aainc.services.brandco.test.selenium.crm.CampaignLoginTest;
import aainc.services.brandco.test.selenium.crm.DemoModeTest;
import aainc.services.brandco.test.selenium.utils.DriverManager;
import junit.framework.TestCase;
import org.junit.AfterClass;
import org.junit.runner.RunWith;
import org.junit.runners.Suite;

@RunWith(Suite.class)
@Suite.SuiteClasses({UserTest.class, AdminTest.class, CampaignLoginTest.class, DemoModeTest.class})
public class TestSuites extends TestCase {

    @AfterClass
    public static void shutdown() {
        DriverManager.getInstance().quit();
    }
}