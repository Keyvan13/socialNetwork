<?php /**
 *
 */
class Post{
  private $imgPath, $text, $noLikes , $userId , $date ;
  private $html = "post";




  function __construct($imgp , $t , $u , $d)
  {
    $imgPath = $imgp;
    $text = $t;
    $userId = $u;
    $date = $d;
  }

  public function getHtml()
  {
    return $this->html;
  }
}
 ?>
