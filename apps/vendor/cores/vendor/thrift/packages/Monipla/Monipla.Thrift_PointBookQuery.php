<?php
/**
 *  @generated
 */
class Thrift_PointBookQuery {
  static $_TSPEC;

  public $socialAccount = null;
  public $userId = null;
  public $fromDateTime = null;
  public $toDateTime = null;
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
          'var' => 'fromDateTime',
          'type' => TType::STRING,
          ),
        4 => array(
          'var' => 'toDateTime',
          'type' => TType::STRING,
          ),
        5 => array(
          'var' => 'offset',
          'type' => TType::I32,
          ),
        6 => array(
          'var' => 'limit',
          'type' => TType::I32,
          ),
        7 => array(
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
      if (isset($vals['fromDateTime'])) {
        $this->fromDateTime = $vals['fromDateTime'];
      }
      if (isset($vals['toDateTime'])) {
        $this->toDateTime = $vals['toDateTime'];
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
    return 'Thrift_PointBookQuery';
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
            $xfer += $input->readString($this->fromDateTime);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->toDateTime);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->offset);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 6:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->limit);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 7:
          if ($ftype == TType::LST) {
            $this->orders = array();
            $_size60 = 0;
            $_etype63 = 0;
            $xfer += $input->readListBegin($_etype63, $_size60);
            for ($_i64 = 0; $_i64 < $_size60; ++$_i64)
            {
              $elem65 = null;
              $elem65 = array();
              $_size66 = 0;
              $_ktype67 = 0;
              $_vtype68 = 0;
              $xfer += $input->readMapBegin($_ktype67, $_vtype68, $_size66);
              for ($_i70 = 0; $_i70 < $_size66; ++$_i70)
              {
                $key71 = '';
                $val72 = false;
                $xfer += $input->readString($key71);
                $xfer += $input->readBool($val72);
                $elem65[$key71] = $val72;
              }
              $xfer += $input->readMapEnd();
              $this->orders []= $elem65;
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
    $xfer += $output->writeStructBegin('Thrift_PointBookQuery');
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
    if ($this->fromDateTime !== null) {
      $xfer += $output->writeFieldBegin('fromDateTime', TType::STRING, 3);
      $xfer += $output->writeString($this->fromDateTime);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->toDateTime !== null) {
      $xfer += $output->writeFieldBegin('toDateTime', TType::STRING, 4);
      $xfer += $output->writeString($this->toDateTime);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->offset !== null) {
      $xfer += $output->writeFieldBegin('offset', TType::I32, 5);
      $xfer += $output->writeI32($this->offset);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->limit !== null) {
      $xfer += $output->writeFieldBegin('limit', TType::I32, 6);
      $xfer += $output->writeI32($this->limit);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->orders !== null) {
      if (!is_array($this->orders)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('orders', TType::LST, 7);
      {
        $output->writeListBegin(TType::MAP, count($this->orders));
        {
          foreach ($this->orders as $iter73)
          {
            {
              $output->writeMapBegin(TType::STRING, TType::BOOL, count($iter73));
              {
                foreach ($iter73 as $kiter74 => $viter75)
                {
                  $xfer += $output->writeString($kiter74);
                  $xfer += $output->writeBool($viter75);
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
