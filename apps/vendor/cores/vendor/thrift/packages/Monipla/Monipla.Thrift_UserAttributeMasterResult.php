<?php
/**
 *  @generated
 */
class Thrift_UserAttributeMasterResult {
  static $_TSPEC;

  public $result = null;
  public $userAttributeMasterList = null;
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
          'var' => 'userAttributeMasterList',
          'type' => TType::LST,
          'etype' => TType::STRUCT,
          'elem' => array(
            'type' => TType::STRUCT,
            'class' => 'Thrift_UserAttributeMaster',
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
      if (isset($vals['userAttributeMasterList'])) {
        $this->userAttributeMasterList = $vals['userAttributeMasterList'];
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
    return 'Thrift_UserAttributeMasterResult';
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
            $this->userAttributeMasterList = array();
            $_size190 = 0;
            $_etype193 = 0;
            $xfer += $input->readListBegin($_etype193, $_size190);
            for ($_i194 = 0; $_i194 < $_size190; ++$_i194)
            {
              $elem195 = null;
              $elem195 = new Thrift_UserAttributeMaster();
              $xfer += $elem195->read($input);
              $this->userAttributeMasterList []= $elem195;
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
    $xfer += $output->writeStructBegin('Thrift_UserAttributeMasterResult');
    if ($this->result !== null) {
      if (!is_object($this->result)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('result', TType::STRUCT, 1);
      $xfer += $this->result->write($output);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->userAttributeMasterList !== null) {
      if (!is_array($this->userAttributeMasterList)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('userAttributeMasterList', TType::LST, 2);
      {
        $output->writeListBegin(TType::STRUCT, count($this->userAttributeMasterList));
        {
          foreach ($this->userAttributeMasterList as $iter196)
          {
            $xfer += $iter196->write($output);
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
