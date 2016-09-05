<?php
/**
 *  @generated
 */
class Thrift_OperationQueue {
  static $_TSPEC;

  public $id = null;
  public $userId = null;
  public $operationType = null;
  public $title = null;
  public $description = null;
  public $send = null;
  public $scheduleDateTime = null;
  public $sendDateTime = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'id',
          'type' => TType::I64,
          ),
        2 => array(
          'var' => 'userId',
          'type' => TType::I64,
          ),
        3 => array(
          'var' => 'operationType',
          'type' => TType::I16,
          ),
        4 => array(
          'var' => 'title',
          'type' => TType::STRING,
          ),
        5 => array(
          'var' => 'description',
          'type' => TType::STRING,
          ),
        6 => array(
          'var' => 'send',
          'type' => TType::I16,
          ),
        7 => array(
          'var' => 'scheduleDateTime',
          'type' => TType::STRING,
          ),
        8 => array(
          'var' => 'sendDateTime',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['id'])) {
        $this->id = $vals['id'];
      }
      if (isset($vals['userId'])) {
        $this->userId = $vals['userId'];
      }
      if (isset($vals['operationType'])) {
        $this->operationType = $vals['operationType'];
      }
      if (isset($vals['title'])) {
        $this->title = $vals['title'];
      }
      if (isset($vals['description'])) {
        $this->description = $vals['description'];
      }
      if (isset($vals['send'])) {
        $this->send = $vals['send'];
      }
      if (isset($vals['scheduleDateTime'])) {
        $this->scheduleDateTime = $vals['scheduleDateTime'];
      }
      if (isset($vals['sendDateTime'])) {
        $this->sendDateTime = $vals['sendDateTime'];
      }
    }
  }

  public function getName() {
    return 'Thrift_OperationQueue';
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
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->id);
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
          if ($ftype == TType::I16) {
            $xfer += $input->readI16($this->operationType);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->title);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->description);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 6:
          if ($ftype == TType::I16) {
            $xfer += $input->readI16($this->send);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 7:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->scheduleDateTime);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 8:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->sendDateTime);
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
    $xfer += $output->writeStructBegin('Thrift_OperationQueue');
    if ($this->id !== null) {
      $xfer += $output->writeFieldBegin('id', TType::I64, 1);
      $xfer += $output->writeI64($this->id);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->userId !== null) {
      $xfer += $output->writeFieldBegin('userId', TType::I64, 2);
      $xfer += $output->writeI64($this->userId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->operationType !== null) {
      $xfer += $output->writeFieldBegin('operationType', TType::I16, 3);
      $xfer += $output->writeI16($this->operationType);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->title !== null) {
      $xfer += $output->writeFieldBegin('title', TType::STRING, 4);
      $xfer += $output->writeString($this->title);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->description !== null) {
      $xfer += $output->writeFieldBegin('description', TType::STRING, 5);
      $xfer += $output->writeString($this->description);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->send !== null) {
      $xfer += $output->writeFieldBegin('send', TType::I16, 6);
      $xfer += $output->writeI16($this->send);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->scheduleDateTime !== null) {
      $xfer += $output->writeFieldBegin('scheduleDateTime', TType::STRING, 7);
      $xfer += $output->writeString($this->scheduleDateTime);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->sendDateTime !== null) {
      $xfer += $output->writeFieldBegin('sendDateTime', TType::STRING, 8);
      $xfer += $output->writeString($this->sendDateTime);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
