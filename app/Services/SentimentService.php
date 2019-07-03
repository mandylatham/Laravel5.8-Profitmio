<?php

namespace App\Services;

use App\Models\Sentiment;
use App\Models\Response;
use Illuminate\Support\Str;

class SentimentService
{
    private $service;

    public function __construct(AwsComprehendService $service)
    {
        $this->service = $service;
    }

    /**
     * Get the sentiment for a given text string
     * 
     * @param string $text The text for which to analyze sentiment
     * 
     * @return App\Models\Sentiment
     */
    public function forText($text)
    {
        $awsResponse = $this->service->getSentiment($text);

        $sentiment = new Sentiment;
        foreach (['Positive', 'Negative', 'Neutral', 'Mixed'] as $sentimentText) {
            $sentiment->$sentimentText = $awsResponse->SentimentScore[$sentimentText];
        }
        $sentiment->sentiment = Str::lower($awsResponse->Sentiment);
        
        return $sentiment;
    }

    /**
     * Get a Sentiment object for a given response
     * 
     * @param App\Models\Response $response The response to add the sentiment for
     * 
     * @return App\Models\Sentiment
     */
    public function forResponse(Response $response)
    {
        $sentiment = $this->forText($response->message);

        $response->sentiment()->delete();

        $response->sentiment()->save($sentiment);

        return $sentiment;
    }
}