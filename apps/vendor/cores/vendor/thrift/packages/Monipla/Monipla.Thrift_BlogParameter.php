<?php
/**
 *  @generated
 */
class Thrift_BlogParameter {
  static $_TSPEC;

  public $id = null;
  public $title = null;
  public $userId = null;
  public $url = null;
  public $rssUrl = null;
  public $pv = null;

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
          'var' => 'userId',
          'type' => TType::I64,
          ),
        4 => array(
          'var' => 'url',
          'type' => TType::STRING,
          ),
        5 => array(
          'var' => 'rssUrl',
          'type' => TType::STRING,
          ),
        6 => array(
          'var' => 'pv',
          'type' => TType::I64,
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
      if (isset($vals['userId'])) {
        $this->userId = $vals['userId'];
      }
      if (isset($vals['url'])) {
        $this->url = $vals['url'];
      }
      if (isset($vals['rssUrl'])) {
        $this->rssUrl = $vals['rssUrl'];
      }
      if (isset($vals['pv'])) {
        $this->pv = $vals['pv'];
      }
    }
  }

  public function getName() {
    return 'Thrift_BlogParameter';
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
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->userId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->url);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->rssUrl);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 6:
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->pv);
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
    $xfer += $output->writeStructBegin('Thrift_BlogParameter');
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
    if ($this->userId !== null) {
      $xfer += $output->writeFieldBegin('userId', TType::I64, 3);
      $xfer += $output->writeI64($this->userId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->url !== null) {
      $xfer += $output->writeFieldBegin('url', TType::STRING, 4);
      $xfer += $output->writeString($this->url);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->rssUrl !== null) {
      $xfer += $output->writeFieldBegin('rssUrl', TType::STRING, 5);
      $xfer += $output->writeString($this->rssUrl);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->pv !== null) {
      $xfer += $output->writeFieldBegin('pv', TType::I64, 6);
      $xfer += $output->writeI64($this->pv);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
