<div class="wrap">
  <h2>Manage Issues</h2>
  
  <table class="widefat im_category_list">
    <thead>
      <tr>
        <th scope="col">Category Name</th>
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col"></th>
      </tr>
    </thead>
    <tbody>
    <?php $alt = ' class="alternate"'; ?>
    <?php foreach ( $categories as $cat ): ?>
      <?php
        if ( in_array($cat->cat_ID, $published) )
          $status = "published";
        elseif ( in_array($cat->cat_ID, $unpublished) )
          $status = "unpublished";
        else
          $status = "ignored";
      ?>
      <tr id="cat-<?php echo $cat->cat_ID; ?>"<?php echo $alt; ?>>
        <td><strong><a title='Edit the status of "<?php echo $cat->cat_name; ?>"' href="categories.php?action=edit&cat_ID=<?php echo $cat->cat_ID; ?>"><?php echo $cat->cat_name; ?></a></strong></td>
        <td><?php
          if ( "published" == $status ) { echo "<strong>Published</strong>"; }
          else { echo "<a class='im-publish' href='?page=manage-issues&amp;action=list&amp;cat_ID=$cat->cat_ID'>Publish</a>"; }
        ?></td>
        <td><?php
          if ( "unpublished" == $status ) { echo "<strong>Unpublished</strong>"; }
          else { echo "<a class='im-unpublish' href='?page=manage-issues&amp;action=unpublish&amp;cat_ID=$cat->cat_ID'>Unpublish</a>"; }
        ?></td>
        <td><?php
          if ( "ignored" == $status ) { echo "<strong>Ignored</strong>"; }
          else { echo "<a class='im-ignore' href='?page=manage-issues&amp;action=ignore&amp;cat_ID=$cat->cat_ID'>Ignore</a>"; }
        ?></td>
      </tr>
      <?php $alt = empty( $alt ) ? ' class="alternate"' : ''; ?>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>