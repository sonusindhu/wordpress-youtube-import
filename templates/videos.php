<h2 id="videoTableh2El">Searched videos</h2>
<table class="wp-list-table widefat fixed striped posts">
    <thead>
        <tr>
            <th width="5%">No.</th>
            <!--<th>Video ID</th>-->
            <th>Title</th>
            <th>Video Image</th>
            <th>Published On</th>
            <th>See on Youtube</th>
            <th>Add</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $counter = 1;
        foreach ($searchResponse['items'] as $searchResult) {
            
            $video['videoId'] = $searchResult['id']['videoId'];
            $video['title'] = $searchResult['snippet']['title'];
            $video['description'] = preg_replace('/[^A-Za-z0-9\- ]/', '', $searchResult['snippet']['description']);
            
            $pattern = "/[a-zA-Z]*[:\/\/]*[A-Za-z0-9\-_]+\.+[A-Za-z0-9\.\/%&=\?\-_]+/i";
            $replacement = " ";
            $video['description'] = preg_replace($pattern, $replacement, $video['description']);
            
            $video['publishedAt'] = $searchResult['snippet']['publishedAt'];
            $video['thumbnails']['default'] = $searchResult['snippet']['thumbnails']['default']['url'];
            $video['thumbnails']['medium'] = $searchResult['snippet']['thumbnails']['medium']['url'];
            $video['thumbnails']['high'] = $searchResult['snippet']['thumbnails']['high']['url'];
            
//            pr($searchResult['resourceId']);exit;
            
            if($video['videoId']=='U' || $video['videoId']==''){
                $video['videoId'] = $searchResult['snippet']['resourceId']['videoId'];
            }
            
            //check if video is already added
            $checkQuery = query_posts(array(
                'fields' => 'ids',
                'meta_query' => array(
                    array(
                        'key' => 'xvideoID',
                        'value' => $video['videoId'],
                    )
                )
            ));
//            pr($video);
//            die;
            ?>
            <tr>
                <td><?php echo $counter; ?></td>
                <!--<td><?php echo $video['videoId'] ?></td>-->
                <td><?php echo $video['title'] ?></td>
                <td><img width="50" src="<?php echo $video['thumbnails']['default'] ?>"></td>
                <td><?php echo date('Y-m-d', strtotime($video['publishedAt'])) ?></td>

                <td><a target="_blank" href="https://www.youtube.com/watch?v=<?php echo $video['videoId'] ?>">See on Youtube</a></td>
                <td>
                    <?php
                    if (empty($checkQuery)) {
                        ?> 
                        <select name="category_id" id="category_id-<?php echo $video['videoId'] ?>" class="category_select">
                            <option value="0">Category</option>
                            <?php
                            foreach ($categories as $category) {
                                echo '<option value=' . $category->term_id . '>' . $category->cat_name . '</option>';
                            }
                            ?>
                        </select>
                        <button data-videoId="<?php echo $video['videoId'] ?>" data-title="<?php echo $video['title'] ?>" data-description="<?php echo $video['description'] ?>" data-thumbnail="<?php echo $video['thumbnails']['medium'] ?>" class="button button-primary gsdAddVideoEl">Add video</button>
                        <?php
                    } else {
                        ?> 
                        <button>Added</button>
                        <?php
                    }
                    ?>
                </td>
            </tr>

            <?php
            $counter++;
        }
        ?>
    </tbody>
</table>

<div class="tablenav bottom">
    <div class="tablenav-pages">
        <span class="displaying-num">10 videos</span>
        <span class="pagination-links">
            <a data-pageToken="<?php echo $options['prevPageToken'] ?>" class="first-page <?php if (empty($options['prevPageToken'])) echo 'disabled'; ?> channelsPageLink" title="Previous Page" href="javascript:;">«</a>
            <a data-pageToken="<?php echo $options['nextPageToken'] ?>" class="last-page <?php if (empty($options['nextPageToken'])) echo 'disabled'; ?>  channelsPageLink" title="Next Page" href="javascript:;">»</a>
        </span>
    </div>
    <br class="clear">
</div>