<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $question = $request->input('question');
        $answer = null;

        if ($question) {
            try {
                $response = OpenAI::chat()->create([
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'user', 'content' => $question],
                    ],
                ]);

                $answer = $response->choices[0]->message->content;
            } catch (\Exception $e) {
                $answer = 'Erro ao consultar o ChatGPT: ' . $e->getMessage();
            }
        }

        return view('chatGPT.index', compact('question', 'answer'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:1000',
        ]);

        return redirect()->route('chat.index', ['question' => $request->question]);
    }
}
