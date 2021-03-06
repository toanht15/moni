<?php
/**
 *  @generated
 */
class Monipla_sendNotification_args {
  static $_TSPEC;

  public $notification = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'notification',
          'type' => TType::STRUCT,
          'class' => 'Thrift_AddNotification',
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['notification'])) {
        $this->notification = $vals['notification'];
      }
    }
  }

  public function getName() {
    return 'Monipla_sendNotification_args';
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
            $this->notification = new Thrift_AddNotification();
            $xfer += $this->notification->read($input);
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
    $xfer += $output->writeStructBegin('Monipla_sendNotification_args');
    if ($this->notification !== null) {
      if (!is_object($this->notification)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('notification', TType::STRUCT, 1);
      $xfer += $this->notification->write($output);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
