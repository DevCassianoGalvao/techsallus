<?php
/* ─────────────────────────────────────────────────────────────
   TechSallus — Cliente OpenAI para geração de conteúdo (stub)
   ───────────────────────────────────────────────────────────── */

require_once __DIR__ . '/../config/config.php';

class OpenAI
{
    private string $apiKey;
    private string $model = 'gpt-4o-mini';

    public function __construct()
    {
        $this->apiKey = OPENAI_API_KEY;
    }

    /**
     * Gera um rascunho de artigo de blog sobre gestão hospitalar.
     * @param string $topic Tema do artigo
     * @param string $cat   Categoria (Faturamento, Gestão, etc.)
     * @return array{title: string, content: string}|null
     */
    public function generateBlogDraft(string $topic, string $cat): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        $prompt = "Escreva um artigo de blog em português brasileiro sobre o seguinte tema para gestores de clínicas e hospitais: \"{$topic}\". Categoria: {$cat}. O artigo deve ter pelo menos 600 palavras, incluir subtítulos (## H2), uma citação em blockquote, e terminar com uma chamada para ação sutil mencionando o TechSallus. Retorne JSON com as chaves 'title' e 'content' (HTML com tags p, h2, blockquote, ul, li, strong).";

        // TODO: implementar chamada real à API
        // $response = $this->request('/chat/completions', ['model' => $this->model, 'messages' => [...]]);
        return null;
    }

    private function request(string $endpoint, array $body): ?array
    {
        $ch = curl_init('https://api.openai.com/v1' . $endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($body),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_TIMEOUT        => 60,
        ]);
        $raw = curl_exec($ch);
        curl_close($ch);
        return $raw ? json_decode($raw, true) : null;
    }
}
