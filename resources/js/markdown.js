// Markdown Parser and Renderer
import { marked } from 'marked';
import hljs from 'highlight.js';
import 'highlight.js/styles/github-dark.css';

// Configure marked
marked.setOptions({
    highlight: function(code, lang) {
        if (lang && hljs.getLanguage(lang)) {
            try {
                return hljs.highlight(code, { language: lang }).value;
            } catch (err) {
                console.error('Highlight error:', err);
            }
        }
        return hljs.highlightAuto(code).value;
    },
    breaks: true,
    gfm: true,
});

// Parse markdown to HTML
window.parseMarkdown = (markdown) => {
    if (!markdown) return '';
    return marked.parse(markdown);
};

// Initialize markdown rendering for all elements with data-markdown attribute
document.addEventListener('DOMContentLoaded', () => {
    const markdownElements = document.querySelectorAll('[data-markdown]');
    markdownElements.forEach(element => {
        const markdown = element.textContent || element.innerText;
        element.innerHTML = window.parseMarkdown(markdown);
        element.classList.add('markdown-content');
    });
});

// Also initialize when Livewire updates
document.addEventListener('livewire:load', () => {
    const markdownElements = document.querySelectorAll('[data-markdown]');
    markdownElements.forEach(element => {
        const markdown = element.textContent || element.innerText;
        element.innerHTML = window.parseMarkdown(markdown);
        element.classList.add('markdown-content');
    });
});

