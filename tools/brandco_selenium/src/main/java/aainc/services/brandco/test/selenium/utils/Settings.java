package aainc.services.brandco.test.selenium.utils;

import java.io.IOException;
import java.util.Properties;

public class Settings {

    // 一般ユーザー
    public static final String GP_EMAIL = "monipla.stg.test@gmail.com";
    public static final String GP_PASSWORD = "allied55";
    public static final String GP_USER_ID = "208";


    // Adminユーザー
    public static final String GP_ADMIN_EMAIL = "monipla.stg.super.user@gmail.com";
    public static final String GP_ADMIN_PASSWORD = "allied55";
    public static final String GP_ADMIN_USER_ID = "795";
    public static final String GP_ADMIN_FAN_NO = "1";

    private static Settings SETTINGS = new Settings();

    private final Properties properties = new Properties();

    public Settings() {
        try {
            properties.load(this.getClass().getClassLoader().getResourceAsStream("settings.properties"));
        } catch (IOException e) {
            throw new RuntimeException(e);
        }
    }

    public String getBrandHomeUrl() {
        return properties.getProperty("brand.home.url");
    }

    public String getConnectionUrl() {
        return properties.getProperty("db.connection.url");
    }

    public String getConnectionUser() {
        return properties.getProperty("db.connection.user");
    }

    public String getConnectionPassword() {
        return properties.getProperty("db.connection.password");
    }

    public String getBatchHost() {
        return properties.getProperty("batch.host");
    }

    public static Settings getInstance() {
        return SETTINGS;
    }
}
