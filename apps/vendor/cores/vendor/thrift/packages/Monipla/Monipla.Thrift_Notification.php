<?php
/**
 *  @generated
 */
class Thrift_Notification {
  static $_TSPEC;

  public $id = null;
  public $applicationId = null;
  public $applicationName = null;
  public $clientId = null;
  public $title = null;
  public $message = null;
  public $lead = null;
  public $badgeColor = null;
  public $badgeText = null;
  public $url = null;
  public $iconPath = null;
  public $opened = null;
  public $dateTime = null;
  public $remindFlg = null;
  public $noticeUserId = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'id',
          'type' => TType::I64,
          ),
        2 => array(
          'var' => 'applicationId',
          'type' => TType::I64,
          ),
        3 => array(
          'var' => 'applicationName',
          'type' => TType::STRING,
          ),
        4 => array(
          'var' => 'clientId',
          'type' => TType::STRING,
          ),
        5 => array(
          'var' => 'title',
          'type' => TType::STRING,
          ),
        6 => array(
          'var' => 'message',
          'type' => TType::STRING,
          ),
        7 => array(
          'var' => 'lead',
          'type' => TType::STRING,
          ),
        8 => array(
          'var' => 'badgeColor',
          'type' => TType::I16,
          ),
        9 => array(
          'var' => 'badgeText',
          'type' => TType::STRING,
          ),
        10 => array(
          'var' => 'url',
          'type' => TType::STRING,
          ),
        11 => array(
          'var' => 'iconPath',
          'type' => TType::STRING,
          ),
        12 => array(
          'var' => 'opened',
          'type' => TType::I16,
          ),
        13 => array(
          'var' => 'dateTime',
          'type' => TType::STRING,
          ),
        14 => array(
          'var' => 'remindFlg',
          'type' => TType::I16,
          ),
        15 => array(
          'var' => 'noticeUserId',
          'type' => TType::I64,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['id'])) {
        $this->id = $vals['id'];
      }
      if (isset($vals['applicationId'])) {
        $this->applicationId = $vals['applicationId'];
      }
      if (isset($vals['applicationName'])) {
        $this->applicationName = $vals['applicationName'];
      }
      if (isset($vals['clientId'])) {
        $this->clientId = $vals['clientId'];
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
      if (isset($vals['iconPath'])) {
        $this->iconPath = $vals['iconPath'];
      }
      if (isset($vals['opened'])) {
        $this->opened = $vals['opened'];
      }
      if (isset($vals['dateTime'])) {
        $this->dateTime = $vals['dateTime'];
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
    return 'Thrift_Notification';
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
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->id);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->applicationId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->applicationName);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->clientId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->title);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 6:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->message);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 7:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->lead);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 8:
          if ($ftype == TType::I16) {
            $xfer += $input->readI16($this->badgeColor);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 9:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->badgeText);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 10:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->url);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 11:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->iconPath);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 12:
          if ($ftype == TType::I16) {
            $xfer += $input->readI16($this->opened);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 13:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->dateTime);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 14:
          if ($ftype == TType::I16) {
            $xfer += $input->readI16($this->remindFlg);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 15:
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
    $xfer += $output->writeStructBegin('Thrift_Notification');
    if ($this->id !== null) {
      $xfer += $output->writeFieldBegin('id', TType::I64, 1);
      $xfer += $output->writeI64($this->id);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->applicationId !== null) {
      $xfer += $output->writeFieldBegin('applicationId', TType::I64, 2);
      $xfer += $output->writeI64($this->applicationId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->applicationName !== null) {
      $xfer += $output->writeFieldBegin('applicationName', TType::STRING, 3);
      $xfer += $output->writeString($this->applicationName);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->clientId !== null) {
      $xfer += $output->writeFieldBegin('clientId', TType::STRING, 4);
      $xfer += $output->writeString($this->clientId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->title !== null) {
      $xfer += $output->writeFieldBegin('title', TType::STRING, 5);
      $xfer += $output->writeString($this->title);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->message !== null) {
      $xfer += $output->writeFieldBegin('message', TType::STRING, 6);
      $xfer += $output->writeString($this->message);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->lead !== null) {
      $xfer += $output->writeFieldBegin('lead', TType::STRING, 7);
      $xfer += $output->writeString($this->lead);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->badgeColor !== null) {
      $xfer += $output->writeFieldBegin('badgeColor', TType::I16, 8);
      $xfer += $output->writeI16($this->badgeColor);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->badgeText !== null) {
      $xfer += $output->writeFieldBegin('badgeText', TType::STRING, 9);
      $xfer += $output->writeString($this->badgeText);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->url !== null) {
      $xfer += $output->writeFieldBegin('url', TType::STRING, 10);
      $xfer += $output->writeString($this->url);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->iconPath !== null) {
      $xfer += $output->writeFieldBegin('iconPath', TType::STRING, 11);
      $xfer += $output->writeString($this->iconPath);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->opened !== null) {
      $xfer += $output->writeFieldBegin('opened', TType::I16, 12);
      $xfer += $output->writeI16($this->opened);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->dateTime !== null) {
      $xfer += $output->writeFieldBegin('dateTime', TType::STRING, 13);
      $xfer += $output->writeString($this->dateTime);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->remindFlg !== null) {
      $xfer += $output->writeFieldBegin('remindFlg', TType::I16, 14);
      $xfer += $output->writeI16($this->remindFlg);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->noticeUserId !== null) {
      $xfer += $output->writeFieldBegin('noticeUserId', TType::I64, 15);
      $xfer += $output->writeI64($this->noticeUserId);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
