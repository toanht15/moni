package aainc.services.brandco.test.selenium.utils;

import org.junit.rules.TestWatcher;
import aainc.services.brandco.test.selenium.utils.DriverManager;
import org.junit.runner.Description;

public class FirefoxScreenShotRule extends TestWatcher {

    @Override
    protected void failed(Throwable e, Description description) {
        DriverManager manager = DriverManager.getInstance();
        String filePath = manager.takeScreenShot(description.getClassName() + "_" + description.getMethodName());
        System.err.println(String.format("The last URL=%s, The file path of the screen shot on failure=%s", manager.getCurrentUrl(), filePath));
    }
}
