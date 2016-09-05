<?php
/**
 *  @generated
 */
class Thrift_WithdrawQuery {
  static $_TSPEC;

  public $userId = null;
  public $reasons = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'userId',
          'type' => TType::I64,
          ),
        2 => array(
          'var' => 'reasons',
          'type' => TType::MAP,
          'ktype' => TType::STRING,
          'vtype' => TType::STRING,
          'key' => array(
            'type' => TType::STRING,
          ),
          'val' => array(
            'type' => TType::STRING,
            ),
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['userId'])) {
        $this->userId = $vals['userId'];
      }
      if (isset($vals['reasons'])) {
        $this->reasons = $vals['reasons'];
      }
    }
  }

  public function getName() {
    return 'Thrift_WithdrawQuery';
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
            $xfer += $input->readI64($this->userId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::MAP) {
            $this->reasons = array();
            $_size204 = 0;
            $_ktype205 = 0;
            $_vtype206 = 0;
            $xfer += $input->readMapBegin($_ktype205, $_vtype206, $_size204);
            for ($_i208 = 0; $_i208 < $_size204; ++$_i208)
            {
              $key209 = '';
              $val210 = '';
              $xfer += $input->readString($key209);
              $xfer += $input->readString($val210);
              $this->reasons[$key209] = $val210;
            }
            $xfer += $input->readMapEnd();
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
    $xfer += $output->writeStructBegin('Thrift_WithdrawQuery');
    if ($this->userId !== null) {
      $xfer += $output->writeFieldBegin('userId', TType::I64, 1);
      $xfer += $output->writeI64($this->userId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->reasons !== null) {
      if (!is_array($this->reasons)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('reasons', TType::MAP, 2);
      {
        $output->writeMapBegin(TType::STRING, TType::STRING, count($this->reasons));
        {
          foreach ($this->reasons as $kiter211 => $viter212)
          {
            $xfer += $output->writeString($kiter211);
            $xfer += $output->writeString($viter212);
          }
        }
        $output->writeMapEnd();
      }
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
