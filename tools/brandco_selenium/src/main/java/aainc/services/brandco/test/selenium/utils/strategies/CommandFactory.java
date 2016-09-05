package aainc.services.brandco.test.selenium.utils.strategies;

public class CommandFactory {

    public CommandStrategy newInstance() {
        String osName = System.getProperty("os.name").toLowerCase();
        if (osName.indexOf("mac") > -1) {
            return new MacOSCommandStrategy();
        }
        return new DefaultCommandStrategy();
    }
}
