# 🎨 Yeni Özellikler Dokümantasyonu

## 9. UI/UX İyileştirmeleri

### Dark Mode
- **Aktifleştirme**: Header'daki ay/güneş ikonuna tıklayın veya `Ctrl+Shift+D` (Mac: `Cmd+Shift+D`) tuşlarına basın
- **Otomatik**: Sistem tercihinize göre otomatik olarak ayarlanır
- **Kalıcı**: Tercihiniz localStorage'da saklanır

### Responsive Design
- Tüm sayfalar mobile-first yaklaşımıyla tasarlandı
- Breakpoint optimizasyonları yapıldı
- Tablet ve mobil cihazlarda optimize edilmiş görünüm

### Accessibility (WCAG Uyumluluğu)
- Tüm form alanları için ARIA labels eklendi
- Semantic HTML kullanıldı
- Keyboard navigation desteği
- Focus management iyileştirildi
- Screen reader uyumluluğu

### Keyboard Shortcuts
- `g h` - Ana sayfaya git
- `g p` - Profile sayfasına git
- `g n` - Bildirimleri aç
- `c` - Idea oluştur formuna odaklan
- `/` - Arama kutusuna odaklan
- `Esc` - Modalları kapat
- `Ctrl+Shift+D` / `Cmd+Shift+D` - Dark mode toggle
- `Ctrl+?` / `Cmd+?` - Yardım modalını aç

### Drag & Drop Resim Yükleme
- Idea oluştururken resimleri sürükle-bırak ile yükleyebilirsiniz
- Drag over efekti ile görsel geri bildirim
- Klavye ile de erişilebilir (Enter veya Space)

## 10. İçerik Zenginleştirme

### Markdown Desteği
Idea ve comment içeriklerinde markdown kullanabilirsiniz:

```markdown
# Başlık
## Alt Başlık

**Kalın** ve *italik* metin

- Liste öğesi 1
- Liste öğesi 2

`kod` ve kod blokları

> Alıntı

[Link](https://example.com)
```

**Kullanım**: İçeriğinizi markdown formatında yazın, otomatik olarak render edilir.

### Code Syntax Highlighting
Kod bloklarında syntax highlighting:

````markdown
```php
public function example() {
    return 'Hello World';
}
```
````

Desteklenen diller: PHP, JavaScript, Python, HTML, CSS ve daha fazlası.

### Rich Text Editor
Tiptap tabanlı rich text editor (gelecekte kullanım için hazır):

```javascript
// Kullanım örneği
const editor = initRichTextEditor('editor-id', '<p>Initial content</p>');
const content = getEditorContent(editor);
```

### Video Embed
YouTube ve Vimeo videolarını otomatik olarak embed eder:

- YouTube: `https://www.youtube.com/watch?v=VIDEO_ID` veya `https://youtu.be/VIDEO_ID`
- Vimeo: `https://vimeo.com/VIDEO_ID`

Video URL'lerini markdown içinde veya normal link olarak kullanabilirsiniz.

### Poll/Anket Sistemi
Idea'lara poll ekleyebilirsiniz:

**Model Kullanımı**:
```php
$poll = Poll::create([
    'idea_id' => $idea->id,
    'question' => 'Hangi özelliği tercih edersiniz?',
    'options' => ['Özellik A', 'Özellik B', 'Özellik C'],
    'is_active' => true,
    'ends_at' => now()->addDays(7), // Opsiyonel
]);
```

**View'da Kullanım**:
```blade
@foreach($idea->polls as $poll)
    <livewire:poll-component :poll="$poll" :idea="$idea" :key="$poll->id" />
@endforeach
```

**Özellikler**:
- Çoklu seçenek desteği
- Anlık sonuç gösterimi
- Yüzde hesaplama
- Zaman sınırlı poll'lar
- Kullanıcı başına tek oy

## Kurulum

### 1. NPM Paketlerini Yükleyin
```bash
npm install
```

### 2. Assets'leri Build Edin
```bash
npm run dev
# veya production için
npm run build
```

### 3. Migration'ları Çalıştırın
```bash
php artisan migrate
```

## Kullanım Örnekleri

### Markdown İçerik Gösterme
```blade
<div data-markdown>
{{ $idea->description }}
</div>
```

### Video Embed
```blade
<div class="rich-content">
    <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ">Video Linki</a>
</div>
```

### Poll Oluşturma (Admin/Controller)
```php
use App\Models\Poll;

$poll = Poll::create([
    'idea_id' => $idea->id,
    'question' => 'Bu özellik hakkında ne düşünüyorsunuz?',
    'options' => [
        'Çok iyi',
        'İyi',
        'Orta',
        'Kötü'
    ],
    'is_active' => true,
]);
```

## Notlar

- Dark mode tercihi tarayıcı localStorage'ında saklanır
- Markdown rendering client-side yapılır (performans için)
- Video embed otomatik olarak algılanır ve render edilir
- Poll sistemi tamamen responsive ve accessibility uyumludur
- Tüm özellikler keyboard navigation destekler

## Gelecek İyileştirmeler

- [ ] Rich text editor'ü formlara entegre etme
- [ ] Markdown preview özelliği
- [ ] Daha fazla video platform desteği (Dailymotion, etc.)
- [ ] Poll sonuçlarını export etme
- [ ] Poll şablonları

