<?php
/**
 *  @generated
 */
class Thrift_NotificationQuery {
  static $_TSPEC;

  public $socialAccount = null;
  public $userId = null;
  public $clientId = null;
  public $id = null;
  public $opened = -1;
  public $noticeUserId = null;
  public $pager = null;

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
          'var' => 'clientId',
          'type' => TType::STRING,
          ),
        4 => array(
          'var' => 'id',
          'type' => TType::I64,
          ),
        5 => array(
          'var' => 'opened',
          'type' => TType::I16,
          ),
        6 => array(
          'var' => 'noticeUserId',
          'type' => TType::I64,
          ),
        7 => array(
          'var' => 'pager',
          'type' => TType::STRUCT,
          'class' => 'Thrift_Pager',
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
      if (isset($vals['clientId'])) {
        $this->clientId = $vals['clientId'];
      }
      if (isset($vals['id'])) {
        $this->id = $vals['id'];
      }
      if (isset($vals['opened'])) {
        $this->opened = $vals['opened'];
      }
      if (isset($vals['noticeUserId'])) {
        $this->noticeUserId = $vals['noticeUserId'];
      }
      if (isset($vals['pager'])) {
        $this->pager = $vals['pager'];
      }
    }
  }

  public function getName() {
    return 'Thrift_NotificationQuery';
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
            $xfer += $input->readString($this->clientId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->id);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::I16) {
            $xfer += $input->readI16($this->opened);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 6:
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->noticeUserId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 7:
          if ($ftype == TType::STRUCT) {
            $this->pager = new Thrift_Pager();
            $xfer += $this->pager->read($input);
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
    $xfer += $output->writeStructBegin('Thrift_NotificationQuery');
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
    if ($this->clientId !== null) {
      $xfer += $output->writeFieldBegin('clientId', TType::STRING, 3);
      $xfer += $output->writeString($this->clientId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->id !== null) {
      $xfer += $output->writeFieldBegin('id', TType::I64, 4);
      $xfer += $output->writeI64($this->id);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->opened !== null) {
      $xfer += $output->writeFieldBegin('opened', TType::I16, 5);
      $xfer += $output->writeI16($this->opened);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->noticeUserId !== null) {
      $xfer += $output->writeFieldBegin('noticeUserId', TType::I64, 6);
      $xfer += $output->writeI64($this->noticeUserId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->pager !== null) {
      if (!is_object($this->pager)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('pager', TType::STRUCT, 7);
      $xfer += $this->pager->write($output);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
