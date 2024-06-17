<?php

/**
 * Get bookmarks from the Readeck API, parse JSON, and create an RSS feed.
 */
class ReadeckRSS {

    // API URL defined in config.php
    public $api_url;

    // Feed URL defined in config.php
    public $feed_url;

    // API Token
    private $token;

    
    /**
     * Set properties.
     */
    public function __construct($api_url, $feed_url, $token)
    {
        $this->api_url = $api_url;
        $this->feed_url = $feed_url;
        $this->token = $token;
    }


    /**
     * Make request to the API and parse JSON
     */
    public function apiRequest($params=false) 
    {
        // initial cURL
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->api_url.'/api/bookmarks?'.http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer '.$this->token,
                'content-type: application/json'
            ],
        ]);

        // Send request
        $response = curl_exec($curl);
        $err = curl_error($curl);

        // Close connection
        curl_close($curl);

        // Check for errors
        if ($err) {
            echo 'cURL Error #:' . $err;
            return false;
        } else {
            return json_decode($response, true);
        }
    }


    /**
     * Create an RSS feed with provided parameteres
     */
    public function createFeed($title, $url, $description, $params)
    {
        $articles = $this->apiRequest($params);
        if ( $articles ) {
            $rss = $this->buildRSS($title, $url, $description, $articles);
            return $rss;
        } else {
            return false;
        }
    }


    /**
     * Build RSS feed string from the API JSON data
     */
    public function buildRSS(
        $title, 
        $url, 
        $description, 
        $articles
    ) {
        $feed = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $feed .= "<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">\n";
        $feed .= "\t<channel>\n";
        $feed .= "\t\t<title>".$title."</title>\n"; 
        $feed .= "\t\t<link>".$url."</link>\n";
        $feed .= "\t\t<description>".$description."</description>\n";
        $feed .= "\t\t<atom:link href=\"".$url."\" rel=\"self\" type=\"application/rss+xml\" />\n";

        foreach ( $articles as $article ) {

            if ( isset($article['title']) && $article['title'] ) {

                $feed .= "\t\t<item>\n";
                $feed .= "\t\t\t<title>" . $article['title'] . "</title>\n"; 
                $feed .= "\t\t\t<link>" . $this->api_url.'/bookmarks'.$article['id'] . "</link>\n"; 
                $feed .= "\t\t\t<author>".implode(', ', $article['authors'])."</author>\n";
                $feed .= "\t\t\t<source>" . $article['url'] . "</source>\n"; 
                if ( isset($article['resources']['thumbnail']['src']) && $article['resources']['thumbnail']['src'] ) {
                    $feed .= "\t\t\t<enclosure url=\"".$article['resources']['image']['src']."\" type=\"image/jpeg\"/>\n"; 
                }
                $feed .= "\t\t\t<description>" . $article['description'] . "</description>\n"; 
                $feed .= "\t\t\t<pubDate>".date('r', strtotime($article['created']))."</pubDate>\n"; 
                $feed .= "\t\t</item>\n"; 

            }

        }

        $feed .= "\t</channel>\n";
        $feed .= "</rss>"; 

        return $feed;
    }


    /**
     * Output the RSS text
     */
    public function printRSS($feed)
    {
        header("Content-Type: application/rss+xml; charset=UTF-8"); 
        echo $feed;
    }

}