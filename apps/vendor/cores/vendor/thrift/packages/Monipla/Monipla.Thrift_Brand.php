<?php
/**
 *  @generated
 */
class Thrift_Brand {
  static $_TSPEC;

  public $id = null;
  public $enterpriseId = null;
  public $name = null;
  public $agentId = null;
  public $salesForceId = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'id',
          'type' => TType::I64,
          ),
        2 => array(
          'var' => 'enterpriseId',
          'type' => TType::I64,
          ),
        3 => array(
          'var' => 'name',
          'type' => TType::STRING,
          ),
        4 => array(
          'var' => 'agentId',
          'type' => TType::I64,
          ),
        5 => array(
          'var' => 'salesForceId',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['id'])) {
        $this->id = $vals['id'];
      }
      if (isset($vals['enterpriseId'])) {
        $this->enterpriseId = $vals['enterpriseId'];
      }
      if (isset($vals['name'])) {
        $this->name = $vals['name'];
      }
      if (isset($vals['agentId'])) {
        $this->agentId = $vals['agentId'];
      }
      if (isset($vals['salesForceId'])) {
        $this->salesForceId = $vals['salesForceId'];
      }
    }
  }

  public function getName() {
    return 'Thrift_Brand';
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
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->enterpriseId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->name);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->agentId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->salesForceId);
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
    $xfer += $output->writeStructBegin('Thrift_Brand');
    if ($this->id !== null) {
      $xfer += $output->writeFieldBegin('id', TType::I64, 1);
      $xfer += $output->writeI64($this->id);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->enterpriseId !== null) {
      $xfer += $output->writeFieldBegin('enterpriseId', TType::I64, 2);
      $xfer += $output->writeI64($this->enterpriseId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->name !== null) {
      $xfer += $output->writeFieldBegin('name', TType::STRING, 3);
      $xfer += $output->writeString($this->name);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->agentId !== null) {
      $xfer += $output->writeFieldBegin('agentId', TType::I64, 4);
      $xfer += $output->writeI64($this->agentId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->salesForceId !== null) {
      $xfer += $output->writeFieldBegin('salesForceId', TType::STRING, 5);
      $xfer += $output->writeString($this->salesForceId);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
