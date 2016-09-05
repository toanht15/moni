<?php
/**
 *  @generated
 */
class Thrift_Users {
  static $_TSPEC;

  public $result = null;
  public $user = null;
  public $duplicated = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'result',
          'type' => TType::STRUCT,
          'class' => 'Thrift_APIResult',
          ),
        2 => array(
          'var' => 'user',
          'type' => TType::LST,
          'etype' => TType::STRUCT,
          'elem' => array(
            'type' => TType::STRUCT,
            'class' => 'Thrift_UserData',
            ),
          ),
        3 => array(
          'var' => 'duplicated',
          'type' => TType::BOOL,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['result'])) {
        $this->result = $vals['result'];
      }
      if (isset($vals['user'])) {
        $this->user = $vals['user'];
      }
      if (isset($vals['duplicated'])) {
        $this->duplicated = $vals['duplicated'];
      }
    }
  }

  public function getName() {
    return 'Thrift_Users';
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
            $this->user = array();
            $_size227 = 0;
            $_etype230 = 0;
            $xfer += $input->readListBegin($_etype230, $_size227);
            for ($_i231 = 0; $_i231 < $_size227; ++$_i231)
            {
              $elem232 = null;
              $elem232 = new Thrift_UserData();
              $xfer += $elem232->read($input);
              $this->user []= $elem232;
            }
            $xfer += $input->readListEnd();
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::BOOL) {
            $xfer += $input->readBool($this->duplicated);
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
    $xfer += $output->writeStructBegin('Thrift_Users');
    if ($this->result !== null) {
      if (!is_object($this->result)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('result', TType::STRUCT, 1);
      $xfer += $this->result->write($output);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->user !== null) {
      if (!is_array($this->user)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('user', TType::LST, 2);
      {
        $output->writeListBegin(TType::STRUCT, count($this->user));
        {
          foreach ($this->user as $iter233)
          {
            $xfer += $iter233->write($output);
          }
        }
        $output->writeListEnd();
      }
      $xfer += $output->writeFieldEnd();
    }
    if ($this->duplicated !== null) {
      $xfer += $output->writeFieldBegin('duplicated', TType::BOOL, 3);
      $xfer += $output->writeBool($this->duplicated);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
