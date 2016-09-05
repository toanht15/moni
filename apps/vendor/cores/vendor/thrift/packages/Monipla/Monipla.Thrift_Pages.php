<?php
/**
 *  @generated
 */
class Thrift_Pages {
  static $_TSPEC;

  public $totalCount = null;
  public $startRowNum = null;
  public $endRowNum = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'totalCount',
          'type' => TType::I32,
          ),
        2 => array(
          'var' => 'startRowNum',
          'type' => TType::I32,
          ),
        3 => array(
          'var' => 'endRowNum',
          'type' => TType::I32,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['totalCount'])) {
        $this->totalCount = $vals['totalCount'];
      }
      if (isset($vals['startRowNum'])) {
        $this->startRowNum = $vals['startRowNum'];
      }
      if (isset($vals['endRowNum'])) {
        $this->endRowNum = $vals['endRowNum'];
      }
    }
  }

  public function getName() {
    return 'Thrift_Pages';
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
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->totalCount);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->startRowNum);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->endRowNum);
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
    $xfer += $output->writeStructBegin('Thrift_Pages');
    if ($this->totalCount !== null) {
      $xfer += $output->writeFieldBegin('totalCount', TType::I32, 1);
      $xfer += $output->writeI32($this->totalCount);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->startRowNum !== null) {
      $xfer += $output->writeFieldBegin('startRowNum', TType::I32, 2);
      $xfer += $output->writeI32($this->startRowNum);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->endRowNum !== null) {
      $xfer += $output->writeFieldBegin('endRowNum', TType::I32, 3);
      $xfer += $output->writeI32($this->endRowNum);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
