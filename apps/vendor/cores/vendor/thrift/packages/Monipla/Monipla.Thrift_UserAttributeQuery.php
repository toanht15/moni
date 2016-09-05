<?php
/**
 *  @generated
 */
class Thrift_UserAttributeQuery {
  static $_TSPEC;

  public $socialAccount = null;
  public $masterId = null;
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
          'var' => 'masterId',
          'type' => TType::I64,
          ),
        3 => array(
          'var' => 'offset',
          'type' => TType::I32,
          ),
        4 => array(
          'var' => 'limit',
          'type' => TType::I32,
          ),
        5 => array(
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
      if (isset($vals['masterId'])) {
        $this->masterId = $vals['masterId'];
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
    return 'Thrift_UserAttributeQuery';
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
            $xfer += $input->readI64($this->masterId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->offset);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->limit);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::LST) {
            $this->orders = array();
            $_size167 = 0;
            $_etype170 = 0;
            $xfer += $input->readListBegin($_etype170, $_size167);
            for ($_i171 = 0; $_i171 < $_size167; ++$_i171)
            {
              $elem172 = null;
              $elem172 = array();
              $_size173 = 0;
              $_ktype174 = 0;
              $_vtype175 = 0;
              $xfer += $input->readMapBegin($_ktype174, $_vtype175, $_size173);
              for ($_i177 = 0; $_i177 < $_size173; ++$_i177)
              {
                $key178 = '';
                $val179 = false;
                $xfer += $input->readString($key178);
                $xfer += $input->readBool($val179);
                $elem172[$key178] = $val179;
              }
              $xfer += $input->readMapEnd();
              $this->orders []= $elem172;
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
    $xfer += $output->writeStructBegin('Thrift_UserAttributeQuery');
    if ($this->socialAccount !== null) {
      if (!is_object($this->socialAccount)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('socialAccount', TType::STRUCT, 1);
      $xfer += $this->socialAccount->write($output);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->masterId !== null) {
      $xfer += $output->writeFieldBegin('masterId', TType::I64, 2);
      $xfer += $output->writeI64($this->masterId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->offset !== null) {
      $xfer += $output->writeFieldBegin('offset', TType::I32, 3);
      $xfer += $output->writeI32($this->offset);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->limit !== null) {
      $xfer += $output->writeFieldBegin('limit', TType::I32, 4);
      $xfer += $output->writeI32($this->limit);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->orders !== null) {
      if (!is_array($this->orders)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('orders', TType::LST, 5);
      {
        $output->writeListBegin(TType::MAP, count($this->orders));
        {
          foreach ($this->orders as $iter180)
          {
            {
              $output->writeMapBegin(TType::STRING, TType::BOOL, count($iter180));
              {
                foreach ($iter180 as $kiter181 => $viter182)
                {
                  $xfer += $output->writeString($kiter181);
                  $xfer += $output->writeBool($viter182);
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
