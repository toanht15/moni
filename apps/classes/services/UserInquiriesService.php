<?php
AAFW::import('jp.aainc.classes.services.StreamService');
AAFW::import('jp.aainc.classes.services.UserService');

class UserInquiriesService extends aafwServiceBase
{

    public function __construct()
    {
        $this->user_inquiries = $this->getModel('UserInquiries');
    }

    public function createInquiry(array $params = array()) {
        $user_inquiry               = $this->user_inquiries->createEmptyObject();
        $user_inquiry->name         = $params['name'];
        $user_inquiry->mail_address = $params['mail_address'];
        $user_inquiry->content      = $params['content'];
        $user_inquiry->barandId     = $params['brandId'];
        $this->user_inquiries->save($user_inquiry);
    }

    public function sendInquiryMail (array $params = array(), $brand, BrandsUsersRelation $brandsUsers = null) {

        $mail  = new MailManager(array('FromAddress' => $params['mail_address'], 'Envelope' => $params['mail_address']));
        $mailParams['BRAND_NAME']      = $brandsUsers ? $brandsUsers->getBrand()->name : $brand->name;
        $mailParams['NAME']            = $params['name'];
        $mailParams['MAIL_ADDRESS']    = $params['mail_address'];
        $mailParams['CONTENT']         = $params['content'];
        $mailParams['DATE']            = date( "Y/m/d H:i:s" );
        $mailParams['HTTP_REFERER']    = $params['refer'];
        $mailParams['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
        $mailParams['AA_ID']           = $brandsUsers ? $brandsUsers->getUser()->monipla_user_id : '';
        $mailParams['USER_ID']         = $brandsUsers ? $brandsUsers->user_id : '';
        $mailParams['USER_NO']         = $brandsUsers ? $brandsUsers->no : '';
        $mail->loadMailContent('user_inquiry_mail');
        $mail->sendNow( config('Mail.Support'), $mailParams);
    }
}