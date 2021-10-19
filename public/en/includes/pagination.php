<?php
class pagination{

public $current_page;
public $per_page;
public $total_number;

function __construct($page=1,$per_page=10,$total_number=0){
	$this->current_page = (int)$page;
	$this->per_page = (int)$per_page;
	$this->total_number = (int)$total_number;

}
public function total_pages(){
	return ceil($this->total_number/$this->per_page);
}
 public function offset() {
    // Assuming 20 items per page:
    // page 1 has an offset of 0    (1-1) * 20
    // page 2 has an offset of 20   (2-1) * 20
    //   in other words, page 2 starts with item 21
    return ($this->current_page - 1) * $this->per_page;
  }
  public function page($number){
  if ($number < 0 && $this->current_page + $number >0) {
    return $this->current_page + $number;
  }elseif($number > 0 && $this->current_page + $number <= ceil($this->total_number/$this->per_page)){
    return $this->current_page + $number;
  }else{
    return false;
  }
  
}
public function previous_page(){
	return $this->current_page - 1;
}
public function next_page(){
	return $this->current_page + 1;
}

public function has_previous_page(){
	return $this->previous_page()>=1 ? true:false;
}

public function has_next_page(){
	return $this->next_page()<=$this->total_pages() ? true:false;
}
public static function template($currentPage,$currentPageNumber,$profiles_per_page,$total,$additional=null){
  ?>
  <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center pagination-sm mb-4">        
          <?php
             $page = new pagination($currentPageNumber,$profiles_per_page,$total);
             if ($page->has_previous_page()) {
                echo
                  "<li class='page-item'>
                        <a class='page-link' href='$currentPage?pn={$page->previous_page()}$additional'>Previous</a>
                     </li>
                  ";
             }
             for ($page_number=-3; $page_number <= 3; $page_number++) { 
                if ($page_number != 0) {
                   if ($page->page($page_number)) {
                      echo "<li class='page-item'>
                          <a href='$currentPage?pn={$page->page($page_number)}$additional' class='page-link'>{$page->page($page_number)}
                          </a>
                        </li>
                        ";
                   }
                }elseif($page->has_next_page() || $page->has_previous_page()){
                   echo "<li class='page-item active'><a class='page-link'> $currentPageNumber </a></li>";
                }
             }
             
             if ($page->has_next_page()) {
                echo
                "<li class='page-item'>
                      <a class='page-link' href='$currentPage?pn={$page->next_page()}$additional'>Next</a>
                   </li>
                ";
             }
          ?>
          
       </ul>
    </nav>
  <?php
}
}
?>