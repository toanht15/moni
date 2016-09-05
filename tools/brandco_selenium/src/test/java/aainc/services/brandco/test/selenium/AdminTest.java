package aainc.services.brandco.test.selenium;

import aainc.services.brandco.test.selenium.utils.DriverManager;
import aainc.services.brandco.test.selenium.utils.FirefoxScreenShotRule;
import aainc.services.brandco.test.selenium.utils.Settings;
import org.junit.Before;
import org.junit.Rule;
import org.junit.Test;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Action;
import org.openqa.selenium.interactions.Actions;

import java.sql.Time;
import java.util.Calendar;
import java.util.Date;
import java.util.List;
import java.util.concurrent.TimeUnit;

import static java.util.Arrays.asList;
import static java.util.stream.Collectors.toList;
import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

public class AdminTest {

    @Rule
    public FirefoxScreenShotRule rule = new FirefoxScreenShotRule();

    @Before
    public void before() {
        DriverManager.getInstance().refrech();
    }

    /**
     * メッセージを送信してからスレッドを表示し、アクションの編集画面でカウントをチェックするまでのシナリオを実行します。
     * @throws InterruptedException
     */
    @Test
    public void testSendMessageAndShowThread() throws InterruptedException {
        // send a sample mail to the user.
        DriverManager manager = DriverManager.getInstance();
        String brandHomeUrl = Settings.getInstance().getBrandHomeUrl();
        String preservedUrl = "";

        // login first.
        manager.loginByGooglePlus(Settings.GP_ADMIN_EMAIL, Settings.GP_ADMIN_PASSWORD, brandHomeUrl);

        // go to creation page.
        manager.goTo(brandHomeUrl + "admin-cp/edit_customize_skeleton?type=2");

        // goto the next page.
        manager.click(".newSkeletonSubmitButton");

        preservedUrl = manager.getCurrentUrl();

        // confirm the message.
        manager.sendKeys("#text_area", "メッセージ送信テスト - " + (new Date()));
        manager.click("#submit");

        // select targets.
        manager.click(".select");
        manager.exists(".userNum");

        // choose targets.
        manager.check("input[value=\"" + Settings.GP_ADMIN_USER_ID + "\"]");
        manager.click("a[data-update_type=\"insert\"]");
        manager.exists(".checkedUser");

        manager.waitUntilElementHided(".blockOverlay");

        // send the mail.
        manager.clickUntilElementExist("p[data-btn_type=\"send_mail\"] a", "#submitReservationSchedule");
        manager.click("#submitReservationSchedule");

        // validate.
        manager.verifyCurrentURL(brandHomeUrl + "admin-cp/show_reservation_info/");
        manager.exists(".messageReserved");

        // goTo ids.
        String[] urlFragments = preservedUrl.split("/");
        String campaignId = urlFragments[urlFragments.length - 2];
        String actionId = urlFragments[urlFragments.length - 1];

        // go to thread and verify the message.
        WebElement threadMsg = manager.goToUrlUntilElementFound(brandHomeUrl + "messages/thread/" + campaignId, ".messageText");
        manager.waitUntilElementDeleted(".cmd_execute_message_action");
        assertTrue(threadMsg.getText().startsWith("メッセージ送信テスト -"));

        // go to edit and verify the counts.
        manager.goTo(brandHomeUrl + "admin-cp/edit_action/" + campaignId + "/" + actionId);
        String sendCount = manager.getText(".iconMail1 .num");
        String openCount = manager.getText(".iconMail2 .num");
        String completeCount = manager.getText(".iconCheck3 .num");
        assertEquals(
            asList("1", "1", "1"),
            asList(sendCount, openCount, completeCount));
    }

    public void testSendTwoContinuousMessages() throws InterruptedException {
        // send a sample mail to the user.
        DriverManager manager = DriverManager.getInstance();
        String brandHomeUrl = Settings.getInstance().getBrandHomeUrl();
        String preservedUrl = "";

        // login first.
        manager.loginByGooglePlus(Settings.GP_ADMIN_EMAIL, Settings.GP_ADMIN_PASSWORD, brandHomeUrl);

        // go to creation page.
        manager.goTo(brandHomeUrl + "admin-cp/edit_customize_skeleton?type=2");

        List<WebElement> msgs = manager.findBySelector("li[data-action-type=\"1\"] img");
        manager.dragAndDrop(msgs.get(1), msgs.get(0));

        // goto the next page.
        manager.click(".newSkeletonSubmitButton");

        preservedUrl = manager.getCurrentUrl();
        String[] urlFragments = preservedUrl.split("/");
        String campaignId = urlFragments[urlFragments.length - 2];
        String actionId = urlFragments[urlFragments.length - 1];

        // confirm the first message.
        manager.sendKeys("#text_area", "メッセージ送信テスト1 - " + (new Date()));
        manager.click("#submit");

        // confirm the second message.
        manager.click("a[href*=\"" + brandHomeUrl + "admin-cp/edit_action/\"]");
        manager.sendKeys("#text_area", "メッセージ送信テスト2 - " + (new Date()));
        manager.click("#submit");

        // select targets.
        manager.click(".select");
        manager.exists(".userNum");

        // choose targets.
        manager.check("input[value=\"" + Settings.GP_ADMIN_USER_ID + "\"]");
        manager.click("a[data-update_type=\"insert\"]");
        manager.exists(".checkedUser");
        manager.waitUntilElementHided(".blockOverlay");
        manager.exists("span[data-check_no=\"1\"]");

        // send the mail.
        manager.clickUntilElementExist("p[data-btn_type=\"send_mail\"] a", "#submitReservationSchedule");
        manager.click("#submitReservationSchedule");

        // validate.
        manager.verifyCurrentURL(brandHomeUrl + "admin-cp/show_reservation_info/");
        manager.exists(".messageReserved");


        // go to thread and verify the message.
        manager.reloadUntilTextNotFound(brandHomeUrl + "messages/thread/" + campaignId, "404");
        TimeUnit.SECONDS.sleep(5);

        // go to edit and verify the counts.
        manager.goTo(brandHomeUrl + "admin-cp/edit_action/" + campaignId + "/" + actionId);
        String sendCount = manager.getText(".iconMail1 .num");
        String openCount = manager.getText(".iconMail2 .num");
        String completeCount = manager.getText(".iconCheck3 .num");
        assertEquals("1", sendCount);
    }
}
