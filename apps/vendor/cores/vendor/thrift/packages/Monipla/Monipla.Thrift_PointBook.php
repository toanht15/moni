<?php
/**
 *  @generated
 */
class Thrift_PointBook {
  static $_TSPEC;

  public $userID = null;
  public $dateTime = null;
  public $point = null;
  public $description = null;
  public $socialMediaAccountID = null;
  public $socialMediaType = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'userID',
          'type' => TType::STRING,
          ),
        2 => array(
          'var' => 'dateTime',
          'type' => TType::STRING,
          ),
        3 => array(
          'var' => 'point',
          'type' => TType::I32,
          ),
        4 => array(
          'var' => 'description',
          'type' => TType::STRING,
          ),
        5 => array(
          'var' => 'socialMediaAccountID',
          'type' => TType::STRING,
          ),
        6 => array(
          'var' => 'socialMediaType',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['userID'])) {
        $this->userID = $vals['userID'];
      }
      if (isset($vals['dateTime'])) {
        $this->dateTime = $vals['dateTime'];
      }
      if (isset($vals['point'])) {
        $this->point = $vals['point'];
      }
      if (isset($vals['description'])) {
        $this->description = $vals['description'];
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
    return 'Thrift_PointBook';
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
            $xfer += $input->readString($this->userID);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->dateTime);
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
            $xfer += $input->readString($this->description);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->socialMediaAccountID);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 6:
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
    $xfer += $output->writeStructBegin('Thrift_PointBook');
    if ($this->userID !== null) {
      $xfer += $output->writeFieldBegin('userID', TType::STRING, 1);
      $xfer += $output->writeString($this->userID);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->dateTime !== null) {
      $xfer += $output->writeFieldBegin('dateTime', TType::STRING, 2);
      $xfer += $output->writeString($this->dateTime);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->point !== null) {
      $xfer += $output->writeFieldBegin('point', TType::I32, 3);
      $xfer += $output->writeI32($this->point);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->description !== null) {
      $xfer += $output->writeFieldBegin('description', TType::STRING, 4);
      $xfer += $output->writeString($this->description);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->socialMediaAccountID !== null) {
      $xfer += $output->writeFieldBegin('socialMediaAccountID', TType::STRING, 5);
      $xfer += $output->writeString($this->socialMediaAccountID);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->socialMediaType !== null) {
      $xfer += $output->writeFieldBegin('socialMediaType', TType::STRING, 6);
      $xfer += $output->writeString($this->socialMediaType);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
