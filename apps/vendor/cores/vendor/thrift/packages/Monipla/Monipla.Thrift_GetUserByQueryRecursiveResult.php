<?php
/**
 *  @generated
 */
class Thrift_GetUserByQueryRecursiveResult {
  static $_TSPEC;

  public $result = null;
  public $ids = null;
  public $user = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'result',
          'type' => TType::STRUCT,
          'class' => 'Thrift_APIResult',
          ),
        2 => array(
          'var' => 'ids',
          'type' => TType::LST,
          'etype' => TType::I64,
          'elem' => array(
            'type' => TType::I64,
            ),
          ),
        3 => array(
          'var' => 'user',
          'type' => TType::STRUCT,
          'class' => 'Thrift_UserData',
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['result'])) {
        $this->result = $vals['result'];
      }
      if (isset($vals['ids'])) {
        $this->ids = $vals['ids'];
      }
      if (isset($vals['user'])) {
        $this->user = $vals['user'];
      }
    }
  }

  public function getName() {
    return 'Thrift_GetUserByQueryRecursiveResult';
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
            $this->ids = array();
            $_size213 = 0;
            $_etype216 = 0;
            $xfer += $input->readListBegin($_etype216, $_size213);
            for ($_i217 = 0; $_i217 < $_size213; ++$_i217)
            {
              $elem218 = null;
              $xfer += $input->readI64($elem218);
              $this->ids []= $elem218;
            }
            $xfer += $input->readListEnd();
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::STRUCT) {
            $this->user = new Thrift_UserData();
            $xfer += $this->user->read($input);
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
    $xfer += $output->writeStructBegin('Thrift_GetUserByQueryRecursiveResult');
    if ($this->result !== null) {
      if (!is_object($this->result)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('result', TType::STRUCT, 1);
      $xfer += $this->result->write($output);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->ids !== null) {
      if (!is_array($this->ids)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('ids', TType::LST, 2);
      {
        $output->writeListBegin(TType::I64, count($this->ids));
        {
          foreach ($this->ids as $iter219)
          {
            $xfer += $output->writeI64($iter219);
          }
        }
        $output->writeListEnd();
      }
      $xfer += $output->writeFieldEnd();
    }
    if ($this->user !== null) {
      if (!is_object($this->user)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('user', TType::STRUCT, 3);
      $xfer += $this->user->write($output);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
