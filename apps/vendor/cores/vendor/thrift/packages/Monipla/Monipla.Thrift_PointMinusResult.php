<?php
/**
 *  @generated
 */
class Thrift_PointMinusResult {
  static $_TSPEC;

  public $result = null;
  public $pointMinuses = null;
  public $totalCount = null;
  public $startRowNum = null;
  public $endRowNum = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'result',
          'type' => TType::STRUCT,
          'class' => 'Thrift_APIResult',
          ),
        2 => array(
          'var' => 'pointMinuses',
          'type' => TType::LST,
          'etype' => TType::STRUCT,
          'elem' => array(
            'type' => TType::STRUCT,
            'class' => 'Thrift_PointMinus',
            ),
          ),
        3 => array(
          'var' => 'totalCount',
          'type' => TType::I32,
          ),
        4 => array(
          'var' => 'startRowNum',
          'type' => TType::I32,
          ),
        5 => array(
          'var' => 'endRowNum',
          'type' => TType::I32,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['result'])) {
        $this->result = $vals['result'];
      }
      if (isset($vals['pointMinuses'])) {
        $this->pointMinuses = $vals['pointMinuses'];
      }
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
    return 'Thrift_PointMinusResult';
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
          if ($ftype == TType::LST) {
            $this->pointMinuses = array();
            $_size90 = 0;
            $_etype93 = 0;
            $xfer += $input->readListBegin($_etype93, $_size90);
            for ($_i94 = 0; $_i94 < $_size90; ++$_i94)
            {
              $elem95 = null;
              $elem95 = new Thrift_PointMinus();
              $xfer += $elem95->read($input);
              $this->pointMinuses []= $elem95;
            }
            $xfer += $input->readListEnd();
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->totalCount);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->startRowNum);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
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
    $xfer += $output->writeStructBegin('Thrift_PointMinusResult');
    if ($this->result !== null) {
      if (!is_object($this->result)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('result', TType::STRUCT, 1);
      $xfer += $this->result->write($output);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->pointMinuses !== null) {
      if (!is_array($this->pointMinuses)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('pointMinuses', TType::LST, 2);
      {
        $output->writeListBegin(TType::STRUCT, count($this->pointMinuses));
        {
          foreach ($this->pointMinuses as $iter96)
          {
            $xfer += $iter96->write($output);
          }
        }
        $output->writeListEnd();
      }
      $xfer += $output->writeFieldEnd();
    }
    if ($this->totalCount !== null) {
      $xfer += $output->writeFieldBegin('totalCount', TType::I32, 3);
      $xfer += $output->writeI32($this->totalCount);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->startRowNum !== null) {
      $xfer += $output->writeFieldBegin('startRowNum', TType::I32, 4);
      $xfer += $output->writeI32($this->startRowNum);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->endRowNum !== null) {
      $xfer += $output->writeFieldBegin('endRowNum', TType::I32, 5);
      $xfer += $output->writeI32($this->endRowNum);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
