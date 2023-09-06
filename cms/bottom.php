<script>

    // bootstrap modal enhancement with input field
    $('body').on('show.bs.modal', '.modal', function (e) {
        // fix the problem of ios modal form with input field
        var $this = $(this);
        if (navigator.userAgent.match(/iPhone|iPad|iPod/i)) {
            // Position modal absolute and bump it down to the scrollPosition
            var heightModal = Math.max($('body').height(), $(window).height(), $(document).height()) + 1;
            $this.css({
                position: 'absolute',
                paddingTop: $(window).scrollTop() + 'px',
                height: heightModal + 'px'
            });
            // Position backdrop absolute and make it span the entire page
            //
            // Also dirty, but we need to tap into the backdrop after Boostrap
            // positions it but before transitions finish.
            //
            setTimeout(function () {
                $('.modal-backdrop').css({
                    position: 'absolute',
                    top: 0,
                    left: 0,
                    width: '100%',
                    height: heightModal + 'px'
                });
            }, 500);
        }
    });
</script>

<?php
$stime = microtime();
$stime = explode(' ', $stime);
$stime = $stime[1] + $stime[0];
$sfinish = $stime;
$total_time = round(($sfinish - $sstart), 4);
$finalrender = 'Page generated in '.$total_time.' seconds.';
?>
<?
if ($benchmark == 1) {
?>
<script>
console.log("<?=$finalrender;?>");
</script>
<?
}

include_once('on_page_css.php');
?>