<?php
// This class sends a text prompt to the Gemini API and returns the AI's reply.
// ChatBot uses this when it needs AI-generated suggestions or explanations.

class AIProcessor
{
    private string $apiKey;    // API key for Gemini
    private string $model;     // Which Gemini model to use

    public function __construct(string $apiKey, string $model = 'gemini-2.5-flash')
    {
        // Save API key and model name for later use
        $this->apiKey = $apiKey;
        $this->model  = $model;
    }

    // Sends a text prompt to Gemini and returns the AI's reply as a string
    public function generateText(string $prompt): string
    {
        // Endpoint for sending text prompts to Gemini
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";

        // Request body structure required by the Gemini API
        $payload = [
            'contents' => [
                [
                    'role'  => 'user',
                    'parts' => [
                        ['text' => $prompt]   // The actual text we want the model to answer
                    ]
                ]
            ]
        ];

        // Set up a cURL request
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-goog-api-key: ' . $this->apiKey,   // Authentication for Gemini API
            ],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 15,
        ]);

        // Execute the request and capture the raw response
        $raw = curl_exec($ch);

        // If the request completely failed (network error, timeout, etc.)
        if ($raw === false) {
            curl_close($ch);
            return "Sorry, I couldn't contact the AI service right now.";
        }

        // Read the HTTP status code before closing the connection
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Convert the raw JSON string into a PHP array
        $data = json_decode($raw, true);

        // If the API responded with an error or the JSON was invalid
        if ($status >= 400 || $data === null) {
            return "Sorry, something went wrong while talking to the AI service.";
        }

        // Extract the AI's text reply (standard response structure from Gemini)
        if (!empty($data['candidates'][0]['content']['parts'][0]['text'])) {
            return $data['candidates'][0]['content']['parts'][0]['text'];
        }

        // Fallback if the response did not contain the text we expected
        return "Sorry, the AI did not return a readable answer.";
    }
}