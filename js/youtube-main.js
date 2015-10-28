jQuery(document).ready(function($) {
    // We can also pass the url value separately from ajaxurl for front end AJAX implementations
//    jQuery.post(ajax_object.ajax_url, data, function(response) {
//        alert('Got this from the server: ' + response);
//    });
//    console.log(ajax_object.loader_url);

    //search channels
    searchingChannels = false;
    $('#gsdSearchForm').live('submit', function(e) {
        e.preventDefault();
        var data = {
            'action': 'gsd_youtube_search_channels',
        };
        $this = $(this);
        if (searchingChannels === false) {
            data.searchTerm = $("#gsdYoutubeChannelButtonEl").val();
            data.searchType = $(".gsdYoutubeSearchByEl:checked").val();
            data.order = $(".gsdYoutubeSortByEl:checked").val();
            //find the channels
            $(".portfolioloader").show();
            $.ajax({
                type: "POST",
                url: ajax_object.ajax_url,
                data: data,
                success: function(data) {
                    $("#gsdChannelsContainer").html(data);
                    $(".portfolioloader").hide();
                },
            });
        }

    });

    //get channel videos
    $('.gsdSearchByChannelEl').live('click', function() {
        $this = $(this);
        channelId = $this.attr('data-channelid');
        channelName = $this.attr('data-channelName');
        $('.portfolioloader').show();
        $.ajax({
            type: "POST",
            url: ajax_object.ajax_url,
            data: {channelId: channelId, searchType: 'video', action: 'gsd_youtube_search_channels'},
            success: function(data) {
                $("#gsdChannelsContainer").html(data);
                $(".portfolioloader").hide();
                if (channelName) {
                    $("#videoTableh2El").html('Channel <i>' + channelName + '</i> videos');
                }
            },
        });
    });
    //get plylist videos
    $('.gsdSearchByPlaylistEl').live('click', function() {
        $this = $(this);
        playlistId = $this.attr('data-playlistId');
        playlistName = $this.attr('data-playlistName');
        $('.portfolioloader').show();
        $.ajax({
            type: "POST",
            url: ajax_object.ajax_url,
            data: {playlistId: playlistId, searchType: 'video', action: 'gsd_youtube_search_channels'},
            success: function(data) {
                $("#gsdChannelsContainer").html(data);
                $(".portfolioloader").hide();
                if (channelName) {
                    $("#videoTableh2El").html('Playlist <i>' + playlistName + '</i> videos');
                }
            },
        });
    });

    //add video
    $('.gsdAddVideoEl').live('click', function() {
        $this = $(this);
        videoid = $this.attr('data-videoId');
        videotitle = $this.attr('data-title');
        videodescription = $this.attr('data-description');
        videothumbnail = $this.attr('data-thumbnail');
        category_id = $("#category_id-" + videoid).val();
        $(".portfolioloader").show();
        $.ajax({
            type: "POST",
            url: ajax_object.ajax_url,
            data: {action: 'gsd_add_video', videoid: videoid, videotitle: videotitle, videothumbnail: videothumbnail, category_id: category_id, videodescription: videodescription},
            success: function(data) {
                $(".portfolioloader").hide();
                $this.replaceWith("<button>Added</button>");
            },
        });

    });

    $('.gsdAddChannelEl').live('click', function() {
        $this = $(this);
        channelId = $this.attr('data-channelId');
        channelEtag = $this.attr('data-etag');
        $('.portfolioloader').show();
        $.ajax({
            type: "POST",
            url: ajax_object.ajax_url,
            data: {action: 'gsd_add_channel', channelId: channelId, etag: channelEtag},
            success: function(data) {
                $('.showstudentaslist').html(data);
                $('.portfolioloader').hide();
            },
        });

    });

    $('.channelsPageLink').live('click', function() {
        $this = $(this);
        pageToken = $this.attr('data-pageToken');
        console.log('dfgdkjbg');
        if (!pageToken) {
            return false;
        }
        var data = {
            'action': 'gsd_youtube_search_channels',
        };
        //data.searchTerm = $("#gsdYoutubeChannelButtonEl").val();
        data.pageToken = pageToken;
        $('.portfolioloader').show();
        $.ajax({
            type: "POST",
            url: ajax_object.ajax_url,
            data: data,
            success: function(data) {
                $("#gsdChannelsContainer").html(data);
                $(".portfolioloader").hide();
            },
        });

    });

//    $('.videosPageLink').live('click', function() {
//        $this = $(this);
//        pageToken = $this.attr('data-pageToken');
//        console.log('dfgdkjbg');
//        if (!pageToken) {
//            return false;
//        }
//        var data = {
//            'action': 'gsd_youtube_search_channel_videos',
//        };
//        data.searchTerm = $("#gsdYoutubeChannelButtonEl").val();
//        data.pageToken = pageToken;
//        $('.portfolioloader').show();
//        $.ajax({
//            type: "POST",
//            url: ajax_object.ajax_url,
//            data: data,
//            success: function(data) {
//                $("#gsdVideosContainer").html(data);
//                $(".portfolioloader").hide();
//            },
//        });
//
//    });

});