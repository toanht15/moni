<?php
/**
 *  @generated
 */
class Thrift_CouponCodeSummaryResult {
  static $_TSPEC;

  public $result = null;
  public $totalAllCount = null;
  public $assignedCount = null;
  public $notAssignedCount = null;
  public $utilizationRate = null;
  public $lessOneYearAndHalfCount = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'result',
          'type' => TType::STRUCT,
          'class' => 'Thrift_APIResult',
          ),
        2 => array(
          'var' => 'totalAllCount',
          'type' => TType::I32,
          ),
        3 => array(
          'var' => 'assignedCount',
          'type' => TType::I32,
          ),
        4 => array(
          'var' => 'notAssignedCount',
          'type' => TType::I32,
          ),
        5 => array(
          'var' => 'utilizationRate',
          'type' => TType::DOUBLE,
          ),
        6 => array(
          'var' => 'lessOneYearAndHalfCount',
          'type' => TType::I32,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['result'])) {
        $this->result = $vals['result'];
      }
      if (isset($vals['totalAllCount'])) {
        $this->totalAllCount = $vals['totalAllCount'];
      }
      if (isset($vals['assignedCount'])) {
        $this->assignedCount = $vals['assignedCount'];
      }
      if (isset($vals['notAssignedCount'])) {
        $this->notAssignedCount = $vals['notAssignedCount'];
      }
      if (isset($vals['utilizationRate'])) {
        $this->utilizationRate = $vals['utilizationRate'];
      }
      if (isset($vals['lessOneYearAndHalfCount'])) {
        $this->lessOneYearAndHalfCount = $vals['lessOneYearAndHalfCount'];
      }
    }
  }

  public function getName() {
    return 'Thrift_CouponCodeSummaryResult';
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
            $this->result = new Thrift_APIResult();
            $xfer += $this->result->read($input);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->totalAllCount);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->assignedCount);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->notAssignedCount);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::DOUBLE) {
            $xfer += $input->readDouble($this->utilizationRate);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 6:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->lessOneYearAndHalfCount);
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
    $xfer += $output->writeStructBegin('Thrift_CouponCodeSummaryResult');
    if ($this->result !== null) {
      if (!is_object($this->result)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('result', TType::STRUCT, 1);
      $xfer += $this->result->write($output);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->totalAllCount !== null) {
      $xfer += $output->writeFieldBegin('totalAllCount', TType::I32, 2);
      $xfer += $output->writeI32($this->totalAllCount);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->assignedCount !== null) {
      $xfer += $output->writeFieldBegin('assignedCount', TType::I32, 3);
      $xfer += $output->writeI32($this->assignedCount);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->notAssignedCount !== null) {
      $xfer += $output->writeFieldBegin('notAssignedCount', TType::I32, 4);
      $xfer += $output->writeI32($this->notAssignedCount);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->utilizationRate !== null) {
      $xfer += $output->writeFieldBegin('utilizationRate', TType::DOUBLE, 5);
      $xfer += $output->writeDouble($this->utilizationRate);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->lessOneYearAndHalfCount !== null) {
      $xfer += $output->writeFieldBegin('lessOneYearAndHalfCount', TType::I32, 6);
      $xfer += $output->writeI32($this->lessOneYearAndHalfCount);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
