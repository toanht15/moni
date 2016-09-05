<?php
/**
 *  @generated
 */
class Thrift_NotificationMarkReadQuery {
  static $_TSPEC;

  public $ids = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'ids',
          'type' => TType::LST,
          'etype' => TType::I64,
          'elem' => array(
            'type' => TType::I64,
            ),
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['ids'])) {
        $this->ids = $vals['ids'];
      }
    }
  }

  public function getName() {
    return 'Thrift_NotificationMarkReadQuery';
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
          if ($ftype == TType::LST) {
            $this->ids = array();
            $_size118 = 0;
            $_etype121 = 0;
            $xfer += $input->readListBegin($_etype121, $_size118);
            for ($_i122 = 0; $_i122 < $_size118; ++$_i122)
            {
              $elem123 = null;
              $xfer += $input->readI64($elem123);
              $this->ids []= $elem123;
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
    $xfer += $output->writeStructBegin('Thrift_NotificationMarkReadQuery');
    if ($this->ids !== null) {
      if (!is_array($this->ids)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('ids', TType::LST, 1);
      {
        $output->writeListBegin(TType::I64, count($this->ids));
        {
          foreach ($this->ids as $iter124)
          {
            $xfer += $output->writeI64($iter124);
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
