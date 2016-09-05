<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class FacebookMarketingUser extends aafwEntityBase {
    public static $permissions = array(
        "1" => "ACCOUNT_ADMIN",
        "2" => "ADMANAGER_READ",
        "3" => "ADMANAGER_WRITE",
        "4" => "BILLING_READ",
        "5" => "BILLING_WRITE",
        "7" => "REPORTS"
    );

    public static $role = array(
        "1001" => "Administrator access",
        "1002" => "Advertiser (ad manager) access",
        "1003" => "Analyst access",
        "1004" => "Direct sales access"
    );

    public static $status = array(
        "1"     => "ACTIVE",
        "2"     => "DISABLED",
        "3"     => "UNSETTLED",
        "7"     => "PENDING_RISK_REVIEW",
        "9"     => "IN_GRACE_PERIOD",
        "100"   => "PENDING_CLOSURE",
        "101"   => "CLOSED",
        "102"   => "PENDING_SETTLEMENT",
        "201"   => "ANY_ACTIVE",
        "202"   => "ANY_CLOSED"
    );
}
