<?php /**
 *
 */
class Post{
  private $imgPath, $text, $noLikes , $uName , $date ;
  private $html;

function __construct($imgp , $t , $u , $d)
  {
    $this->imgPath = $imgp;
    $this->text = $t;
    $this->uName= $u;
    $this->date = $d;
  }

  public function getD()
  {
    return $this->date;
  }


  public function getHtml()
  {
    return " <div class=\"w3-card-4 w3-margin-top w3-container\">
                        <div class=\"w3-center\">
                        <img  src=\"$this->imgPath\" style=\"width:50%\">
                        </div>
                        <div>
                        <p>$this->uName</p>
                        <p>$this->text</p></div>
                      </div> ";
  }
}
 ?>
