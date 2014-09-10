<?php
// TwitterFeed extension for Bolt
// Minimum version: 1.4

namespace LatestTweet;
require_once "TwitterAPIExchange.php";

use TwitterAPIExchange;
use DateTime;

class Extension extends \Bolt\BaseExtension
{

    public $twitter;
    public $settings;

    function info() {

        $data = array(
            'name' =>"Twitter Tweet",
            'description' => "An extension to add your latest tweet to your site when using <code>{{ tweet() }}</code> in templates.",
            'author' => "Daniel Gamage",
            'link' => "http://bolt.cm",
            'version' => "0.4",
            'required_bolt_version' => "1.4",
            'highest_bolt_version' => "2.0",
            'type' => "Twig function",
            'first_releasedate' => "2014-09-10",
            'latest_releasedate' => "2014-09-10",
        );

        return $data;
    }

    function initialize() {
        $this->addTwigFunction('tweet', 'twigTweet');
    }

    function twigTweet() {

        $settings = array(
            'oauth_access_token' => $this->config['oauth_access_token'],
            'oauth_access_token_secret' => $this->config['oauth_access_token_secret'],
            'consumer_key' => $this->config['consumer_key'],
            'consumer_secret' => $this->config['consumer_secret']
        );

        /** Perform a GET request and echo the response **/
        /** Note: Set the GET field BEFORE calling buildOauth(); **/
        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $getfield = '?screen_name=EatRockhill';
        $requestMethod = 'GET';
        $twitter = new TwitterAPIExchange($settings);
        $response = $twitter->setGetfield($getfield)
                     ->buildOauth($url, $requestMethod)
                     ->performRequest();
        $tweet = json_decode($response, true);
        $date = new DateTime( $tweet[0]['created_at'] );
        $date = $date->format( 'j M' );
        $profile = "https://twitter.com/" . $tweet[0]['user']['screen_name'];
        $html = "<header><a href='" . $profile . "'>@" . $tweet[0]['user']['screen_name'] . "</a> • " . $date . "</header>" . "<p>" . $tweet[0]['text'] . "</p>";

        return new \Twig_Markup($html, 'UTF-8');
    }
}
