<?php
/**
 *  @generated
 */
class Thrift_CouponCodeQuery {
  static $_TSPEC;

  public $socialAccount = null;
  public $userId = null;
  public $code = null;
  public $fromDateTime = null;
  public $toDateTime = null;
  public $fromLimitDateTime = null;
  public $toLimitDateTime = null;
  public $assigned = -1;
  public $offset = null;
  public $limit = null;
  public $orders = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'socialAccount',
          'type' => TType::STRUCT,
          'class' => 'Thrift_SocialAccount',
          ),
        2 => array(
          'var' => 'userId',
          'type' => TType::I64,
          ),
        3 => array(
          'var' => 'code',
          'type' => TType::STRING,
          ),
        4 => array(
          'var' => 'fromDateTime',
          'type' => TType::STRING,
          ),
        5 => array(
          'var' => 'toDateTime',
          'type' => TType::STRING,
          ),
        6 => array(
          'var' => 'fromLimitDateTime',
          'type' => TType::STRING,
          ),
        7 => array(
          'var' => 'toLimitDateTime',
          'type' => TType::STRING,
          ),
        8 => array(
          'var' => 'assigned',
          'type' => TType::I16,
          ),
        9 => array(
          'var' => 'offset',
          'type' => TType::I32,
          ),
        10 => array(
          'var' => 'limit',
          'type' => TType::I32,
          ),
        11 => array(
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
      if (isset($vals['socialAccount'])) {
        $this->socialAccount = $vals['socialAccount'];
      }
      if (isset($vals['userId'])) {
        $this->userId = $vals['userId'];
      }
      if (isset($vals['code'])) {
        $this->code = $vals['code'];
      }
      if (isset($vals['fromDateTime'])) {
        $this->fromDateTime = $vals['fromDateTime'];
      }
      if (isset($vals['toDateTime'])) {
        $this->toDateTime = $vals['toDateTime'];
      }
      if (isset($vals['fromLimitDateTime'])) {
        $this->fromLimitDateTime = $vals['fromLimitDateTime'];
      }
      if (isset($vals['toLimitDateTime'])) {
        $this->toLimitDateTime = $vals['toLimitDateTime'];
      }
      if (isset($vals['assigned'])) {
        $this->assigned = $vals['assigned'];
      }
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
    return 'Thrift_CouponCodeQuery';
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
            $this->socialAccount = new Thrift_SocialAccount();
            $xfer += $this->socialAccount->read($input);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->userId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->code);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->fromDateTime);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->toDateTime);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 6:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->fromLimitDateTime);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 7:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->toLimitDateTime);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 8:
          if ($ftype == TType::I16) {
            $xfer += $input->readI16($this->assigned);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 9:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->offset);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 10:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->limit);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 11:
          if ($ftype == TType::LST) {
            $this->orders = array();
            $_size23 = 0;
            $_etype26 = 0;
            $xfer += $input->readListBegin($_etype26, $_size23);
            for ($_i27 = 0; $_i27 < $_size23; ++$_i27)
            {
              $elem28 = null;
              $elem28 = array();
              $_size29 = 0;
              $_ktype30 = 0;
              $_vtype31 = 0;
              $xfer += $input->readMapBegin($_ktype30, $_vtype31, $_size29);
              for ($_i33 = 0; $_i33 < $_size29; ++$_i33)
              {
                $key34 = '';
                $val35 = false;
                $xfer += $input->readString($key34);
                $xfer += $input->readBool($val35);
                $elem28[$key34] = $val35;
              }
              $xfer += $input->readMapEnd();
              $this->orders []= $elem28;
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
    $xfer += $output->writeStructBegin('Thrift_CouponCodeQuery');
    if ($this->socialAccount !== null) {
      if (!is_object($this->socialAccount)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('socialAccount', TType::STRUCT, 1);
      $xfer += $this->socialAccount->write($output);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->userId !== null) {
      $xfer += $output->writeFieldBegin('userId', TType::I64, 2);
      $xfer += $output->writeI64($this->userId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->code !== null) {
      $xfer += $output->writeFieldBegin('code', TType::STRING, 3);
      $xfer += $output->writeString($this->code);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->fromDateTime !== null) {
      $xfer += $output->writeFieldBegin('fromDateTime', TType::STRING, 4);
      $xfer += $output->writeString($this->fromDateTime);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->toDateTime !== null) {
      $xfer += $output->writeFieldBegin('toDateTime', TType::STRING, 5);
      $xfer += $output->writeString($this->toDateTime);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->fromLimitDateTime !== null) {
      $xfer += $output->writeFieldBegin('fromLimitDateTime', TType::STRING, 6);
      $xfer += $output->writeString($this->fromLimitDateTime);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->toLimitDateTime !== null) {
      $xfer += $output->writeFieldBegin('toLimitDateTime', TType::STRING, 7);
      $xfer += $output->writeString($this->toLimitDateTime);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->assigned !== null) {
      $xfer += $output->writeFieldBegin('assigned', TType::I16, 8);
      $xfer += $output->writeI16($this->assigned);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->offset !== null) {
      $xfer += $output->writeFieldBegin('offset', TType::I32, 9);
      $xfer += $output->writeI32($this->offset);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->limit !== null) {
      $xfer += $output->writeFieldBegin('limit', TType::I32, 10);
      $xfer += $output->writeI32($this->limit);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->orders !== null) {
      if (!is_array($this->orders)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('orders', TType::LST, 11);
      {
        $output->writeListBegin(TType::MAP, count($this->orders));
        {
          foreach ($this->orders as $iter36)
          {
            {
              $output->writeMapBegin(TType::STRING, TType::BOOL, count($iter36));
              {
                foreach ($iter36 as $kiter37 => $viter38)
                {
                  $xfer += $output->writeString($kiter37);
                  $xfer += $output->writeBool($viter38);
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
