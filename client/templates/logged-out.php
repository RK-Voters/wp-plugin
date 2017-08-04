<?php
  global $rkvoters_model;

?>

<div class="loggedOut">
  <h2 style="font-size: 30px">Log In</h2>
  <div  class="logged_out_img">
    <img src="<?php echo $rkvoters_model -> current_campaign -> img; ?>" />
  </div>
  <?php
    wp_login_form(
      array(
          "redirect" =>  site_url( '' )
      )
    );
  ?>
  <a href="<?php echo wp_lostpassword_url( get_permalink() ); ?>" title="Lost Password">Lost Password</a>
</div>
