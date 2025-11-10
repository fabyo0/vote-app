// Rich Text Editor using Tiptap
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Image from '@tiptap/extension-image';
import YouTube from '@tiptap/extension-youtube';

// Initialize Tiptap editor
window.initRichTextEditor = (elementId, content = '') => {
    const element = document.getElementById(elementId);
    if (!element) return null;

    const editor = new Editor({
        element: element,
        extensions: [
            StarterKit.configure({
                heading: {
                    levels: [1, 2, 3],
                },
            }),
            Link.configure({
                openOnClick: false,
                HTMLAttributes: {
                    class: 'text-blue dark:text-blue-400 hover:underline',
                },
            }),
            Image.configure({
                HTMLAttributes: {
                    class: 'max-w-full h-auto rounded-lg',
                },
            }),
            YouTube.configure({
                width: 640,
                height: 480,
                HTMLAttributes: {
                    class: 'rounded-lg',
                },
            }),
        ],
        content: content,
        editorProps: {
            attributes: {
                class: 'prose prose-sm dark:prose-invert max-w-none focus:outline-none min-h-[200px] p-4 bg-gray-100 dark:bg-gray-700 rounded-xl',
            },
        },
    });

    return editor;
};

// Helper to get editor content as HTML
window.getEditorContent = (editor) => {
    if (!editor) return '';
    return editor.getHTML();
};

// Helper to get editor content as Markdown
window.getEditorContentAsMarkdown = (editor) => {
    if (!editor) return '';
    // Simple conversion - can be enhanced with a proper markdown converter
    return editor.getText();
};

// Helper to set editor content
window.setEditorContent = (editor, content) => {
    if (!editor) return;
    editor.commands.setContent(content);
};

