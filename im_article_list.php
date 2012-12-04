<?php
  $category = get_categories( "orderby=name&hierarchical=0&hide_empty=0&include=$cat_ID" );
  $posts = get_posts( "numberposts=-1&post_status=pending&category=$cat_ID" );
  $post_IDs = array();
  if ( $posts ) {
    foreach ( $posts as $post ) {
      $post_IDs[] = $post->ID;
    }
  } else {
    $post_IDs[] = 0;
  }
  // take the category out of the unpublished list
  $unpublished_categories = $unpublished;
  $key = array_search( $cat_ID, $unpublished_categories );
  if ( FALSE !== $key ) {
    array_splice( $unpublished_categories, $key, 1 );
  }
  query_posts(array(
    "post__in" => $post_IDs,
    "posts_per_page" => -1,
    "category__not_in" => $unpublished_categories
  ));
?>
<div class="wrap">
<?php if ( have_posts() && isset( $category[0] ) ) : ?>
  <h2>Publishing Category: <?php echo $category[0]->cat_name; ?></h2>
  <div id="poststuff" class="metabox-holder has-right-sidebar">
    <div id="side-info-column" class="inner-sidebar">
      <div id="side-sortables" class="meta-box-sortables ui-sortable">
        <div id="submitdiv" class="postbox">
          <div class="handlediv" title="Click to toggle"><br /></div>
          <h3 class="hndle"><span>Publish Issue</span></h3>
          <form id="im_publish_form" method="get" action="edit.php">
            <div class="hidden-fields">
              <input type="hidden" name="page" id="im_publish_page" value="manage-issues" />
              <input type="hidden" name="action" id="im_publish_action" value="publish" />
              <input type="hidden" name="cat_ID" id="im_publish_cat_ID" value="<?php echo $cat_ID; ?>" />
              <input type="hidden" name="posts" id="im_publish_posts" value="" />
            </div>
            <div class="inside">
              <div id="minor-publishing">
                <div id="misc-publishing-actions">
                  <div class="misc-pub-section misc-pub-section-last">
                    <p>Publication Date/Time:</p>
                    <div id='timestampdiv'>
                      <?php
                        global $wp_locale;
                        $time_adj = time() + (get_option( 'gmt_offset' ) * 3600 );
                        $jj = gmdate( 'd', $time_adj );
                        $mm = gmdate( 'm', $time_adj );
                        $aa = gmdate( 'Y', $time_adj );
                        $hh = gmdate( 'H', $time_adj );
                        $mn = gmdate( 'i', $time_adj );
                        $ss = gmdate( 's', $time_adj );
                        $month = "<select id=\"mm\" name=\"mm\">\n";
                        for ( $i = 1; $i < 13; $i = $i +1 ) {
                          $month .= "\t\t\t" . '<option value="' . zeroise($i, 2) . '"';
                          if ( $i == $mm )
                            $month .= ' selected="selected"';
                          $month .= '>' . $wp_locale->get_month( $i ) . "</option>\n";
                        }
                        $month .= '</select>';
                        $day = '<input type="text" id="jj" name="jj" value="' . $jj . '" size="2" maxlength="2" autocomplete="off"  />';
                        $year = '<input type="text" id="aa" name="aa" value="' . $aa . '" size="4" maxlength="5" autocomplete="off"  />';
                        $hour = '<input type="text" id="hh" name="hh" value="' . $hh . '" size="2" maxlength="2" autocomplete="off"  />';
                        $minute = '<input type="text" id="mn" name="mn" value="' . $mn . '" size="2" maxlength="2" autocomplete="off"  />';
                        printf(_c('%1$s%2$s, %3$s @ %4$s : %5$s|1: month input, 2: day input, 3: year input, 4: hour input, 5: minute input'), $month, $day, $year, $hour, $minute);
                      ?>
                    </div>
                  </div>
                  <div class="clear"></div>
                </div>
              </div>
              <div id="major-publishing-actions">
                <div id="publishing-action"><input type="submit" value="Publish Issue" class="button-primary" id="publish" name="publish" /></div>
                <div class="clear"></div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div id="post-body" class="has-sidebar">
      <div id="post-body-content" class="has-sidebar-content">
        <p>Drag the post names into the order you want them to appear, from newest to oldest.</p>
        <ul class="im_article_list">
          <?php while ( have_posts() ) : the_post(); ?>
          <li id="post-<?php the_ID(); ?>" style="cursor: move; background-color: #E4F2FD; padding: 0.25em;">
            <p class="title" style="font-weight: bold; margin: 0;"><?php the_title(); ?></p>
            <p class="author" style="padding-left: 2em; font-size: 90%; margin: 0;"><?php the_author(); ?></p>
          </li>
          <?php endwhile; ?>
        </ul>
      </div>
    </div>
    <br class="clear" />
  </div>
<?php elseif ( isset( $category[0] ) ): ?>
  <h2>No pending posts in <?php echo $category[0]->cat_name; ?></h2>
<?php else: ?>
  <h2>Category <?php echo $cat_ID; ?> does not exist</h2>
<?php endif; ?>
</div>