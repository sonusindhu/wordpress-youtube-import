<h2>Searched Playlists</h2>
<table class="wp-list-table widefat fixed striped posts">
    <thead>
        <tr>
            <th>No.</th>
            <!--<th>Channel ID</th>-->
            <th>Title</th>
            <th>Channel Image</th>
            <th>See on Youtube</th>
            <th>Search Videos</th>
            <th>Add Playlist</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $counter = 1;
        foreach ($searchResponse['items'] as $searchResult) {
//            pr($searchResult);
            $video['playlistId'] = $searchResult['id']['playlistId'];
            $video['title'] = $searchResult['snippet']['title'];
            $video['description'] = $searchResult['snippet']['description'];
            $video['publishedAt'] = $searchResult['snippet']['publishedAt'];
            $video['thumbnails']['default'] = $searchResult['snippet']['thumbnails']['default']['url'];
            $video['thumbnails']['medium'] = $searchResult['snippet']['thumbnails']['medium']['url'];
            $video['thumbnails']['high'] = $searchResult['snippet']['thumbnails']['high']['url'];
//            pr($video);
            ?> 
            <tr>
                <td><?php echo $counter ?></td>
                <td><?php echo $video['title'] ?></td>
                <td><img width="50" src="<?php echo $video['thumbnails']['default'] ?>"></td>
                <td><a target="_blank" href="https://www.youtube.com/playlist/?list=<?php echo $video['playlistId'] ?>">See on Youtube</a></td>
                <td><button data-playlistId="<?php echo $video['playlistId'] ?>" data-channelName="<?php echo $video['title'] ?>" class="btn btn-primary gsdSearchByPlaylistEl">Get Playlist Videos</button></td>
                <td>
                    <?php
                    if (is_array($added_channels) and in_array($video['channelId'], $added_channels)) {
                        ?> 
                        <button class="btn btn-primary">Added</button>
                        <?php
                    } else {
                        ?>
                        <button data-etag=<?php echo $channelEtag ?> data-channelId="<?php echo $video['channelId'] ?>" class="btn btn-primary gsdAddChannelEl">Add Playlist</button>
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
        <span class="displaying-num">10 channels</span>
        <span class="pagination-links">
            <a data-pageToken="<?php echo $options['prevPageToken'] ?>" class="first-page <?php if (empty($options['prevPageToken'])) echo 'disabled'; ?> channelsPageLink" title="Previous Page" href="javascript:;">«</a>
            <a data-pageToken="<?php echo $options['nextPageToken'] ?>" class="last-page <?php if (empty($options['nextPageToken'])) echo 'disabled'; ?>  channelsPageLink" title="Next Page" href="javascript:;">»</a>
        </span>
    </div>
    <br class="clear">
</div>