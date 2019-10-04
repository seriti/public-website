<div class="www-body">
  
  <div class="row">
    <div class="col-md-12">
       <?php echo $messages; ?>     
    </div>
  </div> 
 
  <div class="row">
    <?php
    if($image!='') {
      echo '<div class="col-sm-6 col-md-4 col-lg-3"><br/>'.$image.'</div>
            <div class="col-sm-6 col-md-8 col-lg-9"><h1>'.$title.'</h1>'.$text.'</div>';
      
    } else {
      echo '<div class="col-md-12"><h1>'.$title.'</h1>'.$text.'</span></div>';
    }    
    ?>
  </div>  
  
  <div class="row">
    <div class="col-md-12">
      <?php echo $gallery; ?>     
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-12">
      <?php echo $download; ?>     
    </div>
  </div>
</div>