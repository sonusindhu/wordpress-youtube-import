<img class="portfolioloader" src="<?php echo plugins_url('../images/big-roller.gif', __FILE__) ?>" style="position: fixed;top: 50%;left: 50%; display: none">
<div class="wrap">
    <h2>Youtube settings</h2>
    <form method="post" id="gsdSearchForm">
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Search</th>
                <td>
                    <label><input name="search" checked class="gsdYoutubeSearchByEl" type="radio" value="video" /> Videos </label>
                    <label><input name="search" class="gsdYoutubeSearchByEl" type="radio" value="channel" /> Channels</label> 
                    <label><input name="search" class="gsdYoutubeSearchByEl" type="radio" value="playlist" /> Playlists </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"></th>
                <td><input name="q" id="gsdYoutubeChannelButtonEl" type="text" value="" /></td>
            </tr>
             <tr valign="top">
                <th scope="row">Sort By</th>
                <td>
                    <label><input name="order" checked class="gsdYoutubeSortByEl" type="radio" value="date" /> Date </label>
                    <label><input name="order" class="gsdYoutubeSortByEl" type="radio" value="rating" /> Rating</label> 
                    <label><input name="order" class="gsdYoutubeSortByEl" type="radio" value="relevance" /> Relevance </label>                  
                    <label><input name="order" class="gsdYoutubeSortByEl" type="radio" value="title" /> Title </label>
                    <label><input name="order" class="gsdYoutubeSortByEl" type="radio" value="videoCount" /> Video Count </label>
                    <label><input name="order" class="gsdYoutubeSortByEl" type="radio" value="viewCount" /> View Count </label>
                </td>
            </tr>
        </table>
        <button type="submit" class="button button-primary" id="gsdSubmitButtonEl">SEARCH</button>
    </form>
</div>

<div class="wrap" id="gsdChannelsContainer">

</div>