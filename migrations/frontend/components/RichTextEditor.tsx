"use client";

import dynamic from "next/dynamic";
import { useMemo, useRef, useState, useEffect } from "react";

// Un solo chunk (QuillWrapper incluye quill + react-quill) para evitar ChunkLoadError / _next/undefined
const ReactQuill = dynamic(
  () => import(/* webpackChunkName: "quill-editor" */ "./QuillWrapper"),
  { ssr: false }
);
import "react-quill/dist/quill.snow.css";

const FONT_OPTIONS = ["Source Sans Pro", "Sans Serif", "Serif", "Monospace"];

const formats = [
  "font",
  "bold", "italic", "underline", "strike",
  "blockquote", "code-block",
  "script",
  "color", "background",
  "list", "bullet", "indent",
  "align", "header",
  "link", "image", "video",
  "code-block",
];

interface RichTextEditorProps {
  value: string;
  onChange: (value: string) => void;
  placeholder?: string;
  minHeight?: string;
  className?: string;
}

export default function RichTextEditor({
  value,
  onChange,
  placeholder = "",
  minHeight = "200px",
  className = "",
}: RichTextEditorProps) {
  const [showSource, setShowSource] = useState(false);
  const sourceValue = showSource ? value : "";
  const editorRef = useRef<{ getEditor: () => any } | null>(null);
  const setShowSourceRef = useRef(setShowSource);
  useEffect(() => {
    setShowSourceRef.current = setShowSource;
  }, [setShowSource]);

  const quillModules = useMemo(() => ({
    toolbar: {
      container: [
        [{ font: FONT_OPTIONS }],
        ["bold", "italic", "underline", "strike"],
        ["blockquote", "code-block"],
        [{ script: "sub" }, { script: "super" }],
        [{ color: [] }, { background: [] }],
        [{ list: "ordered" }, { list: "bullet" }],
        [{ indent: "-1" }, { indent: "+1" }],
        [{ align: [] }],
        [{ header: [1, 2, 3, 4, 5, 6, false] }],
        ["link", "image", "video", "table", "code"],
        ["clean"],
      ],
      handlers: {
        video(this: { quill?: any }) {
          const quill = (editorRef.current?.getEditor?.()) ?? this.quill;
          if (!quill) return;
          const url = window.prompt("URL do vídeo (YouTube, Vimeo, etc.):");
          if (!url) return;
          const range = quill.getSelection(true);
          const embed = `<p><a href="${url}" target="_blank" rel="noopener noreferrer" class="ql-video-embed">🎬 Vídeo: ${url}</a></p>`;
          quill.clipboard.dangerouslyPasteHTML(range?.index ?? 0, embed);
        },
        table(this: { quill?: any }) {
          const quill = (editorRef.current?.getEditor?.()) ?? this.quill;
          if (!quill) return;
          const range = quill.getSelection(true);
          const tableHtml = `<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;"><tr><td></td><td></td><td></td></tr><tr><td></td><td></td><td></td></tr><tr><td></td><td></td><td></td></tr></table><p><br></p>`;
          quill.clipboard.dangerouslyPasteHTML(range?.index ?? 0, tableHtml);
        },
        code() {
          setShowSourceRef.current(true);
        },
      },
    },
    history: { delay: 500, maxStack: 100, userOnly: true },
  }), []);

  const handleSourceChange = (e: React.ChangeEvent<HTMLTextAreaElement>) => {
    onChange(e.target.value);
  };

  const leaveSourceView = () => {
    setShowSource(false);
  };

  if (showSource) {
    return (
      <div className={`rich-text-editor ${className}`}>
        <div className="flex items-center justify-between gap-2 mb-1">
          <span className="text-xs text-gray-500">Código HTML</span>
          <button
            type="button"
            onClick={leaveSourceView}
            className="text-xs px-2 py-1 rounded border border-gray-300 bg-white text-gray-700 hover:bg-gray-50"
          >
            Voltar ao editor
          </button>
        </div>
        <textarea
          value={sourceValue}
          onChange={handleSourceChange}
          spellCheck={false}
          className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent font-mono text-sm resize-y min-h-[200px]"
          style={{ minHeight }}
          placeholder="<p>...</p>"
        />
      </div>
    );
  }

  return (
    <div className={`rich-text-editor ${className}`}>
      <style jsx global>{`
        .rich-text-editor .quill {
          border-radius: 0.5rem;
          border: 1px solid #d1d5db;
          background: #fff;
        }
        .rich-text-editor .ql-toolbar {
          border: none;
          border-bottom: 1px solid #e5e7eb;
          background: #f9fafb;
          border-radius: 0.5rem 0.5rem 0 0;
        }
        .rich-text-editor .ql-container {
          border: none;
          font-family: inherit;
          border-radius: 0 0 0.5rem 0.5rem;
          min-height: ${minHeight};
        }
        .rich-text-editor .ql-editor {
          min-height: ${minHeight};
        }
        .rich-text-editor .ql-editor.ql-blank::before {
          font-style: normal;
          color: #9ca3af;
        }
        .rich-text-editor .ql-video-embed {
          color: #0d9488;
        }
      `}</style>
      <ReactQuill
        ref={(el) => {
          if (el && typeof (el as any).getEditor === "function") {
            editorRef.current = el as any;
          }
        }}
        theme="snow"
        value={value}
        onChange={onChange}
        modules={quillModules}
        formats={formats}
        placeholder={placeholder}
      />
    </div>
  );
}
