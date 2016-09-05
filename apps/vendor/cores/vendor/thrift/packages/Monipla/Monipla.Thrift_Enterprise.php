<?php
/**
 *  @generated
 */
class Thrift_Enterprise {
  static $_TSPEC;

  public $id = null;
  public $name = null;
  public $mailAddress = null;
  public $password = null;
  public $zip1 = null;
  public $zip2 = null;
  public $tel1 = null;
  public $tel2 = null;
  public $tel3 = null;
  public $address1 = null;
  public $address2 = null;
  public $address3 = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'id',
          'type' => TType::I64,
          ),
        2 => array(
          'var' => 'name',
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
          'var' => 'zip1',
          'type' => TType::STRING,
          ),
        6 => array(
          'var' => 'zip2',
          'type' => TType::STRING,
          ),
        7 => array(
          'var' => 'tel1',
          'type' => TType::STRING,
          ),
        8 => array(
          'var' => 'tel2',
          'type' => TType::STRING,
          ),
        9 => array(
          'var' => 'tel3',
          'type' => TType::STRING,
          ),
        10 => array(
          'var' => 'address1',
          'type' => TType::STRING,
          ),
        11 => array(
          'var' => 'address2',
          'type' => TType::STRING,
          ),
        12 => array(
          'var' => 'address3',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['id'])) {
        $this->id = $vals['id'];
      }
      if (isset($vals['name'])) {
        $this->name = $vals['name'];
      }
      if (isset($vals['mailAddress'])) {
        $this->mailAddress = $vals['mailAddress'];
      }
      if (isset($vals['password'])) {
        $this->password = $vals['password'];
      }
      if (isset($vals['zip1'])) {
        $this->zip1 = $vals['zip1'];
      }
      if (isset($vals['zip2'])) {
        $this->zip2 = $vals['zip2'];
      }
      if (isset($vals['tel1'])) {
        $this->tel1 = $vals['tel1'];
      }
      if (isset($vals['tel2'])) {
        $this->tel2 = $vals['tel2'];
      }
      if (isset($vals['tel3'])) {
        $this->tel3 = $vals['tel3'];
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
    }
  }

  public function getName() {
    return 'Thrift_Enterprise';
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
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->name);
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
            $xfer += $input->readString($this->zip1);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 6:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->zip2);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 7:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->tel1);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 8:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->tel2);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 9:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->tel3);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 10:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->address1);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 11:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->address2);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 12:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->address3);
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
    $xfer += $output->writeStructBegin('Thrift_Enterprise');
    if ($this->id !== null) {
      $xfer += $output->writeFieldBegin('id', TType::I64, 1);
      $xfer += $output->writeI64($this->id);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->name !== null) {
      $xfer += $output->writeFieldBegin('name', TType::STRING, 2);
      $xfer += $output->writeString($this->name);
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
    if ($this->zip1 !== null) {
      $xfer += $output->writeFieldBegin('zip1', TType::STRING, 5);
      $xfer += $output->writeString($this->zip1);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->zip2 !== null) {
      $xfer += $output->writeFieldBegin('zip2', TType::STRING, 6);
      $xfer += $output->writeString($this->zip2);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->tel1 !== null) {
      $xfer += $output->writeFieldBegin('tel1', TType::STRING, 7);
      $xfer += $output->writeString($this->tel1);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->tel2 !== null) {
      $xfer += $output->writeFieldBegin('tel2', TType::STRING, 8);
      $xfer += $output->writeString($this->tel2);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->tel3 !== null) {
      $xfer += $output->writeFieldBegin('tel3', TType::STRING, 9);
      $xfer += $output->writeString($this->tel3);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->address1 !== null) {
      $xfer += $output->writeFieldBegin('address1', TType::STRING, 10);
      $xfer += $output->writeString($this->address1);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->address2 !== null) {
      $xfer += $output->writeFieldBegin('address2', TType::STRING, 11);
      $xfer += $output->writeString($this->address2);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->address3 !== null) {
      $xfer += $output->writeFieldBegin('address3', TType::STRING, 12);
      $xfer += $output->writeString($this->address3);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
