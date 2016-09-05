package aainc.services.brandco.test.selenium;

import aainc.services.brandco.test.selenium.utils.DriverManager;
import aainc.services.brandco.test.selenium.utils.Settings;
import org.apache.commons.io.FileUtils;
import org.junit.Test;
import org.openqa.selenium.Capabilities;
import org.openqa.selenium.OutputType;
import org.openqa.selenium.TakesScreenshot;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.firefox.FirefoxBinary;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.remote.ErrorHandler;
import org.openqa.selenium.remote.Response;

import java.io.ByteArrayInputStream;
import java.io.File;
import java.io.IOException;
import java.net.InetAddress;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.logging.LogManager;

/**
 * 疎通確認に利用したサンプルです。
 */
public class SampleTest {

    private class FirefoxDriver2 extends FirefoxDriver {

        public FirefoxDriver2(FirefoxBinary binary) {
            super(binary, null);
        }

        @Override
        protected void startSession(Capabilities desiredCapabilities, Capabilities requiredCapabilities) {
            this.setErrorHandler(new ErrorHandler() {
                @Override
                public Response throwIfResponseFailed(Response response, long duration) throws RuntimeException {
                    System.err.println(response.toString());
                    System.err.println("type=" + (response.getValue() != null ? response.getValue().getClass() : ""));
                    return super.throwIfResponseFailed(response, duration);
                }
            });
            super.startSession(desiredCapabilities, requiredCapabilities);
        }
    }

    /**
     * 疎通確認用のメソッド。
     * @throws IOException
     */
    public void testShowNewMoniplaUrl() throws IOException {
        LogManager.getLogManager().readConfiguration(new ByteArrayInputStream(
                ("#test log\n" +
                ".level= ALL\n" +
                "handlers= java.util.logging.ConsoleHandler\n" +
                "java.util.logging.ConsoleHandler.level= ALL").getBytes()));

        {
            InetAddress address = InetAddress.getByName("stg.monipla.com");
            System.out.println("IP address=(stg.monipla.com)" + address.toString());
        }
        {
            InetAddress address = InetAddress.getByName("localhost");
            System.out.println("IP address(localhost)=" + address.toString());
        }

        FirefoxBinary binary = new FirefoxBinary();
        binary.setEnvironmentProperty("DISPLAY", ":1");

        FirefoxDriver2 driver = new FirefoxDriver2(binary);
        System.out.println(driver.toString());
        Capabilities capabilities = driver.getCapabilities();
        System.out.println(capabilities.toString());

        try {
            driver.get("https://stg.monipla.com/test_domain/");
            TakesScreenshot screenshot = (TakesScreenshot) driver;
            byte[] screenshotFile = screenshot.getScreenshotAs(OutputType.BYTES);
            FileUtils.writeByteArrayToFile(new File("/tmp/test.png"), screenshotFile);
        } finally {
            driver.close();
        }
    }

    /**
     * 常設CP確認用のメソッド
     * @throws InterruptedException
     */
    public void testNonIncentive() throws InterruptedException {
        DriverManager manager = DriverManager.getInstance();
        String brandHomeUrl = Settings.getInstance().getBrandHomeUrl();

        manager.loginByGooglePlus(Settings.GP_ADMIN_EMAIL, Settings.GP_ADMIN_PASSWORD, brandHomeUrl);
        String[] campaignKeys = manager.openNonIncentiveCampaign(brandHomeUrl);
        manager.joinNonIncentiveCpWhenLoggedIn(brandHomeUrl, campaignKeys[0]);
    }
}
