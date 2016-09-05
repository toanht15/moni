<?php
/**
 *  @generated
 */
class Thrift_CreateRemindQueuesParameter {
  static $_TSPEC;

  public $userId = null;
  public $title = null;
  public $description = null;
  public $notificationIds = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'userId',
          'type' => TType::I64,
          ),
        2 => array(
          'var' => 'title',
          'type' => TType::STRING,
          ),
        3 => array(
          'var' => 'description',
          'type' => TType::STRING,
          ),
        4 => array(
          'var' => 'notificationIds',
          'type' => TType::LST,
          'etype' => TType::I64,
          'elem' => array(
            'type' => TType::I64,
            ),
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['userId'])) {
        $this->userId = $vals['userId'];
      }
      if (isset($vals['title'])) {
        $this->title = $vals['title'];
      }
      if (isset($vals['description'])) {
        $this->description = $vals['description'];
      }
      if (isset($vals['notificationIds'])) {
        $this->notificationIds = $vals['notificationIds'];
      }
    }
  }

  public function getName() {
    return 'Thrift_CreateRemindQueuesParameter';
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
            $xfer += $input->readI64($this->userId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->title);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->description);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::LST) {
            $this->notificationIds = array();
            $_size146 = 0;
            $_etype149 = 0;
            $xfer += $input->readListBegin($_etype149, $_size146);
            for ($_i150 = 0; $_i150 < $_size146; ++$_i150)
            {
              $elem151 = null;
              $xfer += $input->readI64($elem151);
              $this->notificationIds []= $elem151;
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
    $xfer += $output->writeStructBegin('Thrift_CreateRemindQueuesParameter');
    if ($this->userId !== null) {
      $xfer += $output->writeFieldBegin('userId', TType::I64, 1);
      $xfer += $output->writeI64($this->userId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->title !== null) {
      $xfer += $output->writeFieldBegin('title', TType::STRING, 2);
      $xfer += $output->writeString($this->title);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->description !== null) {
      $xfer += $output->writeFieldBegin('description', TType::STRING, 3);
      $xfer += $output->writeString($this->description);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->notificationIds !== null) {
      if (!is_array($this->notificationIds)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('notificationIds', TType::LST, 4);
      {
        $output->writeListBegin(TType::I64, count($this->notificationIds));
        {
          foreach ($this->notificationIds as $iter152)
          {
            $xfer += $output->writeI64($iter152);
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
