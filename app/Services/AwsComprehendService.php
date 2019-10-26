<?php

namespace App\Services;

use stdClass;
use Aws\Result;
use Aws\Comprehend\ComprehendClient;

class AwsComprehendService
{
    /**
     * @var Aws\Comprehend\ComprehendClient
     */
    private $comprehend;

    public function __construct(ComprehendClient $comprehend)
    {
        $this->comprehend = $comprehend;
    }

    /**
     * Get all analysis objects
     * 
     * @param string $text The text string to derrive sentiment about
     * 
     * @return object
     */
    public function getAnalysis($text)
    {
        $response = $this->getSentiment($text);
        $response->phrases = $this->getKeyPhrases($text);

        return $response;
    }
    

    /**
     * Determine the sentiment of the text
     * 
     * @param string $text The text string to derrive sentiment about
     * 
     * @return stdClass
     */
    public function getSentiment($text): stdClass
    {
        $awsResponse = (object)$this->comprehend->detectSentiment([

            'LanguageCode' => 'en',
            'Text' => $text,
        ]);

        return (object) collect($awsResponse)->only(['Sentiment', 'SentimentScore'])->toArray();
    }

    /**
     * Get the keywords from the text
     * 
     * @param string $text The text string to derrive sentiment about
     * 
     * @return Aws\Result
     */
    public function getKeyPhrases($text): Result
    {
        return $this->comprehend->detectKeyPhrases([
            'LanguageCode' => 'en',
            'Text' => $text,
        ]);
    }
}