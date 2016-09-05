<?php
/**
 *  @generated
 */
class Thrift_SocialAccount {
  static $_TSPEC;

  public $socialMediaType = null;
  public $socialMediaAccountID = null;
  public $mailAddress = null;
  public $password = null;
  public $profileImageUrl = null;
  public $profilePageUrl = null;
  public $name = null;
  public $validated = -1;
  public $confirmedMailAddress = 0;
  public $friendCount = 0;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'socialMediaType',
          'type' => TType::STRING,
          ),
        2 => array(
          'var' => 'socialMediaAccountID',
          'type' => TType::STRING,
          ),
        3 => array(
          'var' => 'mailAddress',
          'type' => TType::STRING,
          ),
        4 => array(
          'var' => 'password',
          'type' => TType::STRING,
          ),
        5 => array(
          'var' => 'profileImageUrl',
          'type' => TType::STRING,
          ),
        6 => array(
          'var' => 'profilePageUrl',
          'type' => TType::STRING,
          ),
        7 => array(
          'var' => 'name',
          'type' => TType::STRING,
          ),
        8 => array(
          'var' => 'validated',
          'type' => TType::I16,
          ),
        9 => array(
          'var' => 'confirmedMailAddress',
          'type' => TType::I16,
          ),
        10 => array(
          'var' => 'friendCount',
          'type' => TType::I32,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['socialMediaType'])) {
        $this->socialMediaType = $vals['socialMediaType'];
      }
      if (isset($vals['socialMediaAccountID'])) {
        $this->socialMediaAccountID = $vals['socialMediaAccountID'];
      }
      if (isset($vals['mailAddress'])) {
        $this->mailAddress = $vals['mailAddress'];
      }
      if (isset($vals['password'])) {
        $this->password = $vals['password'];
      }
      if (isset($vals['profileImageUrl'])) {
        $this->profileImageUrl = $vals['profileImageUrl'];
      }
      if (isset($vals['profilePageUrl'])) {
        $this->profilePageUrl = $vals['profilePageUrl'];
      }
      if (isset($vals['name'])) {
        $this->name = $vals['name'];
      }
      if (isset($vals['validated'])) {
        $this->validated = $vals['validated'];
      }
      if (isset($vals['confirmedMailAddress'])) {
        $this->confirmedMailAddress = $vals['confirmedMailAddress'];
      }
      if (isset($vals['friendCount'])) {
        $this->friendCount = $vals['friendCount'];
      }
    }
  }

  public function getName() {
    return 'Thrift_SocialAccount';
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
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->socialMediaType);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->socialMediaAccountID);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->mailAddress);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->password);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->profileImageUrl);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 6:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->profilePageUrl);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 7:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->name);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 8:
          if ($ftype == TType::I16) {
            $xfer += $input->readI16($this->validated);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 9:
          if ($ftype == TType::I16) {
            $xfer += $input->readI16($this->confirmedMailAddress);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 10:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->friendCount);
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
    $xfer += $output->writeStructBegin('Thrift_SocialAccount');
    if ($this->socialMediaType !== null) {
      $xfer += $output->writeFieldBegin('socialMediaType', TType::STRING, 1);
      $xfer += $output->writeString($this->socialMediaType);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->socialMediaAccountID !== null) {
      $xfer += $output->writeFieldBegin('socialMediaAccountID', TType::STRING, 2);
      $xfer += $output->writeString($this->socialMediaAccountID);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->mailAddress !== null) {
      $xfer += $output->writeFieldBegin('mailAddress', TType::STRING, 3);
      $xfer += $output->writeString($this->mailAddress);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->password !== null) {
      $xfer += $output->writeFieldBegin('password', TType::STRING, 4);
      $xfer += $output->writeString($this->password);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->profileImageUrl !== null) {
      $xfer += $output->writeFieldBegin('profileImageUrl', TType::STRING, 5);
      $xfer += $output->writeString($this->profileImageUrl);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->profilePageUrl !== null) {
      $xfer += $output->writeFieldBegin('profilePageUrl', TType::STRING, 6);
      $xfer += $output->writeString($this->profilePageUrl);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->name !== null) {
      $xfer += $output->writeFieldBegin('name', TType::STRING, 7);
      $xfer += $output->writeString($this->name);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->validated !== null) {
      $xfer += $output->writeFieldBegin('validated', TType::I16, 8);
      $xfer += $output->writeI16($this->validated);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->confirmedMailAddress !== null) {
      $xfer += $output->writeFieldBegin('confirmedMailAddress', TType::I16, 9);
      $xfer += $output->writeI16($this->confirmedMailAddress);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->friendCount !== null) {
      $xfer += $output->writeFieldBegin('friendCount', TType::I32, 10);
      $xfer += $output->writeI32($this->friendCount);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
