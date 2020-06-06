<?php /**
 *
 */
class Post{
  private $imgPath, $text, $noLikes , $userId , $date ;

function __construct($imgp , $t , $u , $d)
  {
    $imgPath = $imgp;
    $text = $t;
    $userId = $u;
    $date = $d;
  }

  private $html = " <div class=\"w3-card\">
                      <img src=\"$imgPath\">
                      <p>$userId <br> nolikes</p>
                      <p>$text</p>
                    </div> ";

  public function getHtml()
  {
    return $this->html;
  }
}
 ?>
