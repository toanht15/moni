<?php
/**
 *  @generated
 */
class Thrift_PointSummaryResult {
  static $_TSPEC;

  public $result = null;
  public $applyingPoints = null;
  public $plusPoints = null;
  public $exchangedPoints = null;
  public $expiredPoints = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'result',
          'type' => TType::STRUCT,
          'class' => 'Thrift_APIResult',
          ),
        2 => array(
          'var' => 'applyingPoints',
          'type' => TType::I64,
          ),
        3 => array(
          'var' => 'plusPoints',
          'type' => TType::I64,
          ),
        4 => array(
          'var' => 'exchangedPoints',
          'type' => TType::I64,
          ),
        5 => array(
          'var' => 'expiredPoints',
          'type' => TType::I64,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['result'])) {
        $this->result = $vals['result'];
      }
      if (isset($vals['applyingPoints'])) {
        $this->applyingPoints = $vals['applyingPoints'];
      }
      if (isset($vals['plusPoints'])) {
        $this->plusPoints = $vals['plusPoints'];
      }
      if (isset($vals['exchangedPoints'])) {
        $this->exchangedPoints = $vals['exchangedPoints'];
      }
      if (isset($vals['expiredPoints'])) {
        $this->expiredPoints = $vals['expiredPoints'];
      }
    }
  }

  public function getName() {
    return 'Thrift_PointSummaryResult';
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
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->applyingPoints);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->plusPoints);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->exchangedPoints);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->expiredPoints);
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
    $xfer += $output->writeStructBegin('Thrift_PointSummaryResult');
    if ($this->result !== null) {
      if (!is_object($this->result)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('result', TType::STRUCT, 1);
      $xfer += $this->result->write($output);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->applyingPoints !== null) {
      $xfer += $output->writeFieldBegin('applyingPoints', TType::I64, 2);
      $xfer += $output->writeI64($this->applyingPoints);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->plusPoints !== null) {
      $xfer += $output->writeFieldBegin('plusPoints', TType::I64, 3);
      $xfer += $output->writeI64($this->plusPoints);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->exchangedPoints !== null) {
      $xfer += $output->writeFieldBegin('exchangedPoints', TType::I64, 4);
      $xfer += $output->writeI64($this->exchangedPoints);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->expiredPoints !== null) {
      $xfer += $output->writeFieldBegin('expiredPoints', TType::I64, 5);
      $xfer += $output->writeI64($this->expiredPoints);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
