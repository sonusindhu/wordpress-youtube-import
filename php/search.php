<?php
//Array
//(
//    [Funny Or Die] => UCzS3-65Y91JhOxFiM7j6grg
//    [Kinda Funny] => UCb4G6Wao_DeFr1dm8-a9zjg
//    [Kinda Funny Games] => UCT6QFE3peNry9PdO5uGj96g
//    [ST KR Funny] => UCkhU9r8HrJaFU8MvvdqjpBg
//    [Funny world] => UCkjX-tTLg7hlHkYp_uYT5Hw
//    [Funny Time] => UC8bfhYsKY2W1z8aZHXb7IVA
//    [BOMBA Funny Life] => UCwqVexVoSjg_mVyaW7iDX5A
//    [Funny-Fresh] => UCyZ9a6DTy4yUZePzbdq0tnw
//    [Funny FailsTV] => UCy1fDI65wfh8nWvWz--GpEg
//    [Toys and Funny Kids Surprise Eggs] => UCwjoPtSoNLAoX2sLBaKLYng
//    [Hearth Funny] => UCSk_2s9ueWpi_piryxhN-9g
//    [Lo mas funny] => UCuhp4vf8-lScDD8pp_I0OoQ
//    [ÐŸÐ Ð˜ÐšÐžÐ›Ð« Joke Funny] => UCIQPycRMZcky6uLz5FDxpoA
//    [Funny Clips] => UCZEqrCoBImiYRyn0VlPICow
//    [Anime Battles And Funny Sad Moments] => UCgQ6AwwTgV-6b_CxwdAp0-Q
//    [Dooley Funny] => UCZFOvFj6H5txss4mtRpZoow
//    [Ooops - Funny Home Videos] => UCvOeHmvqlzzsk9DBaIsORyw
//    [ABC Funny Games] => UClxJvAWTBV7XYSbeUGVlhkQ
//    [Funny VegasFX] => UC5ZytDEAe5NfHIHpA3cr2ow
//    [Funny videos - ÐŸÐ Ð˜ÐšÐžÐ›Ð« 2015] => UCGVKvhE-1BPJ7ecT2DqTsxQ
//    [FUNNY FAIL] => UChEPTlwt85qv2ZmUUFc5dpw
//    [Funny Art] => UC-BLQWKtEwNMaCn2faf2dvQ
//    [Funny Squad] => UC93QvXkR-82AN_cstMqUUvg
//    [Yes it's funny] => UCklLTAOBRj4DdxTazOzP_lA
//    [Funny Amika] => UCMcuNpwPRx7iWOg7ezcgCZQ
//)

$topChannels = array(
    0 => 'UCzS3-65Y91JhOxFiM7j6grg',
    1 => 'UCb4G6Wao_DeFr1dm8-a9zjg',
    2 => 'UCT6QFE3peNry9PdO5uGj96g',
    3 => 'UCkhU9r8HrJaFU8MvvdqjpBg',
    4 => 'UCkjX-tTLg7hlHkYp_uYT5Hw',
    5 => 'UC8bfhYsKY2W1z8aZHXb7IVA',
    6 => 'UCwqVexVoSjg_mVyaW7iDX5A',
    7 => 'UCyZ9a6DTy4yUZePzbdq0tnw',
    8 => 'UCy1fDI65wfh8nWvWz--GpEg',
    9 => 'UCwjoPtSoNLAoX2sLBaKLYng',
    10 => 'UCSk_2s9ueWpi_piryxhN-9g',
    11 => 'UCuhp4vf8-lScDD8pp_I0OoQ',
    12 => 'UCIQPycRMZcky6uLz5FDxpoA',
    13 => 'UCZEqrCoBImiYRyn0VlPICow',
    14 => 'UCgQ6AwwTgV-6b_CxwdAp0-Q',
    15 => 'UCZFOvFj6H5txss4mtRpZoow',
    16 => 'UCvOeHmvqlzzsk9DBaIsORyw',
    17 => 'UClxJvAWTBV7XYSbeUGVlhkQ',
    18 => 'UC5ZytDEAe5NfHIHpA3cr2ow',
    19 => 'UCGVKvhE-1BPJ7ecT2DqTsxQ',
    20 => 'UChEPTlwt85qv2ZmUUFc5dpw',
    21 => 'UC-BLQWKtEwNMaCn2faf2dvQ',
    22 => 'UC93QvXkR-82AN_cstMqUUvg',
    23 => 'UCklLTAOBRj4DdxTazOzP_lA',
    24 => 'UCMcuNpwPRx7iWOg7ezcgCZQ',
);

function pr($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

$htmlBody = <<<END
<form method="GET">
  <div>
    Search Term: <input type="search" id="q" name="q" placeholder="Enter Search Term">
  </div>
  <div>
    Max Results: <input type="number" id="maxResults" name="maxResults" min="1" max="50" step="1" value="25">
  </div>
  <input type="submit" value="Search">
</form>
END;

// This code will execute if the user entered a search query in the form
// and submitted the form. Otherwise, the page displays the form above.
if ($_GET['q'] && $_GET['maxResults']) {
    // Call set_include_path() as needed to point to your client library.
    require_once 'google-api-php-client-master/src/Google/autoload.php';
    require_once 'google-api-php-client-master/src/Google/Client.php';
    require_once 'google-api-php-client-master/src/Google/Service/YouTube.php';

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

    try {
        // Call the search.list method to retrieve results matching the specified
        // query term.
        $searchResponse = $youtube->search->listSearch('id,snippet', array(
            'q' => $_GET['q'],
//            'type' => 'channel',
            'type' => 'video',
            'channelId' => 'UCb4G6Wao_DeFr1dm8-a9zjg',
            'maxResults' => $_GET['maxResults'],
        ));

        $videos = '';
        $channels = '';
        $playlists = '';

        // Add each result to the appropriate list, and then display the lists of
        // matching videos, channels, and playlists.
        pr($searchResponse);
        die;
        $YTChannels = array();
        foreach ($searchResponse['items'] as $searchResult) {
            switch ($searchResult['id']['kind']) {
                case 'youtube#video':
                    $videos .= sprintf('<li>%s (%s)</li>', $searchResult['snippet']['title'], $searchResult['id']['videoId']);
                    break;
                case 'youtube#channel':
//                    $YTChannels[] = $searchResult['id']['channelId'];
                    $channels .= sprintf('<li>%s (%s)</li>', $searchResult['snippet']['title'], $searchResult['id']['channelId']);
                    break;
                case 'youtube#playlist':
                    $playlists .= sprintf('<li>%s (%s)</li>', $searchResult['snippet']['title'], $searchResult['id']['playlistId']);
                    break;
            }
        }

        $htmlBody .= <<<END
    <h3>Videos</h3>
    <ul>$videos</ul>
    <h3>Channels</h3>
    <ul>$channels</ul>
    <h3>Playlists</h3>
    <ul>$playlists</ul>
END;
    } catch (Google_ServiceException $e) {
        $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));
    } catch (Google_Exception $e) {
        $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));
    }
}
?>

<!doctype html>
<html>
    <head>
        <title>YouTube Search</title>
    </head>
    <body>
        <?= $htmlBody ?>
    </body>
</html>
