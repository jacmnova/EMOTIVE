"use client";

import { useState, useRef, useEffect } from "react";
import { api } from "@/lib/api";

interface Message {
  role: "user" | "assistant";
  content: string;
}

export default function ChatPage() {
  const [messages, setMessages] = useState<Message[]>([]);
  const [input, setInput] = useState("");
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const bottomRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    bottomRef.current?.scrollIntoView({ behavior: "smooth" });
  }, [messages]);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    const text = input.trim();
    if (!text || loading) return;
    setError("");
    setInput("");
    setMessages((prev) => [...prev, { role: "user", content: text }]);
    setLoading(true);
    try {
      const res = await api<{ answer: string }>("/chat", {
        method: "POST",
        body: JSON.stringify({ question: text }),
      });
      setMessages((prev) => [...prev, { role: "assistant", content: res.answer }]);
    } catch (err) {
      setError(err instanceof Error ? err.message : "Erro ao enviar mensagem");
      setMessages((prev) => prev.slice(0, -1));
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="max-w-3xl mx-auto">
      <h1 className="text-2xl font-bold text-emotive-gray-header mb-6">Chat com ChatGPT</h1>
      <div className="bg-white rounded-xl border border-gray-200 flex flex-col min-h-[400px]">
        <div className="flex-1 p-4 overflow-y-auto space-y-4 min-h-[300px]">
          {messages.length === 0 && (
            <p className="text-gray-500 text-center py-8">Envie uma mensagem para começar.</p>
          )}
          {messages.map((m, i) => (
            <div
              key={i}
              className={`flex ${m.role === "user" ? "justify-end" : "justify-start"}`}
            >
              <div
                className={`max-w-[85%] rounded-lg px-4 py-2 ${
                  m.role === "user"
                    ? "bg-primary text-white"
                    : "bg-gray-100 text-emotive-gray-header"
                }`}
              >
                <p className="whitespace-pre-wrap text-sm">{m.content}</p>
              </div>
            </div>
          ))}
          {loading && (
            <div className="flex justify-start">
              <div className="bg-gray-100 rounded-lg px-4 py-2 text-gray-500 text-sm">
                Pensando…
              </div>
            </div>
          )}
          <div ref={bottomRef} />
        </div>
        <form onSubmit={handleSubmit} className="p-4 border-t border-gray-200">
          {error && <p className="text-red-600 text-sm mb-2">{error}</p>}
          <div className="flex gap-2">
            <input
              type="text"
              value={input}
              onChange={(e) => setInput(e.target.value)}
              placeholder="Sua mensagem..."
              maxLength={1000}
              className="flex-1 px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              disabled={loading}
            />
            <button
              type="submit"
              disabled={loading}
              className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50"
            >
              Enviar
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
