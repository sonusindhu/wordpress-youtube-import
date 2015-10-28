<?php

/**
 * Plugin Name: Youtube Autoload
 * Plugin URI: http://funtuz.com
 * Description: This plugin allows to autoload videos.
 * Version: 1.0.0
 * Author: Gurpreet Singh Dhanju
 * Author URI: http://funtuz.com
 * License: GPL2
 */
function pr($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

$searchTerm = 'funny';
$maxResults = 10;
$client;
$youtube;

function youtubeSetup() {
    global $client, $youtube;
    // This code will execute if the user entered a search query in the form
// and submitted the form. Otherwise, the page displays the form above.
// Call set_include_path() as needed to point to your client library.
    require_once 'php/google-api-php-client-master/src/Google/autoload.php';
    require_once 'php/google-api-php-client-master/src/Google/Client.php';
    require_once 'php/google-api-php-client-master/src/Google/Service/YouTube.php';

    /*
     * Set $DEVELOPER_KEY to the "API key" value from the "Access" tab of the
     * {{ Google Cloud Console }} <{{ https://cloud.google.com/console }}>
     * Please ensure that you have enabled the YouTube Data API for your project.
     */
    $DEVELOPER_KEY = 'AIzaSyA_TQK9GSghep3gzDpNuUamIPFcib6jzSI';

    $client = new Google_Client();
    $client->setDeveloperKey($DEVELOPER_KEY);
//    print_r($client);
//    die;
// Define an object that will be used to make all API requests.
    $youtube = new Google_Service_YouTube($client);
}

function youtubeSearch() {
    global $client, $youtube, $maxResults, $searchTerm, $topChannels;
    youtubeSetup();
    try {
        // Call the search.list method to retrieve results matching the specified
        // query term.
        $randChanKey = array_rand($topChannels, 1);
        $randChan = $topChannels[$randChanKey];
        if($searchTerm!='playlist'){
            $playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('snippet', array(
//                'q' => $searchTerm,
                'playlistId' => $randChan,
                'maxResults' => $maxResults,
                'videoDuration' => 'short',
            ));
            pr($playlistItemsResponse);
            exit;
        }else{
            $searchResponse = $youtube->search->listSearch('id,snippet', array(
                'q' => $searchTerm,
    //            'type' => 'channel',
                'type' => 'video',
                'channelId' => $randChan,
                'maxResults' => $maxResults,
                'videoDuration' => 'short',
            ));
        }
        $videos = '';
        $channels = '';
        $playlists = '';

        // Add each result to the appropriate list, and then display the lists of
        // matching videos, channels, and playlists.
        $videos = array();
        foreach ($searchResponse['items'] as $searchResult) {
            $video['videoId'] = $searchResult['id']['videoId'];
            $video['title'] = $searchResult['snippet']['title'];
            $video['description'] = $searchResult['snippet']['description'];
            $video['publishedAt'] = $searchResult['snippet']['publishedAt'];
            $video['thumbnails']['default'] = $searchResult['snippet']['thumbnails']['default']['url'];
            $video['thumbnails']['medium'] = $searchResult['snippet']['thumbnails']['medium']['url'];
            $video['thumbnails']['high'] = $searchResult['snippet']['thumbnails']['high']['url'];
            $videos[] = $video;
        }

        do_this_hourly($videos);
    } catch (Google_ServiceException $e) {
        echo '<p>A service error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage());
    } catch (Google_Exception $e) {
        echo '<p>An client error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage());
    }
}

add_filter('cron_schedules', 'gsd_add_scheduled_interval');

function gsd_add_scheduled_interval($schedules) {
    $schedules['minutes_5'] = array('interval' => 60, 'display' => 'Once 5 minutes');
    return $schedules;
}

//On plugin activation schedule cron job
register_activation_hook(__FILE__, 'gsd_create_add_video_schedule');

function gsd_create_add_video_schedule() {
    //Use wp_next_scheduled to check if the event is already scheduled
    $timestamp = wp_next_scheduled('gsd_your_cron_hook_name_here');

    //If $timestamp == false schedule daily backups since it hasn't been done previously
    if ($timestamp == false) {
        //Schedule the event for right now, then to repeat daily using the hook 'wi_create_daily_backup'
        wp_schedule_event(time(), 'daily', 'gsd_your_cron_hook_name_here');
    }
}

//hook our function 'gsd_add_videos' to action just scheduled 'gsd_your_cron_hook_name_here'
//add_action('gsd_your_cron_hook_name_here', 'gsd_add_videos');

register_deactivation_hook(__FILE__, 'gsd_remove_add_video_schedule');

//remove schedule action on deactivation of plugin
function gsd_remove_add_video_schedule() {
    wp_clear_scheduled_hook('gsd_your_cron_hook_name_here');
}

//add videos from added channels  with cron job
function gsd_add_videos() {
    //get added channels
    $options = get_option('gsd_youtube_options');
    $options = json_decode($options, true);
    $added_channels = $options['channels'];
    //get this channel's videos
    $searchResponse = getChannelVideos($added_channels[0]);
//    pr($added_channels);
    $message = "Results: " . print_r($searchResponse, true);
    pr($message);
    die;
    mail('dhanjugopi@gmail.com', 'youtube', $message);
}

//gsd_add_videos();

if (isset($_GET['hourly'])) {
    gsd_do_this_hourly2();
    exit;
}

function gsd_do_this_hourly2() {
    set_time_limit(0);
    ini_set('max_execution_time', '60000');
    ini_set('max_input_time', '60000');
    ini_set('post_max_size', '500M');
    ini_set('upload_max_filesize', '500M');
    ini_set('mysql.connect_timeout', '60000');
    $url = 'http://www.xvideos.com/rss/rss.xml';
    $fileContents = file_get_contents($url);
    $simpleXml = simplexml_load_string($fileContents);
    $json = json_encode($simpleXml);
    $json = json_decode($json);
    $allJson = $json->channel->item;
    $xvideoII = 1;
    $howDone = 1;
    shuffle($allJson);
    foreach ($allJson as $kk => $vv) {
        $videoId_xvideo = str_replace('video', '', $vv->guid);
        $checkQuery = query_posts(array(
            'fields' => 'ids',
            'meta_query' => array(
                array(
                    'key' => 'xvideoID',
                    'value' => $vv->guid,
                )
            )
        ));
        if (empty($checkQuery)) {
            $titleTagsRand = array("xxx fuck", "sexy girl", "hot video", "hot lady", "hottest girl", "adult video", "teeny girl", "pure sex", "love sex", "desy girl", "couple sex", "honey sex", "funniest", "lovely", "hardsex", "big cock", "bigtis", "boobs", "ass", "18+", "16+", "hot lady", "blonde", "gay", "desi", "desy video", " indian sex", "english sex", "young girl", "sexy cute", "hard fucking", "old lady");

            $titleTagsRandTitle = $titleTagsRand[array_rand($titleTagsRand)] . " " . $titleTagsRand[array_rand($titleTagsRand)];
            $titleS = $vv->title;
            $title_xvideo = $titleS . " " . $titleTagsRandTitle . " from xvideos";
            $videoFile_xvideo = '<iframe src="http://flashservice.xvideos.com/embedframe/' . $videoId_xvideo . '" frameborder=0 width=510 height=400 scrolling=no></iframe>';
            $output_xvideo = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $vv->description, $matches);
            $videoImage_xvideo = $matches[1][0];
            $inputCategory = array("151", "159", "17", "121", "142", "81", "149", "83", "14", "146", "133", "120", "153", '167');
            $rand_keysNUM = array_rand($inputCategory, 1);
            $realCategory[0] = $inputCategory[$rand_keysNUM];
            $inputUser = array("1", "2", "3", "115");
            $post = array(
                'post_title' => wp_strip_all_tags($title_xvideo),
                'post_content' => $title,
                'post_category' => $realCategory,
                'post-format' => 'video',
                'post_status' => 'pending',
                'post_type' => 'post',
                'post_author' => $inputUser[array_rand($inputUser)],
            );
            $post_id = wp_insert_post($post);
            if ($post_id) {
                set_post_format($post_id, 'video');
                add_post_meta($post_id, 'dp_video_layout', 'standard', true);
                add_post_meta($post_id, 'dp_video_code', $videoFile_xvideo, true);
                add_post_meta($post_id, '_video_thumbnail', $videoImage_xvideo, true);
                add_post_meta($post_id, 'xvideoID', $vv->guid, true);
                add_post_meta($post_id, 'views', rand(100, 700), true);
                $my_post = array(
                    'ID' => $post_id,
                    'post_status' => 'publish'
                );
                wp_update_post($my_post);
                echo $xvideoII . " added<br>";
                $howDone++;
                if ($howDone >= 8) {
                    die;
                }
            }
        } else {
            echo $xvideoII . " skiped<br>";
        }
        $xvideoII++;
    }
}

function gsd_do_this_hourly($videos) {
    set_time_limit(0);
    ini_set('max_execution_time', '60000');
    ini_set('max_input_time', '60000');
    ini_set('post_max_size', '500M');
    ini_set('upload_max_filesize', '500M');
    ini_set('mysql.connect_timeout', '60000');

    $xvideoII = 1;
    $howDone = 1;
    foreach ($videos as $video) {
        $videoId_xvideo = $video['videoId'];
        $checkQuery = query_posts(array(
            'fields' => 'ids',
            'meta_query' => array(
                array(
                    'key' => 'xvideoID',
                    'value' => $video['videoId'],
                )
            )
        ));
        if (empty($checkQuery)) {
            $title_xvideo = $video['title'];
            $videoFile_xvideo = '<iframe src="https://www.youtube.com/embed/' . $videoId_xvideo . '" frameborder=0 scrolling=no></iframe>';
            $videoImage_xvideo = $video['thumbnails']['medium'];
            $inputCategory = array("151", "159", "17", "121", "142", "81", "149", "83", "14", "146", "133", "120", "153", '167');
            $rand_keysNUM = array_rand($inputCategory, 1);
            $realCategory[0] = $inputCategory[$rand_keysNUM];
            $inputUser = array("1", "2", "3", "115");
            $post = array(
                'post_title' => wp_strip_all_tags($title_xvideo),
                'post_content' => $title,
                'post_category' => $realCategory,
                'post-format' => 'video',
                'post_status' => 'pending',
                'post_type' => 'post',
                'post_author' => $inputUser[array_rand($inputUser)],
            );
            $post_id = wp_insert_post($post);
            if ($post_id) {
                set_post_format($post_id, 'video');
                add_post_meta($post_id, 'dp_video_layout', 'standard', true);
                add_post_meta($post_id, 'dp_video_code', $videoFile_xvideo, true);
                add_post_meta($post_id, '_video_thumbnail', $videoImage_xvideo, true);
                add_post_meta($post_id, 'xvideoID', $video['videoId'], true);
                add_post_meta($post_id, 'views', rand(100, 700), true);
                add_post_meta($post_id, 'likes', rand(6, 15), true);
                $my_post = array(
                    'ID' => $post_id,
                    'post_status' => 'publish'
                );
                wp_update_post($my_post);
                echo $xvideoII . " added<br>";
                $howDone++;
                if ($howDone >= 10) {
                    die;
                }
            }
        } else {
            echo $xvideoII . " skiped<br>";
        }
        $xvideoII++;
    }
}

///plugin settings
add_action('admin_menu', 'gsd_my_plugin_menu');

function gsd_my_plugin_menu() {
    add_menu_page('Youtube Plugin Settings', 'Youtube Settings', 'administrator', 'youtube-plugin-settings', 'youtube_plugin_settings_page', 'dashicons-admin-generic');
}

//page HTML content
function youtube_plugin_settings_page() {
    delete_option('gsd_youtube_options');
//    pr(getPluginOptions());
    require_once( plugin_dir_path(__FILE__) . 'templates/main_page.php');
}

//add javascript
add_action('admin_enqueue_scripts', 'gsd_my_enqueue');

function gsd_my_enqueue($hook) {
    if ('toplevel_page_youtube-plugin-settings' != $hook) {
        // Only applies to dashboard panel
        return;
    }
    wp_enqueue_style('youtube-main', plugins_url('/css/youtube-main.css', __FILE__));
    wp_enqueue_script('ajax-script', plugins_url('/js/youtube-main.js', __FILE__), array('jquery'));

    // in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
    wp_localize_script('ajax-script', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
    ));
}

//search channels
add_action('wp_ajax_gsd_youtube_search_channels', 'gsd_youtube_search_channels_callback');

function gsd_youtube_search_channels_callback() {

    global $client, $youtube, $maxResults, $searchTerm;
    $added_channels = array();
    $searchTerm = $_POST['searchTerm'];
    $pageToken = '';
    if (isset($_POST['pageToken']) and !empty($_POST['pageToken'])) {
        $pageToken = $_POST['pageToken'];
    }
    $serach_type = 'video';
    if (isset($_POST['searchType']) and !empty($_POST['searchType'])) {
        $serach_type = $_POST['searchType'];
    }
    $order = 'date';
    if (isset($_POST['order']) and !empty($_POST['order'])) {
        $order = $_POST['order'];
    }


    youtubeSetup();
    try {
        //prepare the parameter for request
        $parameters = array(
            'q' => $searchTerm,
            'maxResults' => $maxResults,
//            'videoDuration' => 'short',
        );
        $parameters['type'] = $serach_type;
        $parameters['order'] = $order;
        //add channel id if passed
        if (isset($_POST['channelId']) and !empty($_POST['channelId'])) {
            $parameters['channelId'] = $_POST['channelId'];
        }
        
        
        if (isset($_POST['playlistId']) and !empty($_POST['playlistId'])) {
            $parameters['playlistId'] = $_POST['playlistId'];
            $playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('snippet', array(
                'playlistId' => $_POST['playlistId'],
                'maxResults' => $maxResults,
//                'videoDuration' => 'short',
            ));
        }
        
        if (isset($pageToken) and !empty($pageToken)) {
            $options = getPluginOptions();
            $parameters = $options['parameters'];
            $parameters['pageToken'] = $pageToken;
        }
        
        
        if ($serach_type == 'playlist') {
            if(strlen($searchTerm) > '20'){
                $parameters['playlistId'] = $_POST['playlistId'];
                $searchResponse = $youtube->playlistItems->listPlaylistItems('snippet', array(
                    'playlistId' => $searchTerm,
                    'maxResults' => '50',
                    
                ));
            }else{
                $seeeee = file_get_contents("https://www.googleapis.com/youtube/v3/channels?part=contentDetails&forUsername=$searchTerm&key=AIzaSyA_TQK9GSghep3gzDpNuUamIPFcib6jzSI");
                $seeeee = json_decode($seeeee);
                if($seeeee->items[0]->contentDetails->relatedPlaylists->uploads){
                    $searchResponse = $youtube->playlistItems->listPlaylistItems('snippet', array(
                        'playlistId' => $seeeee->items[0]->contentDetails->relatedPlaylists->uploads,
                        'maxResults' => '50',
                       
                    ));
                }

            }
        }else{
            $searchResponse = $youtube->search->listSearch('id,snippet', $parameters);
        }

//        pr($parameters);
        // Call the search.list method to retrieve results matching the specified      
        //$searchResponse = $youtube->search->listSearch('id,snippet', $parameters);
//        pr($searchResponse);
        $plugin_data = array(
            'nextPageToken' => $searchResponse['nextPageToken'],
            'prevPageToken' => $searchResponse['prevPageToken'],
            'channelsEtag' => $searchResponse['etag'],
            'parameters' => $parameters,
        );
        //save these tokens on plugin option
        savePluginOptions($plugin_data);
        //get fresh plugin options
        $options = getPluginOptions();
        $added_channels = $options['channels'];
//        pr($searchResponse);
//        pr($options);
        if ($serach_type == 'channel') {
            require_once( plugin_dir_path(__FILE__) . 'templates/channels.php');
        }
        if ($serach_type == 'video') {
            //get all categories
            $categories = get_categories();
            require_once( plugin_dir_path(__FILE__) . 'templates/videos.php');
        }
        if ($serach_type == 'playlist') {
            //require_once( plugin_dir_path(__FILE__) . 'templates/playlists.php');
            
            $categories = get_categories();
            require_once( plugin_dir_path(__FILE__) . 'templates/videos.php');
        }

//        do_this_hourly($videos);
    } catch (Google_ServiceException $e) {
        echo '<p>A service error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage());
    } catch (Google_Exception $e) {
        echo '<p>An client error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage());
    }
    wp_die(); // this is required to terminate immediately and return a proper response
}

//add video
add_action('wp_ajax_gsd_add_video', 'gsd_add_video_callback');

function gsd_add_video_callback() {
    $video['videoId'] = $_POST['videoid'];
    $video['title'] = $_POST['videotitle'];
    $video['description'] = $_POST['videodescription'];
    $video['thumbnails']['medium'] = $_POST['videothumbnail'];
    $videoId_xvideo = $video['videoId'];
    $checkQuery = query_posts(array(
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'xvideoID',
                'value' => $video['videoId'],
            )
        )
    ));
    if (empty($checkQuery)) {
        $title_xvideo = $video['title'];
        $videoFile_xvideo = '<iframe src="https://www.youtube.com/embed/' . $videoId_xvideo . '" frameborder=0 scrolling=no></iframe>';
        $videoImage_xvideo = $video['thumbnails']['medium'];
        //$videoImage_xvideo = $video['thumbnails']['default'];
        $videoImage_xvideo = "https://i.ytimg.com/vi/".$videoId_xvideo."/0.jpg"; 
        $inputCategory = array('2', '4', '5', '6', '7');
        $rand_keysNUM = array_rand($inputCategory, 1);
        $realCategory[0] = $inputCategory[$rand_keysNUM];
        if (isset($_POST['category_id']) and !empty($_POST['category_id'])) {
            $realCategory[0] = $_POST['category_id'];
        }
        $inputUser = array("1", "2", "3", "4", '5','6','7','8','9','10','11','12');
        $posttags = array();
        $psottags = explode(' ', $title_xvideo);
        foreach ($psottags as $keyssss => $valuesss) {
            if (strlen($valuesss) > 3) {
                $posttags[$valuesss] = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $valuesss);
            }
        }
        
        
        $tagsPOSTSS = implode(' #' , $posttags);
        
        
        $postDESC = "#".$tagsPOSTSS.' #IncredibleIndia '.$video['description']." Download Free whatsApp and youtube videos whatsappmasti ".wp_strip_all_tags($title_xvideo);
        
        $postSEODesc = $video['description']." Download Free whatsApp and youtube videos whatsappmasti ".wp_strip_all_tags($title_xvideo);;
     
        
        
        $post = array(
            'post_title' => wp_strip_all_tags($title_xvideo),
            'post_content' => $postDESC,
            'post_category' => $realCategory,
            'post-format' => 'video',
            'post_status' => 'pending',
            'post_type' => 'post',
            'post_author' => $inputUser[array_rand($inputUser)],
        );

        $post_id = wp_insert_post($post);

        if ($post_id) {
            set_post_format($post_id, 'video');
            add_post_meta($post_id, 'dp_video_layout', 'standard', true);
            add_post_meta($post_id, 'dp_video_code', $videoFile_xvideo, true);
            add_post_meta($post_id, '_video_thumbnail', $videoImage_xvideo, true);
            add_post_meta($post_id, 'xvideoID', $video['videoId'], true);
            add_post_meta($post_id, 'views', rand(100, 300), true);
            add_post_meta($post_id, 'likes', rand(6, 10), true);
            
            add_post_meta($post_id, '_aioseop_title', wp_strip_all_tags($title_xvideo), true);
            add_post_meta($post_id,'_aioseop_description' ,$postSEODesc, true);
            
            $my_post = array(
                'ID' => $post_id,
                'post_status' => 'publish'
            );
            wp_update_post($my_post);
            wp_set_post_tags($post_id, $posttags, true);
            echo $xvideoII . " added<br>";
        }
    } else {
        echo $xvideoII . " skiped<br>";
    }
}

//add channel
add_action('wp_ajax_gsd_add_channel', 'gsd_add_channel_callback');

function gsd_add_channel_callback() {
    $channelId = $_POST['channelId'];
    $etag = $_POST['etag'];
    $options = getPluginOptions();
    if (!empty($options)) {
        $options = json_decode($options, true);
    }
    $options['channels'][] = array(
        'id' => $channelId,
        'etag' => $etag
    );
    update_option('gsd_youtube_options', $options);
    echo $options = get_option('gsd_youtube_options');
    wp_die();
}

function getPluginOptions() {
    $options = get_option('gsd_youtube_options');
    return $options;
}

function savePluginOptions($plugin_data = NULL) {
    if ($plugin_data == NULL) {
        return false;
    }
    $options = getPluginOptions();
    foreach ($plugin_data as $optionKey => $optionValue) {
        if ($optionKey == 'channelsEtag') {
            $optionValue = str_replace('"', '', $optionValue);
        }
        $options[$optionKey] = $optionValue;
    }
//    pr($options);
//    delete_option('gsd_youtube_options');

    update_option('gsd_youtube_options', $options);
}

//add_filter('wpas_default_prefix', 'add_default_publicize_hashtag_prefix', 10, 4);
//
//function add_default_publicize_hashtag_prefix() {
//    $default_tags = '#funny #amazing ';
//    return $default_tags;
//}