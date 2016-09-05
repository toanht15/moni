package aainc.services.brandco.test.selenium.utils.strategies;

import aainc.services.brandco.test.selenium.utils.Settings;

import java.io.IOException;

public class DefaultCommandStrategy implements CommandStrategy {

    @Override
    public void openCampaign(String campaignId) {
        {
            ProcessBuilder builder = new ProcessBuilder().command(
                    "ssh",
                    Settings.getInstance().getBatchHost(),
                    "php /var/www/html/brandco/apps/batch/UpdateCpDateToPresent.php " + campaignId);
            try {
                Process process = builder.start();
                int exitValue = process.waitFor();
                if (exitValue != 0) {
                    throw new RuntimeException("UpdateCpDateToPresent failed!");
                }
            } catch (IOException | InterruptedException e) {
                throw new RuntimeException(e);
            }
        }
        {
            ProcessBuilder builder = new ProcessBuilder().command(
                    "ssh",
                    Settings.getInstance().getBatchHost(),
                    "php /var/www/html/brandco/apps/batch/AutoUpdateCpStatus.php");
            try {
                Process process = builder.start();
                int exitValue = process.waitFor();
                if (exitValue != 0) {
                    throw new RuntimeException("AutoUpdateCpStatus failed!");
                }
            } catch (IOException | InterruptedException e) {
                throw new RuntimeException(e);
            }
        }
    }
}
