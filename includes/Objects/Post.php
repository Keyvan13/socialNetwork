<?php
class Post{
  private $imgPath, $text, $noLikes , $uName , $date ;
  private $html , $id , $pHtml;

  function __construct($imgp , $t , $u , $d , $id)
  {
    $this->imgPath = $imgp;
    $this->text = $t;
    $this->uName= $u;
    $this->date = $d;
    $this->id = $id;

    $this->html = <<<_END
      <div class="w3-card w3-white w3-round w3-auto" style="width:70%"><br>
        <div class="w3-center">
          <img  src="$this->imgPath" style="width:50%">
        </div>
        <hr class="w3-clear">
        <div>
          <p><a href="/profile?uName=$this->uName">$this->uName</a></p>
          <p>$this->text</p>
        </div>
      </div>
    _END;
  }

  public function setId($id)
  {
    $this->id = $id;
  }

  public function getId()
  {
    return $this->id;
  }

  public function getD()
  {
    return $this->date;
  }

  public function getHtml()
  {
    return $this->html;
  }

  public function getPhtml()
  {
    $this->pHtml = <<<_END
      <div class="w3-card-4 w3-margin-top w3-container">

        <div class="w3-center">
          <img  src="$this->imgPath" style="width:50%">
        </div>
        <div>
          <p>$this->uName</p>
          <p>$this->text</p>
          <a href="/edit?postid=$this->id">Edit</a>
        </div>
      </div>
    _END;
    return $this->pHtml;
  }

  static function deletePost($id)
  {
    $conn = connectDatabase();
    $query = <<<_END
      DELETE FROM
      posts
      WHERE
      id = $id
    _END;
    $result = $conn->query($query);
    if (!$result) {
      die;
    }
    dumpInfo($result);
    $result->close();
    $conn->close();
  }

  public function getText()
  {
    return $this->text;
  }
}
 ?>
