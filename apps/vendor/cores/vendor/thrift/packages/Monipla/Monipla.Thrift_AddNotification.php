<?php
/**
 *  @generated
 */
class Thrift_AddNotification {
  static $_TSPEC;

  public $socialAccount = null;
  public $clientId = null;
  public $iconPath = null;
  public $title = null;
  public $message = null;
  public $lead = null;
  public $badgeColor = null;
  public $badgeText = null;
  public $url = null;
  public $remindFlg = null;
  public $noticeUserId = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'socialAccount',
          'type' => TType::STRUCT,
          'class' => 'Thrift_SocialAccount',
          ),
        2 => array(
          'var' => 'clientId',
          'type' => TType::STRING,
          ),
        3 => array(
          'var' => 'iconPath',
          'type' => TType::STRING,
          ),
        4 => array(
          'var' => 'title',
          'type' => TType::STRING,
          ),
        5 => array(
          'var' => 'message',
          'type' => TType::STRING,
          ),
        6 => array(
          'var' => 'lead',
          'type' => TType::STRING,
          ),
        7 => array(
          'var' => 'badgeColor',
          'type' => TType::I16,
          ),
        8 => array(
          'var' => 'badgeText',
          'type' => TType::STRING,
          ),
        9 => array(
          'var' => 'url',
          'type' => TType::STRING,
          ),
        10 => array(
          'var' => 'remindFlg',
          'type' => TType::I16,
          ),
        11 => array(
          'var' => 'noticeUserId',
          'type' => TType::I64,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['socialAccount'])) {
        $this->socialAccount = $vals['socialAccount'];
      }
      if (isset($vals['clientId'])) {
        $this->clientId = $vals['clientId'];
      }
      if (isset($vals['iconPath'])) {
        $this->iconPath = $vals['iconPath'];
      }
      if (isset($vals['title'])) {
        $this->title = $vals['title'];
      }
      if (isset($vals['message'])) {
        $this->message = $vals['message'];
      }
      if (isset($vals['lead'])) {
        $this->lead = $vals['lead'];
      }
      if (isset($vals['badgeColor'])) {
        $this->badgeColor = $vals['badgeColor'];
      }
      if (isset($vals['badgeText'])) {
        $this->badgeText = $vals['badgeText'];
      }
      if (isset($vals['url'])) {
        $this->url = $vals['url'];
      }
      if (isset($vals['remindFlg'])) {
        $this->remindFlg = $vals['remindFlg'];
      }
      if (isset($vals['noticeUserId'])) {
        $this->noticeUserId = $vals['noticeUserId'];
      }
    }
  }

  public function getName() {
    return 'Thrift_AddNotification';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::STRUCT) {
            $this->socialAccount = new Thrift_SocialAccount();
            $xfer += $this->socialAccount->read($input);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->clientId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->iconPath);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->title);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->message);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 6:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->lead);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 7:
          if ($ftype == TType::I16) {
            $xfer += $input->readI16($this->badgeColor);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 8:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->badgeText);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 9:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->url);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 10:
          if ($ftype == TType::I16) {
            $xfer += $input->readI16($this->remindFlg);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 11:
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->noticeUserId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('Thrift_AddNotification');
    if ($this->socialAccount !== null) {
      if (!is_object($this->socialAccount)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('socialAccount', TType::STRUCT, 1);
      $xfer += $this->socialAccount->write($output);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->clientId !== null) {
      $xfer += $output->writeFieldBegin('clientId', TType::STRING, 2);
      $xfer += $output->writeString($this->clientId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->iconPath !== null) {
      $xfer += $output->writeFieldBegin('iconPath', TType::STRING, 3);
      $xfer += $output->writeString($this->iconPath);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->title !== null) {
      $xfer += $output->writeFieldBegin('title', TType::STRING, 4);
      $xfer += $output->writeString($this->title);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->message !== null) {
      $xfer += $output->writeFieldBegin('message', TType::STRING, 5);
      $xfer += $output->writeString($this->message);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->lead !== null) {
      $xfer += $output->writeFieldBegin('lead', TType::STRING, 6);
      $xfer += $output->writeString($this->lead);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->badgeColor !== null) {
      $xfer += $output->writeFieldBegin('badgeColor', TType::I16, 7);
      $xfer += $output->writeI16($this->badgeColor);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->badgeText !== null) {
      $xfer += $output->writeFieldBegin('badgeText', TType::STRING, 8);
      $xfer += $output->writeString($this->badgeText);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->url !== null) {
      $xfer += $output->writeFieldBegin('url', TType::STRING, 9);
      $xfer += $output->writeString($this->url);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->remindFlg !== null) {
      $xfer += $output->writeFieldBegin('remindFlg', TType::I16, 10);
      $xfer += $output->writeI16($this->remindFlg);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->noticeUserId !== null) {
      $xfer += $output->writeFieldBegin('noticeUserId', TType::I64, 11);
      $xfer += $output->writeI64($this->noticeUserId);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
