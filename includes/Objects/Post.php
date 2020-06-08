<?php /**
 *
 */
class Post{
  private $imgPath, $text, $noLikes , $userId , $date ;
  private $html;

function __construct($imgp , $t , $u , $d)
  {
    $imgPath = $imgp;
    $text = $t;
    $userId = $u;
    $date = $d;
  }


  public function getHtml()
  {
    return " <div class=\"w3-card\">
                        <img src=\"$imgPath\">
                        <p>$userId <br> nolikes</p>
                        <p>$text</p>
                      </div> ";
  }
}
 ?>
