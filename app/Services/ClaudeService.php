<?php

namespace App\Services;

use Anthropic\Client;
use Exception;
use Illuminate\Support\Facades\Log;

class ClaudeService
{
    protected Client $client;
    protected string $model;
    protected int $maxTokens;

    public function __construct()
    {
        $this->client = new Client(apiKey: config('services.anthropic.api_key'));
        $this->model = config('services.anthropic.model', 'claude-sonnet-4-6');
        $this->maxTokens = (int) config('services.anthropic.max_tokens', 1024);
    }

    public function ask(string $prompt, ?string $systemPrompt = null): string
    {
        try {
            $extra = $systemPrompt ? ['system' => $systemPrompt] : [];
            $response = $this->client->messages->create(
                model: $this->model,
                maxTokens: $this->maxTokens,
                messages: [['role' => 'user', 'content' => $prompt]],
                ...$extra
            );
            return $response->content[0]->text;
        } catch (Exception $e) {
            Log::error('ClaudeService error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function chat(array $messages, ?string $systemPrompt = null): string
    {
        try {
            $extra = $systemPrompt ? ['system' => $systemPrompt] : [];
            $response = $this->client->messages->create(
                model: $this->model,
                maxTokens: $this->maxTokens,
                messages: $messages,
                ...$extra
            );
            return $response->content[0]->text;
        } catch (Exception $e) {
            Log::error('ClaudeService chat error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function analyze(string $text, string $instructions): array
    {
        $prompt = "Analyze: {$instructions}\n\nText:\n{$text}\n\nRespond ONLY with valid JSON.";
        $response = $this->ask($prompt, 'Always respond with valid JSON only.');
        $json = preg_replace('/```json\s*|\s*```/', '', trim($response));
        return json_decode($json, true) ?? [];
    }
}
