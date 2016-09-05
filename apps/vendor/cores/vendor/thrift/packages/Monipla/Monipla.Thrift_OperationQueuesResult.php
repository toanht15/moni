<?php
/**
 *  @generated
 */
class Thrift_OperationQueuesResult {
  static $_TSPEC;

  public $result = null;
  public $operationQueues = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'result',
          'type' => TType::STRUCT,
          'class' => 'Thrift_APIResult',
          ),
        2 => array(
          'var' => 'operationQueues',
          'type' => TType::LST,
          'etype' => TType::STRUCT,
          'elem' => array(
            'type' => TType::STRUCT,
            'class' => 'Thrift_OperationQueue',
            ),
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['result'])) {
        $this->result = $vals['result'];
      }
      if (isset($vals['operationQueues'])) {
        $this->operationQueues = $vals['operationQueues'];
      }
    }
  }

  public function getName() {
    return 'Thrift_OperationQueuesResult';
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
            $this->operationQueues = array();
            $_size153 = 0;
            $_etype156 = 0;
            $xfer += $input->readListBegin($_etype156, $_size153);
            for ($_i157 = 0; $_i157 < $_size153; ++$_i157)
            {
              $elem158 = null;
              $elem158 = new Thrift_OperationQueue();
              $xfer += $elem158->read($input);
              $this->operationQueues []= $elem158;
            }
            $xfer += $input->readListEnd();
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
    $xfer += $output->writeStructBegin('Thrift_OperationQueuesResult');
    if ($this->result !== null) {
      if (!is_object($this->result)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('result', TType::STRUCT, 1);
      $xfer += $this->result->write($output);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->operationQueues !== null) {
      if (!is_array($this->operationQueues)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('operationQueues', TType::LST, 2);
      {
        $output->writeListBegin(TType::STRUCT, count($this->operationQueues));
        {
          foreach ($this->operationQueues as $iter159)
          {
            $xfer += $iter159->write($output);
          }
        }
        $output->writeListEnd();
      }
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
