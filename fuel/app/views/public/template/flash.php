<div id="<?php print $group; ?>"></div>
  <img id="<?php print $group; ?>_picture" src="<?php print $picture ?>" >
  <script>
   if($.flash.available)
   {
     $('#<?php print $group; ?>').flash(
      {
        swf: '<?php print $swfPath; ?>',
        flashvars: <?php print $params; ?>,
        wMode : '<?php print $wmode; ?>',
        height : <?php print $height; ?>,
        width : <?php print $width; ?>
      }
    );
    $('#<?php print $group; ?>_picture').hide();
   }
  </script>