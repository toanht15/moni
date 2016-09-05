<?php
/**
 *  @generated
 */
class Thrift_Pager {
  static $_TSPEC;

  public $offset = null;
  public $limit = null;
  public $orders = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'offset',
          'type' => TType::I32,
          ),
        2 => array(
          'var' => 'limit',
          'type' => TType::I32,
          ),
        3 => array(
          'var' => 'orders',
          'type' => TType::LST,
          'etype' => TType::MAP,
          'elem' => array(
            'type' => TType::MAP,
            'ktype' => TType::STRING,
            'vtype' => TType::BOOL,
            'key' => array(
              'type' => TType::STRING,
            ),
            'val' => array(
              'type' => TType::BOOL,
              ),
            ),
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['offset'])) {
        $this->offset = $vals['offset'];
      }
      if (isset($vals['limit'])) {
        $this->limit = $vals['limit'];
      }
      if (isset($vals['orders'])) {
        $this->orders = $vals['orders'];
      }
    }
  }

  public function getName() {
    return 'Thrift_Pager';
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
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->offset);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->limit);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::LST) {
            $this->orders = array();
            $_size7 = 0;
            $_etype10 = 0;
            $xfer += $input->readListBegin($_etype10, $_size7);
            for ($_i11 = 0; $_i11 < $_size7; ++$_i11)
            {
              $elem12 = null;
              $elem12 = array();
              $_size13 = 0;
              $_ktype14 = 0;
              $_vtype15 = 0;
              $xfer += $input->readMapBegin($_ktype14, $_vtype15, $_size13);
              for ($_i17 = 0; $_i17 < $_size13; ++$_i17)
              {
                $key18 = '';
                $val19 = false;
                $xfer += $input->readString($key18);
                $xfer += $input->readBool($val19);
                $elem12[$key18] = $val19;
              }
              $xfer += $input->readMapEnd();
              $this->orders []= $elem12;
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
    $xfer += $output->writeStructBegin('Thrift_Pager');
    if ($this->offset !== null) {
      $xfer += $output->writeFieldBegin('offset', TType::I32, 1);
      $xfer += $output->writeI32($this->offset);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->limit !== null) {
      $xfer += $output->writeFieldBegin('limit', TType::I32, 2);
      $xfer += $output->writeI32($this->limit);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->orders !== null) {
      if (!is_array($this->orders)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('orders', TType::LST, 3);
      {
        $output->writeListBegin(TType::MAP, count($this->orders));
        {
          foreach ($this->orders as $iter20)
          {
            {
              $output->writeMapBegin(TType::STRING, TType::BOOL, count($iter20));
              {
                foreach ($iter20 as $kiter21 => $viter22)
                {
                  $xfer += $output->writeString($kiter21);
                  $xfer += $output->writeBool($viter22);
                }
              }
              $output->writeMapEnd();
            }
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
