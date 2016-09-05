<?php
/**
 * Autogenerated by Thrift Compiler (0.8.0)
 *
 * DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
 *  @generated
 */
include_once $GLOBALS['THRIFT_ROOT'].'/Thrift.php';

include_once $GLOBALS['THRIFT_ROOT'].'/packages/Monipla/Monipla_types.php';

interface MoniplaIf {
  public function putSocialAccount($account);
  public function changeMailAddress($account);
  public function changeMailAddressByUser($userQuery);
  public function changeName($userQuery);
  public function login($parameter);
  public function createAuthorizationCode($parameter);
  public function exchangeSNSAccessToken($parameter);
  public function getApplication($query);
  public function getUsingApplications($parameter);
  public function isValidScope($query);
  public function getAccessTokenByApplicationId($query);
  public function getScopes($query);
  public function getPermissions($query);
  public function findBySocialAccount($account);
  public function findByMail($mailAddress);
  public function resetPassword($parameter);
  public function setNewPasswordPassThough($parameter);
  public function getUser($accessToken);
  public function getUserByQuery($query);
  public function getUsersByMailAddress($query);
  public function checkAccount($parameter);
  public function addCouponCodes($couponCodes);
  public function createCouponProvisions($parameter);
  public function addPoint($point);
  public function entryUser($params);
  public function subtractPoint($point);
  public function getPointHistory($pointBookQuery);
  public function getPlusPoint($pointBookQuery);
  public function getMinusPoint($pointBookQuery);
  public function getSummaryPoint($account);
  public function getSummaryPointByUser($query);
  public function getNearExpirationPoint($query);
  public function getCouponCodes($couponCodeQuery);
  public function getSummaryCouponCode();
  public function mergeAccount($params);
  public function stopServer();
  public function deleteSocialAccount($params);
  public function exchangeAmazon();
  public function getShippingAddress($address);
  public function backdoorLogin($parameter);
  public function enterpriseBackdoorLogin($parameter);
  public function createAccessToken($parameter);
  public function refreshAccessToken($parameter);
  public function checkBackdoorLogin($parameter);
  public function checkEnterpriseBackdoorLogin($parameter);
  public function updateAddress($address);
  public function sendNotification($notification);
  public function getNotification($notification);
  public function getNotificationCount($notification);
  public function markReadNotification($query);
  public function entryNews($news);
  public function getNews($query);
  public function getUserAttributeMasters($query);
  public function getUserAttributes($query);
  public function addUserAttributes($query);
  public function validateUserAttributes($query);
  public function getSNSAccessToken($query);
  public function setSNSAccessToken($params);
  public function setSNSAccessTokenToCode($params);
  public function withdrawUser($params);
  public function withdrawUserWithReason($params);
  public function sendMergeCandidateNotification($params);
  public function getRemindingSettings($params);
  public function getNotificationsForRemind($params);
  public function deleteRemindQueues($params);
  public function createRemindQueues($params);
  public function getOperationQueues($params);
  public function sendOperationQueue($params);
  public function changeReminding($params);
  public function getRemindingSetting($params);
  public function addRemindingSetting($params);
  public function checkJedisConnection();
  public function getCouponTypes();
  public function getSocialMediaTypes();
  public function checkEnoughCoupons();
  public function getUserRecursive($params);
  public function searchEnterprises($params);
  public function searchAgents($params);
  public function searchBrands($params);
  public function saveEnterprise($params);
  public function saveAgent($params);
  public function saveBrand($params);
  public function updateProfile($params);
  public function saveBlog($params);
  public function removeApplication($params);
  public function saveFriendCount($params);
  public function saveTweet($params);
  public function setOptin($params);
  public function getOptin($query);
}

$GLOBALS['THRIFT_AUTOLOAD']['moniplaclient'] = 'Monipla/Monipla.Monipla.client.php';
// HELPER FUNCTIONS AND STRUCTURES

?>
