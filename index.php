<?php

require_once('config.php');
require_once('ReadeckRSS.php');

if ( !$api_url || !$token ) {
    die('You need to define your Readeck URL and token in the config.php file');
}

// Create a new ReadeckRSS object
$readeck_rss = new ReadeckRSS($api_url, $feed_url, $token);

// If we have defined ?type=video in the URL string
if ( isset($_GET['type']) && $_GET['type'] == 'video' ) {

    // Optional parameters - https://your-readeck.com/docs/api#get-/bookmarks
    $params = [
        'limit' => 20, // Number of items per page
        'offset' => 0, // Pagination offset
        'type' => 'video', // Bookmark type - article, photo, or video
        'is_archived' => 0, // Filter by archived status
    ];

    $video_feed = $readeck_rss->createFeed(
        'Unwatched Videos - Readeck',
        $feed_url.'?type=video',
        'Unwatched videos saved to Readeck.',
        $params
    );

    if ( $video_feed ) {
        $readeck_rss->printRSS($video_feed);
    }
    
// If we have defined ?type=photo in the URL string
} elseif ( isset($_GET['type']) && $_GET['type'] == 'article' ) {

        // Optional parameters - https://your-readeck.com/docs/api#get-/bookmarks
        $params = [
            'limit' => 20, // Number of items per page
            'offset' => 0, // Pagination offset
            'type' => 'article',
            'is_archived' => 0, // Filter by archived status
        ];
    
        $default_feed = $readeck_rss->createFeed(
            'All unread articles - Readeck',
            $feed_url,
            'All unread articles saved to Readeck.',
            $params
        );
    
        if ( $default_feed ) {
            $readeck_rss->printRSS($default_feed);
        }

// Default feed.
} else {

    // Optional parameters - https://your-readeck.com/docs/api#get-/bookmarks
    $params = [
        'limit' => 20, // Number of items per page
        'offset' => 0, // Pagination offset
        'is_archived' => 0, // Filter by archived status
    ];

    $default_feed = $readeck_rss->createFeed(
        'All unread items - Readeck',
        $feed_url,
        'All unread items saved to Readeck.',
        $params
    );

    if ( $default_feed ) {
        $readeck_rss->printRSS($default_feed);
    }

}