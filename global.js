$j = jQuery.noConflict();
$j(document).ready(function() {
  $j("a[@href^=http]").each(function() {
    if(this.href.indexOf(location.hostname) == -1) {
      $j(this).click(function(){window.open(this.href);return false;});
    }
  })
});
