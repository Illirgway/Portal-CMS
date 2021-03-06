<div style="margin-bottom:10px;">
<a href="javascript:history.go(-1)">News</a> » <a href="<?php print Uri::current(); ?>"><?php print $title; ?></a>
</div>

<article class="news">
  <hgroup class="clearfix">
    <h1 class="left"><?php print $title; ?></h1>
    <h5 class="right"><?php print $time; ?></h5>
  </hgroup>
  <div class="pic clearfix">
    <?php if(isset($picture_1)): ?>
      <a class="news_full_img" href="<?php print $picture_1_big ?>"><img class="left" src="<?php print $picture_1 ?>"></a>
    <?php endif; ?>
    <?php if(isset($picture_2)): ?>
      <a class="news_full_img" href="<?php print $picture_2_big ?>"><img class="left" src="<?php print $picture_2 ?>"></a>
    <?php endif; ?>
    <?php if(isset($picture_3)): ?>
      <a class="news_full_img" href="<?php print $picture_3_big ?>"><img class="left" src="<?php print $picture_3 ?>"></a>
    <?php endif; ?>
  </div>
  <p>
    <?php print $full_text; ?>
  </p>
</article>
<script>
$(function() {
  $(".news_full_img").colorbox({rel:'news_full_img'});
});
</script>