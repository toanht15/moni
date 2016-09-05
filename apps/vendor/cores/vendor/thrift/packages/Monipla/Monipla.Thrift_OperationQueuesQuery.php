<?php
/**
 *  @generated
 */
class Thrift_OperationQueuesQuery {
  static $_TSPEC;

  public $operationType = null;
  public $pager = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'operationType',
          'type' => TType::I16,
          ),
        2 => array(
          'var' => 'pager',
          'type' => TType::STRUCT,
          'class' => 'Thrift_Pager',
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['operationType'])) {
        $this->operationType = $vals['operationType'];
      }
      if (isset($vals['pager'])) {
        $this->pager = $vals['pager'];
      }
    }
  }

  public function getName() {
    return 'Thrift_OperationQueuesQuery';
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
          if ($ftype == TType::I16) {
            $xfer += $input->readI16($this->operationType);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRUCT) {
            $this->pager = new Thrift_Pager();
            $xfer += $this->pager->read($input);
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
    $xfer += $output->writeStructBegin('Thrift_OperationQueuesQuery');
    if ($this->operationType !== null) {
      $xfer += $output->writeFieldBegin('operationType', TType::I16, 1);
      $xfer += $output->writeI16($this->operationType);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->pager !== null) {
      if (!is_object($this->pager)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('pager', TType::STRUCT, 2);
      $xfer += $this->pager->write($output);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
