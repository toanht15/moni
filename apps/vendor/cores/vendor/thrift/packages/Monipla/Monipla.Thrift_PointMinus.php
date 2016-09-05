<?php
/**
 *  @generated
 */
class Thrift_PointMinus {
  static $_TSPEC;

  public $userID = null;
  public $point = null;
  public $minusDateTime = null;
  public $provisioned = null;
  public $executed = null;
  public $executionType = null;
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
          'var' => 'point',
          'type' => TType::I32,
          ),
        3 => array(
          'var' => 'minusDateTime',
          'type' => TType::STRING,
          ),
        4 => array(
          'var' => 'provisioned',
          'type' => TType::I32,
          ),
        5 => array(
          'var' => 'executed',
          'type' => TType::I32,
          ),
        6 => array(
          'var' => 'executionType',
          'type' => TType::I32,
          ),
        7 => array(
          'var' => 'description',
          'type' => TType::STRING,
          ),
        8 => array(
          'var' => 'socialMediaAccountID',
          'type' => TType::STRING,
          ),
        9 => array(
          'var' => 'socialMediaType',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['userID'])) {
        $this->userID = $vals['userID'];
      }
      if (isset($vals['point'])) {
        $this->point = $vals['point'];
      }
      if (isset($vals['minusDateTime'])) {
        $this->minusDateTime = $vals['minusDateTime'];
      }
      if (isset($vals['provisioned'])) {
        $this->provisioned = $vals['provisioned'];
      }
      if (isset($vals['executed'])) {
        $this->executed = $vals['executed'];
      }
      if (isset($vals['executionType'])) {
        $this->executionType = $vals['executionType'];
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
    return 'Thrift_PointMinus';
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
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->point);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->minusDateTime);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->provisioned);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->executed);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 6:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->executionType);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 7:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->description);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 8:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->socialMediaAccountID);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 9:
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
    $xfer += $output->writeStructBegin('Thrift_PointMinus');
    if ($this->userID !== null) {
      $xfer += $output->writeFieldBegin('userID', TType::STRING, 1);
      $xfer += $output->writeString($this->userID);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->point !== null) {
      $xfer += $output->writeFieldBegin('point', TType::I32, 2);
      $xfer += $output->writeI32($this->point);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->minusDateTime !== null) {
      $xfer += $output->writeFieldBegin('minusDateTime', TType::STRING, 3);
      $xfer += $output->writeString($this->minusDateTime);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->provisioned !== null) {
      $xfer += $output->writeFieldBegin('provisioned', TType::I32, 4);
      $xfer += $output->writeI32($this->provisioned);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->executed !== null) {
      $xfer += $output->writeFieldBegin('executed', TType::I32, 5);
      $xfer += $output->writeI32($this->executed);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->executionType !== null) {
      $xfer += $output->writeFieldBegin('executionType', TType::I32, 6);
      $xfer += $output->writeI32($this->executionType);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->description !== null) {
      $xfer += $output->writeFieldBegin('description', TType::STRING, 7);
      $xfer += $output->writeString($this->description);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->socialMediaAccountID !== null) {
      $xfer += $output->writeFieldBegin('socialMediaAccountID', TType::STRING, 8);
      $xfer += $output->writeString($this->socialMediaAccountID);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->socialMediaType !== null) {
      $xfer += $output->writeFieldBegin('socialMediaType', TType::STRING, 9);
      $xfer += $output->writeString($this->socialMediaType);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
