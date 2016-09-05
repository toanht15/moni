<?php
/**
 *  @generated
 */
class Monipla_findBySocialAccount_args {
  static $_TSPEC;

  public $account = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'account',
          'type' => TType::STRUCT,
          'class' => 'Thrift_SocialAccount',
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['account'])) {
        $this->account = $vals['account'];
      }
    }
  }

  public function getName() {
    return 'Monipla_findBySocialAccount_args';
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
            $this->account = new Thrift_SocialAccount();
            $xfer += $this->account->read($input);
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
    $xfer += $output->writeStructBegin('Monipla_findBySocialAccount_args');
    if ($this->account !== null) {
      if (!is_object($this->account)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('account', TType::STRUCT, 1);
      $xfer += $this->account->write($output);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
