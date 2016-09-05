<?php
/**
 *  @generated
 */
class Thrift_CouponCode {
  static $_TSPEC;

  public $code = null;
  public $executionType = null;
  public $point = null;
  public $limitDateTime = null;
  public $assigned = null;
  public $assignedDateTime = null;
  public $socialMediaAccountID = null;
  public $socialMediaType = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'code',
          'type' => TType::STRING,
          ),
        2 => array(
          'var' => 'executionType',
          'type' => TType::I32,
          ),
        3 => array(
          'var' => 'point',
          'type' => TType::I32,
          ),
        4 => array(
          'var' => 'limitDateTime',
          'type' => TType::STRING,
          ),
        5 => array(
          'var' => 'assigned',
          'type' => TType::I32,
          ),
        6 => array(
          'var' => 'assignedDateTime',
          'type' => TType::STRING,
          ),
        7 => array(
          'var' => 'socialMediaAccountID',
          'type' => TType::STRING,
          ),
        8 => array(
          'var' => 'socialMediaType',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['code'])) {
        $this->code = $vals['code'];
      }
      if (isset($vals['executionType'])) {
        $this->executionType = $vals['executionType'];
      }
      if (isset($vals['point'])) {
        $this->point = $vals['point'];
      }
      if (isset($vals['limitDateTime'])) {
        $this->limitDateTime = $vals['limitDateTime'];
      }
      if (isset($vals['assigned'])) {
        $this->assigned = $vals['assigned'];
      }
      if (isset($vals['assignedDateTime'])) {
        $this->assignedDateTime = $vals['assignedDateTime'];
      }
      if (isset($vals['socialMediaAccountID'])) {
        $this->socialMediaAccountID = $vals['socialMediaAccountID'];
      }
      if (isset($vals['socialMediaType'])) {
        $this->socialMediaType = $vals['socialMediaType'];
      }
    }
  }

  public function getName() {
    return 'Thrift_CouponCode';
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
            $xfer += $input->readString($this->code);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->executionType);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->point);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->limitDateTime);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->assigned);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 6:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->assignedDateTime);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 7:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->socialMediaAccountID);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 8:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->socialMediaType);
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
    $xfer += $output->writeStructBegin('Thrift_CouponCode');
    if ($this->code !== null) {
      $xfer += $output->writeFieldBegin('code', TType::STRING, 1);
      $xfer += $output->writeString($this->code);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->executionType !== null) {
      $xfer += $output->writeFieldBegin('executionType', TType::I32, 2);
      $xfer += $output->writeI32($this->executionType);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->point !== null) {
      $xfer += $output->writeFieldBegin('point', TType::I32, 3);
      $xfer += $output->writeI32($this->point);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->limitDateTime !== null) {
      $xfer += $output->writeFieldBegin('limitDateTime', TType::STRING, 4);
      $xfer += $output->writeString($this->limitDateTime);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->assigned !== null) {
      $xfer += $output->writeFieldBegin('assigned', TType::I32, 5);
      $xfer += $output->writeI32($this->assigned);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->assignedDateTime !== null) {
      $xfer += $output->writeFieldBegin('assignedDateTime', TType::STRING, 6);
      $xfer += $output->writeString($this->assignedDateTime);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->socialMediaAccountID !== null) {
      $xfer += $output->writeFieldBegin('socialMediaAccountID', TType::STRING, 7);
      $xfer += $output->writeString($this->socialMediaAccountID);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->socialMediaType !== null) {
      $xfer += $output->writeFieldBegin('socialMediaType', TType::STRING, 8);
      $xfer += $output->writeString($this->socialMediaType);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
