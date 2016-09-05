<?php
/**
 *  @generated
 */
class Thrift_Address {
  static $_TSPEC;

  public $socialAccount = null;
  public $userId = null;
  public $mailAddress = null;
  public $firstName = null;
  public $lastName = null;
  public $firstNameKana = null;
  public $lastNameKana = null;
  public $zipCode1 = null;
  public $zipCode2 = null;
  public $prefId = null;
  public $prefName = null;
  public $address1 = null;
  public $address2 = null;
  public $address3 = null;
  public $telNo1 = null;
  public $telNo2 = null;
  public $telNo3 = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'socialAccount',
          'type' => TType::STRUCT,
          'class' => 'Thrift_SocialAccount',
          ),
        2 => array(
          'var' => 'userId',
          'type' => TType::I64,
          ),
        3 => array(
          'var' => 'mailAddress',
          'type' => TType::STRING,
          ),
        4 => array(
          'var' => 'firstName',
          'type' => TType::STRING,
          ),
        5 => array(
          'var' => 'lastName',
          'type' => TType::STRING,
          ),
        6 => array(
          'var' => 'firstNameKana',
          'type' => TType::STRING,
          ),
        7 => array(
          'var' => 'lastNameKana',
          'type' => TType::STRING,
          ),
        8 => array(
          'var' => 'zipCode1',
          'type' => TType::STRING,
          ),
        9 => array(
          'var' => 'zipCode2',
          'type' => TType::STRING,
          ),
        10 => array(
          'var' => 'prefId',
          'type' => TType::I64,
          ),
        11 => array(
          'var' => 'prefName',
          'type' => TType::STRING,
          ),
        12 => array(
          'var' => 'address1',
          'type' => TType::STRING,
          ),
        13 => array(
          'var' => 'address2',
          'type' => TType::STRING,
          ),
        14 => array(
          'var' => 'address3',
          'type' => TType::STRING,
          ),
        15 => array(
          'var' => 'telNo1',
          'type' => TType::STRING,
          ),
        16 => array(
          'var' => 'telNo2',
          'type' => TType::STRING,
          ),
        17 => array(
          'var' => 'telNo3',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['socialAccount'])) {
        $this->socialAccount = $vals['socialAccount'];
      }
      if (isset($vals['userId'])) {
        $this->userId = $vals['userId'];
      }
      if (isset($vals['mailAddress'])) {
        $this->mailAddress = $vals['mailAddress'];
      }
      if (isset($vals['firstName'])) {
        $this->firstName = $vals['firstName'];
      }
      if (isset($vals['lastName'])) {
        $this->lastName = $vals['lastName'];
      }
      if (isset($vals['firstNameKana'])) {
        $this->firstNameKana = $vals['firstNameKana'];
      }
      if (isset($vals['lastNameKana'])) {
        $this->lastNameKana = $vals['lastNameKana'];
      }
      if (isset($vals['zipCode1'])) {
        $this->zipCode1 = $vals['zipCode1'];
      }
      if (isset($vals['zipCode2'])) {
        $this->zipCode2 = $vals['zipCode2'];
      }
      if (isset($vals['prefId'])) {
        $this->prefId = $vals['prefId'];
      }
      if (isset($vals['prefName'])) {
        $this->prefName = $vals['prefName'];
      }
      if (isset($vals['address1'])) {
        $this->address1 = $vals['address1'];
      }
      if (isset($vals['address2'])) {
        $this->address2 = $vals['address2'];
      }
      if (isset($vals['address3'])) {
        $this->address3 = $vals['address3'];
      }
      if (isset($vals['telNo1'])) {
        $this->telNo1 = $vals['telNo1'];
      }
      if (isset($vals['telNo2'])) {
        $this->telNo2 = $vals['telNo2'];
      }
      if (isset($vals['telNo3'])) {
        $this->telNo3 = $vals['telNo3'];
      }
    }
  }

  public function getName() {
    return 'Thrift_Address';
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
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->userId);
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
            $xfer += $input->readString($this->firstName);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->lastName);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 6:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->firstNameKana);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 7:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->lastNameKana);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 8:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->zipCode1);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 9:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->zipCode2);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 10:
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->prefId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 11:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->prefName);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 12:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->address1);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 13:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->address2);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 14:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->address3);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 15:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->telNo1);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 16:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->telNo2);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 17:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->telNo3);
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
    $xfer += $output->writeStructBegin('Thrift_Address');
    if ($this->socialAccount !== null) {
      if (!is_object($this->socialAccount)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('socialAccount', TType::STRUCT, 1);
      $xfer += $this->socialAccount->write($output);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->userId !== null) {
      $xfer += $output->writeFieldBegin('userId', TType::I64, 2);
      $xfer += $output->writeI64($this->userId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->mailAddress !== null) {
      $xfer += $output->writeFieldBegin('mailAddress', TType::STRING, 3);
      $xfer += $output->writeString($this->mailAddress);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->firstName !== null) {
      $xfer += $output->writeFieldBegin('firstName', TType::STRING, 4);
      $xfer += $output->writeString($this->firstName);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->lastName !== null) {
      $xfer += $output->writeFieldBegin('lastName', TType::STRING, 5);
      $xfer += $output->writeString($this->lastName);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->firstNameKana !== null) {
      $xfer += $output->writeFieldBegin('firstNameKana', TType::STRING, 6);
      $xfer += $output->writeString($this->firstNameKana);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->lastNameKana !== null) {
      $xfer += $output->writeFieldBegin('lastNameKana', TType::STRING, 7);
      $xfer += $output->writeString($this->lastNameKana);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->zipCode1 !== null) {
      $xfer += $output->writeFieldBegin('zipCode1', TType::STRING, 8);
      $xfer += $output->writeString($this->zipCode1);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->zipCode2 !== null) {
      $xfer += $output->writeFieldBegin('zipCode2', TType::STRING, 9);
      $xfer += $output->writeString($this->zipCode2);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->prefId !== null) {
      $xfer += $output->writeFieldBegin('prefId', TType::I64, 10);
      $xfer += $output->writeI64($this->prefId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->prefName !== null) {
      $xfer += $output->writeFieldBegin('prefName', TType::STRING, 11);
      $xfer += $output->writeString($this->prefName);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->address1 !== null) {
      $xfer += $output->writeFieldBegin('address1', TType::STRING, 12);
      $xfer += $output->writeString($this->address1);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->address2 !== null) {
      $xfer += $output->writeFieldBegin('address2', TType::STRING, 13);
      $xfer += $output->writeString($this->address2);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->address3 !== null) {
      $xfer += $output->writeFieldBegin('address3', TType::STRING, 14);
      $xfer += $output->writeString($this->address3);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->telNo1 !== null) {
      $xfer += $output->writeFieldBegin('telNo1', TType::STRING, 15);
      $xfer += $output->writeString($this->telNo1);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->telNo2 !== null) {
      $xfer += $output->writeFieldBegin('telNo2', TType::STRING, 16);
      $xfer += $output->writeString($this->telNo2);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->telNo3 !== null) {
      $xfer += $output->writeFieldBegin('telNo3', TType::STRING, 17);
      $xfer += $output->writeString($this->telNo3);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
