// Video Embed Parser (YouTube, Vimeo)
window.parseVideoUrl = (url) => {
    if (!url) return null;

    // YouTube patterns
    const youtubeRegex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
    const youtubeMatch = url.match(youtubeRegex);
    
    if (youtubeMatch) {
        return {
            type: 'youtube',
            id: youtubeMatch[1],
            embedUrl: `https://www.youtube.com/embed/${youtubeMatch[1]}`,
        };
    }

    // Vimeo patterns
    const vimeoRegex = /(?:vimeo\.com\/)(?:.*\/)?(\d+)/;
    const vimeoMatch = url.match(vimeoRegex);
    
    if (vimeoMatch) {
        return {
            type: 'vimeo',
            id: vimeoMatch[1],
            embedUrl: `https://player.vimeo.com/video/${vimeoMatch[1]}`,
        };
    }

    return null;
};

// Create video embed HTML
window.createVideoEmbed = (url) => {
    const videoData = window.parseVideoUrl(url);
    if (!videoData) return null;

    if (videoData.type === 'youtube') {
        return `
            <div class="video-embed my-4">
                <iframe 
                    src="${videoData.embedUrl}" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen
                    class="w-full aspect-video rounded-lg"
                ></iframe>
            </div>
        `;
    }

    if (videoData.type === 'vimeo') {
        return `
            <div class="video-embed my-4">
                <iframe 
                    src="${videoData.embedUrl}" 
                    frameborder="0" 
                    allow="autoplay; fullscreen; picture-in-picture" 
                    allowfullscreen
                    class="w-full aspect-video rounded-lg"
                ></iframe>
            </div>
        `;
    }

    return null;
};

// Auto-detect and embed videos in content
window.autoEmbedVideos = (containerSelector) => {
    const containers = document.querySelectorAll(containerSelector);
    containers.forEach(container => {
        const links = container.querySelectorAll('a[href]');
        links.forEach(link => {
            const url = link.getAttribute('href');
            const videoEmbed = window.createVideoEmbed(url);
            if (videoEmbed) {
                const embedDiv = document.createElement('div');
                embedDiv.innerHTML = videoEmbed;
                link.parentNode.replaceChild(embedDiv, link);
            }
        });
    });
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    window.autoEmbedVideos('.markdown-content, .rich-content');
});

// Initialize on Livewire updates
document.addEventListener('livewire:load', () => {
    window.autoEmbedVideos('.markdown-content, .rich-content');
});

