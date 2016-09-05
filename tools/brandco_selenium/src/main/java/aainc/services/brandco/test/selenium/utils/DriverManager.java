package aainc.services.brandco.test.selenium.utils;

import aainc.services.brandco.test.selenium.utils.strategies.CommandFactory;
import aainc.services.brandco.test.selenium.utils.strategies.CommandStrategy;
import com.gargoylesoftware.htmlunit.ElementNotFoundException;
import com.google.common.base.Predicate;
import org.apache.commons.io.FileUtils;
import org.openqa.selenium.*;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.interactions.Action;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.ui.WebDriverWait;

import javax.lang.model.element.Element;
import java.io.File;
import java.io.IOException;
import java.sql.*;
import java.util.*;
import java.util.Date;
import java.util.concurrent.TimeUnit;
import java.util.concurrent.atomic.AtomicInteger;
import java.util.concurrent.atomic.AtomicLong;
import java.util.concurrent.atomic.AtomicReference;

import static org.junit.Assert.assertTrue;

/**
 * Driver及びWebDriverWaitのライフサイクルを一元管理し、
 * 簡単に扱うためのインターフェースを提供するマネージャ。
 */
public final class DriverManager {
    public static final int MAX_TIME_WAIT = 300;

    private static DriverManager manager = new DriverManager();

    private WebDriver currentDriver;

    private WebDriverWait wait;

    private CommandStrategy commandStrategy = new CommandFactory().newInstance();

    private Connection connection;

    public static DriverManager getInstance() {
        return manager;
    }

    /**
     * Webブラウザのリソースをリフレッシュします。
     * 基本的にテストの実施毎に呼び出してください。
     */
    public final void refrech() {
        quit();
        currentDriver = new FirefoxDriver();
        wait = new WebDriverWait(currentDriver, DriverManager.MAX_TIME_WAIT);
        wait.ignoreAll(Arrays.asList(ElementNotVisibleException.class));
//        Settings settings = Settings.getInstance();
//        try {
//            connection = java.sql.DriverManager.getConnection(
//                    settings.getConnectionUrl(),
//                    settings.getConnectionUser(),
//                    settings.getConnectionPassword());
//        } catch (SQLException e) {
//            throw new RuntimeException(e);
//        }
    }

    /**
     * Webブラウザのリソースを全てクリアします。
     */
    public final void quit() {
        if (currentDriver != null) {
            currentDriver.quit();
            currentDriver = null;
            wait = null;
        }
        if (connection != null) {
            try {
                connection.close();
            } catch (SQLException e) {
                e.printStackTrace();
            }
        }
    }


    @Override
    protected final void finalize() throws Throwable {
        if (currentDriver != null) {
            currentDriver.quit();
        }
    }

    /**
     * 特定のURLに移動します。
     *
     * @param url
     */
    public void goTo(String url) {
        currentDriver.get(url);
    }

    /**
     * 指定されたCSSセレクタにマッチする要素が見つかるまでアクセスを繰り返します。
     *
     * @param url
     * @param cssSelector
     * @return
     */
    public WebElement goToUrlUntilElementFound(String url, String cssSelector) {
        try {
            WebElement element = currentDriver.findElement(By.cssSelector(cssSelector));
            if (element.isDisplayed()) {
                return element;
            }
        } catch (Exception e) {
            // ignore
        }

        until((WebDriver iDriver) -> {
            iDriver.get(url);
            WebElement element = iDriver.findElement(By.cssSelector(cssSelector));
            return element.isDisplayed();
        });
        return currentDriver.findElement(By.cssSelector(cssSelector));
    }

    /**
     * 要素が削除されるまで待機します。
     *
     * @param cssSelector
     */
    public void waitUntilElementDeleted(String cssSelector) {
        until((WebDriver iDriver) -> {
            try {
                iDriver.findElement(By.cssSelector(cssSelector));
            } catch (NoSuchElementException e) {
                return true;
            }
            return false;
        });
    }

    public void waitUntilElementCountMatched(String cssSelector, int count) {
        until((WebDriver iDriver) -> {
            try {
                List<WebElement> elements = iDriver.findElements(By.cssSelector(cssSelector));
                return elements.size() == count;
            } catch (NoSuchElementException e) {
                return false;
            }
        });
    }

    /**
     * 要素が非表示になるまで待機します。
     *
     * @param cssSelector
     */
    public void waitUntilElementHided(String cssSelector) {
        try {
            TimeUnit.SECONDS.sleep(1);
        } catch (InterruptedException e) {
            throw new RuntimeException(e);
        }
        until((WebDriver iDriver) -> {
            try {
                WebElement element = iDriver.findElement(By.cssSelector(cssSelector));
                return !element.isDisplayed();
            } catch (StaleElementReferenceException e) {
                return true;
            }
        });
    }

    /**
     * 特定の文字列が画面上に現れるまで、画面をリロードし続けます。
     *
     * @param url
     * @param text
     */
    public void reloadUntilTextNotFound(String url, String text) {
        until((WebDriver iDriver) -> {
            iDriver.get(url);
            WebElement element = iDriver.findElement(By.cssSelector("body"));
            return !element.getText().contains(text);
        });
    }

    /**
     * 指定されたテキストがなくなるまでアクセスを繰り返します。
     *
     * @param url
     * @param text
     * @return
     */
    public void goToUrlUntilTextNotFound(String url, String text) {
        until((WebDriver iDriver) -> {
            iDriver.get(url);
            return !iDriver.findElement(By.tagName("body")).getText().contains(text);
        });
    }


    /**
     * 現在のURLにアクセスします。
     *
     * @return
     */
    public String getCurrentUrl() {
        return currentDriver.getCurrentUrl();
    }

    /**
     * 指定されたCSSセレクタにマッチする要素を取得します。
     *
     * @param cssSelector
     * @return
     */
    public List<WebElement> findBySelector(String cssSelector) {
        return currentDriver.findElements(By.cssSelector(cssSelector));
    }

    /**
     * CSSセレクタで指定した画面上の要素をクリックします。
     *
     * @param cssSelector
     */
    public void click(String cssSelector) {
        exists(cssSelector);
        until((WebDriver iDriver) -> {
            WebElement elem = iDriver.findElement(By.cssSelector(cssSelector));
            elem.click();
            return true;
        });
    }

    public String getDemoURL(String cssSelector, String href) {
        exists(cssSelector);
        AtomicReference<String> url = new AtomicReference<>();
        until((WebDriver iDriver) -> {
            for (WebElement elem : iDriver.findElements(By.cssSelector(cssSelector))) {
                if (elem.getAttribute("href").contains(href)) {
                    url.set(elem.getAttribute("href"));
                    return true;
                }
            }
            return false;
        });
        return url.get();
    }

    public void clickUntilElementExist(String clickCssSelector, String existenceCssSelector) {
        clickUntilElementExist(clickCssSelector, existenceCssSelector, 0, 1);
    }

    public void clickUntilElementExist(String clickCssSelector, String existenceCssSelector, long sleepTime, long retryCount) {
        exists(clickCssSelector);
        AtomicLong counter = new AtomicLong(retryCount);

        while (counter.get() > 0) {
            try {
                until((WebDriver iDriver) -> {
                    WebElement elem = iDriver.findElement(By.cssSelector(clickCssSelector));
                    elem.click();
                    try {
                        TimeUnit.SECONDS.sleep(sleepTime);
                    } catch (InterruptedException e) {
                        throw new RuntimeException(e);
                    }
                    WebElement existenceCheck = iDriver.findElement(By.cssSelector(existenceCssSelector));
                    return existenceCheck.isDisplayed();
                });
                return;
            } catch (TimeoutException e) {
                counter.decrementAndGet();
            }
        }
    }

    public void clickUntilTextExist(String clickCssSelector, String text) {
        clickUntilTextExist(clickCssSelector, text, 1, 5);
    }
    public void clickUntilTextExist(String clickCssSelector, String text, long sleepTime, long retryCount) {
        AtomicLong counter = new AtomicLong(retryCount);
        while (counter.get() > 0) {
            try {
                try {
                    WebElement elem = currentDriver.findElement(By.cssSelector(clickCssSelector));
                    elem.click();
                } catch (ElementNotFoundException e) {
                }
                until((WebDriver iDriver) -> {
                    try {
                        TimeUnit.SECONDS.sleep(sleepTime);
                    } catch (InterruptedException e) {
                        throw new RuntimeException(e);
                    }
                    WebElement existenceCheck = iDriver.findElement(By.cssSelector("body"));
                    String innerText = existenceCheck.getText();
                    return innerText.contains(text);
                });
                return;
            } catch (TimeoutException e) {
                counter.decrementAndGet();
            }
        }
    }


    /**
     * CSSセレクタで指定した画面のinput(type=checkbox)の要素をチェックします。
     *
     * @param cssSelector
     */
    public void check(String cssSelector) {
        exists(cssSelector);
        until((WebDriver iDriver) -> {
            WebElement elem = iDriver.findElement(By.cssSelector(cssSelector));
            elem.click();
            return elem.getAttribute("checked") != null;
        });
    }

    /**
     * CSSセレクタで指定した画面上の要素に文字列を入力します。
     *
     * @param cssSelector
     * @param keys
     */
    public void sendKeys(String cssSelector, String keys) {
        exists(cssSelector);
        WebElement elem = wait.until((WebDriver iDriver) -> iDriver.findElement(By.cssSelector(cssSelector)));
        elem.sendKeys(keys);
    }

    /**
     * CSSセレクタで指定した画面上の要素が空ならば、文字列を入力します。
     * @param cssSelector
     * @param keys
     */
    public void sendKeysIfEmpty(String cssSelector, String keys) {
        exists(cssSelector);
        WebElement elem = wait.until((WebDriver iDriver) -> iDriver.findElement(By.cssSelector(cssSelector)));
        if (elem.getAttribute("value").equals("")) {
            elem.sendKeys(keys);
        }
    }

    /**
     * CSSセレクタで指定した画面上の要素の存在を確認します。
     *
     * @param cssSelector
     */
    public void exists(String cssSelector) {
        wait.until((WebDriver iDriver) -> iDriver.findElement(By.cssSelector(cssSelector)).isDisplayed());
    }

    /**
     * 指定したURLに移動しているかどうかを検証します。
     *
     * @param url
     */
    public void verifyCurrentURL(String url) {
        until((WebDriver iDriver) -> iDriver.getCurrentUrl().contains(url));
    }

    public void verifyNotErrorPage() {
        WebElement body = currentDriver.findElement(By.cssSelector("body"));
        String text = body.getText();
        if (text.contains("お探しのページは見つかりません") || text.contains("ただいまサイトが大変混み合っております")) {
            throw new RuntimeException("エラーページです!: " + text);
        }
    }

    /**
     * CSSセレクタで指定した画面上の要素からテキストを取得します。
     * @param cssSelector
     * @return
     */
    public String getText(String cssSelector) {
        return currentDriver.findElement(By.cssSelector(cssSelector)).getText();
    }

    /**
     * CSSセレクタで指定した要素がenableであることを確認します。
     *
     * @param cssSelector
     */
    public void untilEnabled(String cssSelector) {
        until((WebDriver iDriver) -> iDriver.findElement(By.cssSelector(cssSelector)).getAttribute("disabled") == null);
    }

    /**
     * 指定されたgoogle plusのアカウントでログインします。
     *
     * @param mail
     * @param passwd
     * @param brandHomeUrl
     * @throws InterruptedException
     */
    public void loginByGooglePlus(String mail, String passwd, String brandHomeUrl) throws InterruptedException {
        loginByGooglePlus(mail, passwd, brandHomeUrl, true);
    }

    public void loginByGooglePlus(String mail, String passwd, String brandHomeUrl, boolean gotoLoginPage) throws InterruptedException {
        if (gotoLoginPage) {
            // goto login page
            currentDriver.get(brandHomeUrl + "my/login");

            // goto google plus login page
            click(".btnSnsGp1 a");
        }

        String currentUrl = currentDriver.getCurrentUrl();
        if (!currentUrl.startsWith("https://accounts.google.com/o/oauth2/auth")) {
            if (!currentUrl.startsWith("https://accounts.google.com/ServiceLogin")) {
                throw new RuntimeException("Invalid URL:" + currentUrl);
            }

            // enter e-mail
            sendKeys("#Email", mail);
            click("#next");

            // enter password
            sendKeys("#Passwd", passwd);
            click("#signIn");
        }

        // approve privacy
        untilEnabled("#submit_approve_access");
        click("#submit_approve_access");

        // validate the result
        until((WebDriver iDriver) -> iDriver.getCurrentUrl().startsWith(brandHomeUrl));
    }

    /**
     * 新モニからログアウトします。
     *
     * @param url
     */
    public void logout(String url) {
        click("a.logout");
        until((WebDriver iDriver) -> iDriver.getCurrentUrl().equals(url));
    }

    /**
     * 特定の操作を繰り返します。
     *
     * @param isTrue
     */
    public void until(Predicate<WebDriver> isTrue) {
       wait.until(isTrue);
    }

    public String queryAsString(String sql) {
        List<Map<String, String>> entities = query(sql);
        return entities.toString();
    }

    /**
     * SQLでデータベースを検索し、結果をMapに詰めて返します。
     *
     * @param sql
     * @return
     */
    private List<Map<String, String>> query(String sql) {
        try (Statement statement = connection.createStatement();
             ResultSet resultSet = statement.executeQuery(sql)) {
            ResultSetMetaData metaData = resultSet.getMetaData();
            List<Map<String, String>> entities = new ArrayList<>();
            while (resultSet.next()) {
                Map<String, String> entity = new TreeMap<>();
                for (int i = 1; i <= metaData.getColumnCount(); i++) {
                    entity.put(metaData.getColumnName(i), resultSet.getString(i));
                }
                entities.add(entity);
            }
            return entities;
        } catch (SQLException e) {
            throw new RuntimeException(e);
        }
    }

    public String[] openCampaign(String brandHomeUrl) {
        return openCampaign(brandHomeUrl, false, false);
    }

    /**
     * 新しい標準構成のキャンペーンをオープンします。
     *
     * @param brandHomeUrl
     * @return
     */
    public String[] openCampaign(String brandHomeUrl, boolean isRestrictedBySNS, boolean isDemoMode) {
        /// go to the skeleton page.
        goTo(brandHomeUrl + "admin-cp/edit_setting_skeleton");

        // create a skeleton.
        click("#skeleton_url");
        verifyCurrentURL(brandHomeUrl + "admin-cp/edit_customize_skeleton");

        // confirm the skeleton.
        click(".newSkeletonSubmitButton");

        // confirm the basic configuration.
        sendKeys(".inputTitle", "キャンペーン告知 STG IT " + (new Date()));

        // Set campaign's thumbnail
        String filePath = System.getProperty("user.dir") + "/../../docroot_static/img/base/imgCpDummy360.png";
        sendKeys(".actionImage0", filePath);

        Calendar calendar = Calendar.getInstance();
        calendar.add(Calendar.MINUTE, 1);
        calendar.add(Calendar.SECOND, 3);
        String hour = String.format("%02d", calendar.get(Calendar.HOUR_OF_DAY));
        String minute = String.format("%02d", calendar.get(Calendar.MINUTE));

        click(String.format("select[name=\"openTimeHH\"] option[value=\"%s\"]", hour));
        click(String.format("select[name=\"openTimeMM\"] option[value=\"%s\"]", minute));

        if (isRestrictedBySNS) {
            click("#join_limit_sns_flg_1");
            click("#join_limit_sns\\[\\]_4");
        }

        click("#submit");
        exists("#editButton");

        String[] urlFragments1 = getCurrentUrl().split("/");
        String campaignId = urlFragments1[urlFragments1.length - 1].replace("?mid=action-saved", "");

        // go to the attract configuration.
        click(".jsAttractModule a");
        verifyCurrentURL(brandHomeUrl + "admin-cp/edit_setting_attract");

        // confirm the page.
        click("#submit");
        exists("#editButton");

        // go to the entry page.
        click("li.stepDetail_require > ul > li:nth-child(1) > a");
        click("#submit");
        exists("#editButton");

        // goTo ids.
        String[] urlFragments2 = getCurrentUrl().split("/");
        String actionId = urlFragments2[urlFragments2.length - 1].replace("?mid=action-saved", "");

        // go to the address page.
        click("li.stepDetail_require > ul > li:nth-child(2) > a");
        click("#submit");
        exists("#editButton");

        // go to the join finish page.
        click("li.stepDetail_require > ul > li:nth-child(3) > a");
        click("#submit");
        exists("#editButton");

        String[] result = {campaignId, actionId};
        if(isDemoMode) {
            // confirm the demo mode opening dialog.
            click("a[href=\"#modal_demo_confirm\"]");
            check("#demo_condition_1");
            check("#demo_condition_2");
            check("#demo_condition_3");
            click("#demoConfirmButton");

            // verify the current url.
            verifyCurrentURL(brandHomeUrl + "admin-cp/public_cps");

            return result;
        }

        // open the campaign.
        click(".btn3 .jsOpenModal");
        check("input[name=\"condition1\"]");
        check("input[name=\"condition2\"]");
        check("input[name=\"condition3\"]");
        check("input[name=\"condition4\"]");
        check("input[name=\"condition5\"]");
        click("#scheduleCp");

        // verify the current url.
        verifyCurrentURL(brandHomeUrl + "admin-cp/public_cps");

        if (!isDemoMode) {
            // go to campaign LP and verify the button.
            commandStrategy.openCampaign(campaignId);
        }

        return result;
    }

    public String[] openNonIncentiveCampaign(String brandHomeUrl) {
        /// go to the skeleton page.
        goTo(brandHomeUrl + "admin-cp/edit_setting_skeleton");

        // select non incentive campaign
        check("section.makeStepType:nth-child(5) > h1 > label > input");

        // create a skeleton.

        click("section.makeStepType:nth-child(5) > div.makeStepTypeCont > section.makeNewStepList > p > span > a");
        verifyCurrentURL(brandHomeUrl + "admin-cp/edit_customize_skeleton");

        // confirm the skeleton.
        click(".newSkeletonSubmitButton");

        // confirm the basic configuration.
        sendKeys(".inputTitle", "キャンペーン告知 STG IT ");

        Calendar calendar = Calendar.getInstance();
        calendar.add(Calendar.MINUTE, 1);
        calendar.add(Calendar.SECOND, 3);
        String hour = String.format("%02d", calendar.get(Calendar.HOUR_OF_DAY));
        String minute = String.format("%02d", calendar.get(Calendar.MINUTE));

        click(String.format("select[name=\"openTimeHH\"] option[value=\"%s\"]", hour));
        click(String.format("select[name=\"openTimeMM\"] option[value=\"%s\"]", minute));

        click("#submit");
        exists("#editButton");

        String[] urlFragments1 = getCurrentUrl().split("/");
        String campaignId = urlFragments1[urlFragments1.length - 1].replace("?mid=action-saved", "");

        // go to the attract configuration.
        click(".jsAttractModule a");
        verifyCurrentURL(brandHomeUrl + "admin-cp/edit_setting_attract");

        // confirm the page.
        click("#submit");
        exists("#editButton");

        // go to the questionnaire page.
        click("li.stepDetail_require > ul > li:nth-child(1) > a");

        sendKeys("#text_area", "test");

        click("#moduleEnqueteList > li.addQuestion:nth-child(1) > a");
        exists(".moduleEnqueteDetail1");
        sendKeys("li.moduleEnqueteDetail1 > p > label > input", "asdf");

        click("#submit");
        exists("#editButton");

        // goTo ids.
        String[] urlFragments2 = getCurrentUrl().split("/");
        String actionId = urlFragments2[urlFragments2.length - 1].replace("?mid=action-saved", "");

        // go to the join finish page.
        click("li.stepDetail_require > ul > li:nth-child(2) > a");
        click("#submit");
        exists("#editButton");

        String[] result = {campaignId, actionId};

        // open the campaign.
        click(".btn3 .jsOpenModal");
        check("input[name=\"condition1\"]");
        check("input[name=\"condition2\"]");
        check("input[name=\"condition3\"]");
        click("#scheduleCp");

        // verify the current url.
        verifyCurrentURL(brandHomeUrl + "admin-cp/public_cps");

        // go to campaign LP and verify the button.
        commandStrategy.openCampaign(campaignId);

        return result;
    }

    public void verifyCampaignCounts(String brandHomeUrl, String[] campaignKeys) {
        goTo(brandHomeUrl + "/admin-cp/edit_action/" + campaignKeys[0] + "/" + campaignKeys[1]);

        // verify the counts.
        // しょうがないので、一旦条件を緩める
        long countOfMoreThanOne = findBySelector(".flowDetail_focus .num")
                .stream()
                .filter(e -> e.getText().equals("1"))
                .map(e -> e.getText())
                .count();
        assertTrue(countOfMoreThanOne > 5);
    }

    public void joinCampaignWhenLoggedIn(String brandHomeUrl, String campaignId) {
        joinCampaignWhenLoggedIn(brandHomeUrl, campaignId, false);
    }

    public void joinCampaignWhenLoggedIn(String brandHomeUrl, String campaignId, boolean isRestrictedBySNS) {
        // go to campaign LP and verify the button.
        WebElement joinButton = null;
        if (!isRestrictedBySNS) {
            joinButton = goToUrlUntilElementFound(brandHomeUrl + "campaigns/" + campaignId, "a.large1[href=\"javascript:void(0);\"]");
        } else {
            joinButton = goToUrlUntilElementFound(brandHomeUrl + "campaigns/" + campaignId, ".btnSnsGp1 a");
        }

        // participate in the campaign.
        joinButton.click();
        verifyCurrentURL(brandHomeUrl + "messages/thread/" + campaignId);

        // enter address.
        sendKeysIfEmpty("input[name=\"lastName\"]", "モニプラ");
        sendKeysIfEmpty("input[name=\"firstName\"]", "テスト");
        sendKeysIfEmpty("input[name=\"lastNameKana\"]", "もにぷら");
        sendKeysIfEmpty("input[name=\"firstNameKana\"]", "てすと");
        sendKeysIfEmpty("input[name=\"zipCode1\"]", "130");
        sendKeysIfEmpty("input[name=\"zipCode2\"]", "0011");
        sendKeysIfEmpty("input[name=\"telNo1\"]", "090");
        sendKeysIfEmpty("input[name=\"telNo2\"]", "5555");
        sendKeysIfEmpty("input[name=\"telNo3\"]", "4444");
        click(".cmd_execute_shipping_address_action");
        exists(".message_backToMoniplaPr");
        try {
            TimeUnit.SECONDS.sleep(1);
        } catch (InterruptedException e) {
            throw new RuntimeException(e);
        }
    }

    public void joinNonIncentiveCpWhenLoggedIn(String brandHomeUrl, String campaignId) {
        WebElement joinButton = goToUrlUntilElementFound(brandHomeUrl + "campaigns/" + campaignId, "a.large1[href=\"javascript:void(0);\"]");

        sendKeysIfEmpty(".openingCpActionForm > dl > dd > textarea", "asdf");
        joinButton.click();
        verifyCurrentURL(brandHomeUrl + "messages/thread/" + campaignId);
        exists(".message_backToMoniplaPr");

        try {
            TimeUnit.SECONDS.sleep(1);
        } catch (InterruptedException e) {
            throw new RuntimeException(e);
        }
    }

    public void joinCampaignWhenNotLoggedIn(String brandHomeUrl, String campaignId, String mail, String password) throws InterruptedException {
        // go to campaign LP and verify the button.
        click(".jsOldLoginOauth .btnSnsGp1 a");;

        loginByGooglePlus(mail, password, brandHomeUrl, false);
        verifyCurrentURL(brandHomeUrl + "messages/thread/" + campaignId);

        // enter address.
        sendKeysIfEmpty("input[name=\"lastName\"]", "モニプラ");
        sendKeysIfEmpty("input[name=\"firstName\"]", "テスト");
        sendKeysIfEmpty("input[name=\"lastNameKana\"]", "もにぷら");
        sendKeysIfEmpty("input[name=\"firstNameKana\"]", "てすと");
        sendKeysIfEmpty("input[name=\"zipCode1\"]", "130");
        sendKeysIfEmpty("input[name=\"zipCode2\"]", "0011");
        sendKeysIfEmpty("input[name=\"telNo1\"]", "090");
        sendKeysIfEmpty("input[name=\"telNo2\"]", "5555");
        sendKeysIfEmpty("input[name=\"telNo3\"]", "4444");
        click(".cmd_execute_shipping_address_action");
        exists(".message_backToMoniplaPr");
        try {
            TimeUnit.SECONDS.sleep(1);
        } catch (InterruptedException e) {
            throw new RuntimeException(e);
        }
    }

    public void sendMessageToUser(String brandHomeUrl, String[] campaignKeys, String gpAdminUserId) {
        goTo(brandHomeUrl + "admin-cp/show_user_list/" + campaignKeys[0] + "/" + campaignKeys[1]);
        clickUntilElementExist("li[style=\"display:list-item\"] > a:first-child", ".sortBox[style=\"display: block;\"] a[data-clear_type]", 1, 10);
        click(".sortBox[style=\"display: block;\"] a[data-clear_type]");
        check("input[value=\"" + gpAdminUserId + "\"]");
        click("a[data-update_type=\"insert\"]");
        waitUntilElementHided(".blockOverlay");

        goTo(brandHomeUrl + "admin-cp/setting_message_option/" + campaignKeys[1]);
        click("#submitReservationFix");

        takeScreenShot("submitReservationFix");

        exists("#submitReservationUnFix");

        takeScreenShot("submitReservationUnFix");

        click("p[data-btn_type=\"send_mail\"] a");

        takeScreenShot("btn_type");

        click("#submitReservationSchedule");

        takeScreenShot("submitReservationSchedule");

        exists("#submitReservationUnSchedule");
    }

    public String takeScreenShot(String className) {
        String filePath = className + ".png";
        TakesScreenshot screenshot = (TakesScreenshot) currentDriver;
        byte[] screenshotFile = screenshot.getScreenshotAs(OutputType.BYTES);
        File newImgFile = new File(filePath);
        try {
            FileUtils.writeByteArrayToFile(newImgFile, screenshotFile);
        } catch (IOException e) {
            throw new RuntimeException(e);
        }
        return newImgFile.getAbsolutePath();
    }

    public void dragAndDrop(WebElement start, WebElement end) {
        Actions actions = new Actions(currentDriver);
        Action build = actions.clickAndHold(start).moveToElement(end).release().build();
        build.perform();
    }
}
