<?php
/**
 *  @generated
 */
class Monipla_findByMail_args {
  static $_TSPEC;

  public $mailAddress = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'mailAddress',
          'type' => TType::STRUCT,
          'class' => 'Thrift_MailAddress',
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['mailAddress'])) {
        $this->mailAddress = $vals['mailAddress'];
      }
    }
  }

  public function getName() {
    return 'Monipla_findByMail_args';
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
            $this->mailAddress = new Thrift_MailAddress();
            $xfer += $this->mailAddress->read($input);
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
    $xfer += $output->writeStructBegin('Monipla_findByMail_args');
    if ($this->mailAddress !== null) {
      if (!is_object($this->mailAddress)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('mailAddress', TType::STRUCT, 1);
      $xfer += $this->mailAddress->write($output);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
