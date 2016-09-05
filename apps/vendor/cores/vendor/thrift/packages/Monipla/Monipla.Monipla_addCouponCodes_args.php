<?php
/**
 *  @generated
 */
class Monipla_addCouponCodes_args {
  static $_TSPEC;

  public $couponCodes = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'couponCodes',
          'type' => TType::LST,
          'etype' => TType::STRUCT,
          'elem' => array(
            'type' => TType::STRUCT,
            'class' => 'Thrift_AddCouponCode',
            ),
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['couponCodes'])) {
        $this->couponCodes = $vals['couponCodes'];
      }
    }
  }

  public function getName() {
    return 'Monipla_addCouponCodes_args';
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
            $this->couponCodes = array();
            $_size248 = 0;
            $_etype251 = 0;
            $xfer += $input->readListBegin($_etype251, $_size248);
            for ($_i252 = 0; $_i252 < $_size248; ++$_i252)
            {
              $elem253 = null;
              $elem253 = new Thrift_AddCouponCode();
              $xfer += $elem253->read($input);
              $this->couponCodes []= $elem253;
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
    $xfer += $output->writeStructBegin('Monipla_addCouponCodes_args');
    if ($this->couponCodes !== null) {
      if (!is_array($this->couponCodes)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('couponCodes', TType::LST, 1);
      {
        $output->writeListBegin(TType::STRUCT, count($this->couponCodes));
        {
          foreach ($this->couponCodes as $iter254)
          {
            $xfer += $iter254->write($output);
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
