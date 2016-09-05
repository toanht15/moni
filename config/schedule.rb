# Use this file to easily define all of your cron jobs.
#
# It's helpful, but not entirely necessary to understand cron before proceeding.
# http://en.wikipedia.org/wiki/Cron

# Example:
#
# set :output, "/path/to/my/cron_log.log"
#
# every 2.hours do
#   command "/usr/bin/some_great_command"
#   runner "MyModel.some_method"
#   rake "some:great:rake:task"
# end
#
# every 4.days do
#   runner "AnotherModel.prune_old_records"
# end

# Learn more: http://github.com/javan/whenever

#set :job_template, "/usr/bin/php ':job'"
#set :dev_null, ">/dev/null 2>&1"
set :output, {:error => nil, :standard => nil}
set :job_template, nil

every '* * * * * ' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/MailQueueMailSender.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/AutoUpdateCpStatus.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/UserMessageDelivery.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/UserAnnounceDelivery.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/SetBrandsUsersNo.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/SetCommentsUsersNo.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/UpdatePageEntryPanelCache.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/ShareMultiPostSns.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/UpdateMoniplaCpInfo.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/CheckSyndotHealth.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/PaymentOrderCompleteMail.php"

end

every '5 * * * * ' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/SegmentingUserData.php"
end

every '10 * * * * ' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/CpLostUserDelivery.php"
end

every '30 0 * * * ' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/CheckSegmentProvisionUsersCount.php"
end

every '35 * * * * ' do
  command "/usr/bin/php /var/www/html/brandco/apps/lib/AAFW.php bat jp.aainc.actions.cli.Crawler twitter_user_timeline_user_auth"
  command "/usr/bin/php /var/www/html/brandco/apps/lib/AAFW.php bat jp.aainc.actions.cli.Crawler facebook_user_post_user_auth"
  command "/usr/bin/php /var/www/html/brandco/apps/lib/AAFW.php bat jp.aainc.actions.cli.Crawler youtube_user_post_user_auth"
  command "/usr/bin/php /var/www/html/brandco/apps/lib/AAFW.php bat jp.aainc.actions.cli.Crawler instagram_user_recent_media"
end

every '55 * * * * ' do
  command "/usr/bin/php /var/www/html/brandco/apps/lib/AAFW.php bat jp.aainc.actions.cli.Crawler facebook_get_posts_detail_user_auth"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/CloseBrand.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/InitializeCpInstagramHashtag.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/VerifyCpInstagramHashtagPostRecentMedia.php"
end

every '00 0 * * * ' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/SetBrandFansScore.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/SummaryFaceBookActionLikeCount.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/SummaryTwitterActionRetweetCount.php"
end

every '20 0 * * * ' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/SummaryFacebookActionCommentCount.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/SummaryTwitterActionReplyCount.php"
end

every 2.hours do
  command "/usr/bin/php /var/www/html/brandco/apps/lib/AAFW.php bat jp.aainc.actions.cli.RecoveringManagerKpi"
end

every '10 4,5,6,7 * * * ' do
  command "/usr/bin/php /var/www/html/brandco/apps/lib/AAFW.php bat jp.aainc.actions.cli.ManagerKpi"
end

every '00 8 * * * ' do
  command "/usr/bin/php /var/www/html/brandco/apps/lib/AAFW.php bat jp.aainc.actions.cli.ManagerBrandKpi"
  command "/usr/bin/php /var/www/html/brandco/apps/lib/AAFW.php bat jp.aainc.actions.cli.Crawler  update_social_account_info"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/PaymentRemindMail.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/PaymentCancelMail.php"

end

every '30 8 * * * ' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/DailyManagerKpi.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/DailyAlertMessage.php"
end

every '0 1,5,9,13,17 * * * ' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/UpdateMsgDeliveredCount.php"
end

every '30 0 * * * ' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/GetExternalFbEntries.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/GetExternalTwitterEntries.php"
end

every 1.day, :at => '1am' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/GetUsersCmtsFbPagePost.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/GetUsersLikesFbPage.php"
end

every 1.day, :at => '2am' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/SyncInstagramHashtagUserPost.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/UpdateDuplicateAddressCountBrandUser.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/UpdateDuplicateAddressCountCpUser.php"
end

every '40 2 * * * ' do
  command "/usr/bin/php /var/www/html/monipla-datasyncer/scruit sync"
end

every '0 2,6,10,14,18 * * * ' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/UpdateCpEntryCount.php"
end

every '0 3,7,11,15,19 * * * ' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/UpdateMsgReadCount.php"
end

every '10 3,7,11,15,19 * * * ' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/UpdateCpAnnounceCount.php"
end

every 6.hours do
  command "/usr/bin/php /var/www/html/brandco/apps/lib/AAFW.php bat jp.aainc.actions.cli.Crawler rss_fetch"
end

every 12.hours do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/LoggingPanelInfo.php"
end

every :month do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/DeleteDisconnectedSNS.php"
end

# every :day do
#   command "/usr/bin/php /var/www/html/brandco/apps/batch/DeleteConversionRecordOverMonth.php"
# end

every '* * * * * ' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/MoveConversionFromRedisToDB.php"
end

every '*/5 * * * * ' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/CopyTrackerDBToBC.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/PaymentCompleteMail.php"
end

every '*/10 * * * * ' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/SetBrandFansCount.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/PaymentStatusUpdate.php"
end

every '*/15 * * * * ' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/UpdateMessageAlertCheck.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/LongRunningQueryChecker.php"
end

every '*/20 * * * * ' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/SyncTweetPosts.php"
end

every '*/20 1-5 * * * ' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/GetTwitterReplyTweets.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/GetTwitterFollowingUsers.php"
end

every '*/20 1-8 * * * ' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/GetTwitterRetweets.php"
end

every '*/5 14-23,0-2 * * * ' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/WithdrawFan.php"
end

every :hour do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/UpdateCpInstagramFollowUserLogStatus.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/CheckConversionTag.php"
end

every 1.day, :at => '0am' do
   command "/usr/bin/php /var/www/html/brandco/apps/batch/StoreSocialAccountFollowerCount.php"
end

every 1.day, :at => '11am' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/CheckServicePerDay.php"
end

every 1.day, :at => '5am' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/DataImportRtoaster.php"
end

every 1.day, :at => '9am' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/CpStatusAlertDelivery.php"
end

every 1.day, :at => '10am' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/CheckUpdateCountBatchLog.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/CheckSSLCertificateExpirationDate.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/CheckInquiryStatusClosed.php 1"
end

every '30 23 * * *' do
   command "/usr/bin/php /var/www/html/brandco/apps/batch/DeletePhysicalSocialAccount.php"
end

every '0 10,16 * * 1-5' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/CheckInquiryStatusClosed.php 2"
end

every '0 12 * * 1-5' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/CheckInquiryStatusClosed.php 3"
end

every 1.day, :at => '14pm' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/UpdateDuplicateAddressCountCpUser.php limit_mode=y"
end

every 1.day, :at => '18pm' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/GetCpPageView.php"
end

every 1.days, :at => '01am' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/MailQueueRotator.php"
end

# グロースバッチ
every '40 1 * * *' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/RotateCpQuestionnaireQuestionAnswer.php"
  command "/usr/bin/php /var/www/html/brandco/apps/batch/RotateGrowthUser.php"
end

#update brand social profile
every 6.hours do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/UpdateBrandSocialProfile.php"
end

#send ads target
every 1.day, :at => '4am' do
  command "/usr/bin/php /var/www/html/brandco/apps/batch/SendAdsTargetUser.php"
end