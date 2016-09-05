<?php
/**
 *  @generated
 */
class Thrift_News {
  static $_TSPEC;

  public $id = null;
  public $title = null;
  public $body = null;
  public $dateTime = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'id',
          'type' => TType::I64,
          ),
        2 => array(
          'var' => 'title',
          'type' => TType::STRING,
          ),
        3 => array(
          'var' => 'body',
          'type' => TType::STRING,
          ),
        4 => array(
          'var' => 'dateTime',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['id'])) {
        $this->id = $vals['id'];
      }
      if (isset($vals['title'])) {
        $this->title = $vals['title'];
      }
      if (isset($vals['body'])) {
        $this->body = $vals['body'];
      }
      if (isset($vals['dateTime'])) {
        $this->dateTime = $vals['dateTime'];
      }
    }
  }

  public function getName() {
    return 'Thrift_News';
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
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->title);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->body);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->dateTime);
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
    $xfer += $output->writeStructBegin('Thrift_News');
    if ($this->id !== null) {
      $xfer += $output->writeFieldBegin('id', TType::I64, 1);
      $xfer += $output->writeI64($this->id);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->title !== null) {
      $xfer += $output->writeFieldBegin('title', TType::STRING, 2);
      $xfer += $output->writeString($this->title);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->body !== null) {
      $xfer += $output->writeFieldBegin('body', TType::STRING, 3);
      $xfer += $output->writeString($this->body);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->dateTime !== null) {
      $xfer += $output->writeFieldBegin('dateTime', TType::STRING, 4);
      $xfer += $output->writeString($this->dateTime);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
