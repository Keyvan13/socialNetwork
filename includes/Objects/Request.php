<?php

/**
 *
 */
class Request
{
  private $sender , $receiver ,$status , $date , $id;
  function __construct($s , $r , $st , $id)
  {
    $this->sender = $s;
    $this->receiver = $r;
    $this->status = $st;
    $this->id = $id;
  }
  public function getSender()
  {
    return $this->sender;
  }

  public function getReceiver()
  {
    return $this->receiver;
  }

  public function getStatus()
  {
    return $this->status;
  }
  public function getId()
  {
    return $this->id;
  }

  public function setStatus($st)
  {
    if ($st == "accept") {
      $this->status = $st;
    } elseif ($st == "reject") {
      $this->status = $st;
    } elseif ($st == "pending") {
      $this->status = $st;
    }else {
      return false;
    }
  }
}


class FriendshipRow
{
  public $sender , $receiver;
  function __construct($s , $r)
  {
    $this->sender = $s;
    $this->receiver = $r;
  }
}

 ?>
