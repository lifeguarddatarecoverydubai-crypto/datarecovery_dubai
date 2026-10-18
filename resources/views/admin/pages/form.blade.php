@php
    $isEdit = isset($page);
    $actionUrl = $isEdit ? route('admin.pages.update', $page) : route('admin.pages.store');
@endphp

<form action="{{ $actionUrl }}" method="POST" id="pageForm" class="space-y-8" enctype="multipart/form-data">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Left Column (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Title & Slug Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                <div>
                    <label for="pageTitle" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Page Title</label>
                    <input type="text" id="pageTitle" name="title" required value="{{ old('title', $page->title ?? '') }}" placeholder="e.g. Cleanroom Laboratory Facilities"
                           class="w-full px-4 py-3 text-lg font-bold rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>

                <!-- Slug / Permalink Box -->
                <div class="flex items-center gap-2 text-xs text-slate-500 bg-slate-50 p-2.5 rounded-xl border border-slate-200/60" x-data="{ editingSlug: false }">
                    <span class="font-semibold text-slate-600">Permalink:</span>
                    <span class="text-slate-400">{{ url('/page') }}/</span>
                    <span x-show="!editingSlug" id="slugDisplay" class="font-mono text-brand-600 font-bold">{{ old('slug', $page->slug ?? 'your-page-slug') }}</span>
                    <input type="text" id="pageSlug" name="slug" value="{{ old('slug', $page->slug ?? '') }}" x-show="editingSlug"
                           class="px-2 py-0.5 rounded border border-slate-300 font-mono text-xs focus:outline-none focus:border-brand-500 w-64">
                    <button type="button" @click="editingSlug = !editingSlug" class="ml-auto text-xs text-brand-600 font-semibold hover:underline">
                        <span x-text="editingSlug ? 'Done' : 'Edit'"></span>
                    </button>
                </div>
            </div>

            <!-- Page Content Editor (Dual-Mode: Visual / HTML Source / Live Preview) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden" 
                 x-data="pageContentEditor({ initial: @js(old('content', $page->content ?? '')) })">
                
                <!-- Toolbar Header -->
                <div class="p-4 bg-slate-900 text-white flex flex-wrap items-center justify-between gap-3 border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-300 flex items-center gap-1.5">
                            <i data-lucide="file-text" class="w-4 h-4 text-brand-400"></i>
                            <span>Page Content</span>
                        </span>
                        
                        <!-- Mode Selector Tabs -->
                        <div class="flex items-center bg-slate-800 p-1 rounded-xl text-xs font-semibold">
                            <button type="button" @click="setTab('code')" 
                                    :class="activeTab === 'code' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:text-white'"
                                    class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                                <i data-lucide="code" class="w-3.5 h-3.5"></i>
                                <span>HTML Source</span>
                            </button>
                            <button type="button" @click="setTab('visual')" 
                                    :class="activeTab === 'visual' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:text-white'"
                                    class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                <span>Visual WYSIWYG</span>
                            </button>
                            <button type="button" @click="setTab('preview')" 
                                    :class="activeTab === 'preview' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:text-white'"
                                    class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                                <i data-lucide="monitor" class="w-3.5 h-3.5"></i>
                                <span>Live Preview</span>
                            </button>
                        </div>
                    </div>

                    <button type="button" onclick="chooseMediaForPage()" 
                            class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center gap-1.5 border border-slate-700 transition">
                        <i data-lucide="image-plus" class="w-3.5 h-3.5 text-brand-400"></i>
                        <span>Add Media</span>
                    </button>
                </div>

                <!-- Hidden Input Bound to Form Submission -->
                <textarea name="content" id="pageContentInput" class="hidden" x-model="content"></textarea>

                <!-- TAB 1: HTML Source Code View -->
                <div x-show="activeTab === 'code'" class="p-4 bg-slate-950 font-mono text-xs">
                    <div class="flex items-center justify-between text-[11px] text-slate-400 mb-2 pb-2 border-b border-slate-800">
                        <span>Raw HTML editor &bull; Preserves all custom classes, tables, grids, and formatting.</span>
                        <span x-text="content.length + ' characters'"></span>
                    </div>
                    <textarea id="pageCodeTextarea" x-model="content" @input="syncFromCode()" rows="22"
                              class="w-full bg-slate-900 text-emerald-300 p-4 rounded-xl border border-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500 font-mono text-xs leading-relaxed"></textarea>
                </div>

                <!-- TAB 2: Visual WYSIWYG Editor -->
                <div x-show="activeTab === 'visual'" class="bg-white">
                    <div class="p-2.5 bg-slate-100 border-b border-slate-200 flex flex-wrap items-center gap-1.5 text-xs text-slate-700">
                        <button type="button" @click="execCommand('bold')" class="p-1.5 hover:bg-slate-200 rounded font-bold" title="Bold"><b>B</b></button>
                        <button type="button" @click="execCommand('italic')" class="p-1.5 hover:bg-slate-200 rounded italic" title="Italic"><i>I</i></button>
                        <button type="button" @click="execCommand('underline')" class="p-1.5 hover:bg-slate-200 rounded underline" title="Underline"><u>U</u></button>
                        <span class="text-slate-300">|</span>
                        <button type="button" @click="execFormat('h2')" class="px-2 py-1 hover:bg-slate-200 rounded font-bold text-xs" title="Heading 2">H2</button>
                        <button type="button" @click="execFormat('h3')" class="px-2 py-1 hover:bg-slate-200 rounded font-bold text-xs" title="Heading 3">H3</button>
                        <button type="button" @click="execFormat('p')" class="px-2 py-1 hover:bg-slate-200 rounded text-xs" title="Paragraph">P</button>
                        <span class="text-slate-300">|</span>
                        <button type="button" @click="execCommand('insertUnorderedList')" class="p-1.5 hover:bg-slate-200 rounded" title="Bullet List">&bull; List</button>
                        <button type="button" @click="execCommand('insertOrderedList')" class="p-1.5 hover:bg-slate-200 rounded" title="Numbered List">1. List</button>
                        <span class="text-slate-300">|</span>
                        <button type="button" @click="execPromptLink()" class="px-2 py-1 hover:bg-slate-200 rounded text-xs" title="Insert Link">🔗 Link</button>
                        <button type="button" @click="execCommand('removeFormat')" class="px-2 py-1 hover:bg-slate-200 rounded text-xs text-slate-500" title="Clear Formatting">Clear</button>
                    </div>

                    <div id="pageVisualEditorContainer" contenteditable="true" @input="syncFromVisual()" 
                         class="p-6 min-h-[440px] focus:outline-none prose prose-slate max-w-none text-slate-800 text-sm leading-relaxed"
                         x-html="content"></div>
                </div>

                <!-- TAB 3: Full Frontend Live Preview -->
                <div x-show="activeTab === 'preview'" class="p-8 bg-slate-50 min-h-[440px]">
                    <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm max-w-4xl mx-auto">
                        <div class="prose prose-slate max-w-none text-slate-800 text-sm sm:text-base leading-relaxed" x-html="content"></div>
                    </div>
                </div>
            </div>

            <!-- Yoast SEO Box Component -->
            @include('admin.partials.yoast-seo-box', [
                'model' => $page ?? null,
                'titleInputId' => 'pageTitle',
                'slugInputId' => 'pageSlug',
                'contentInputId' => 'pageContentInput',
                'urlPrefix' => url('/page'),
                'type' => 'page'
            ])
        </div>

        <!-- Right Sidebar (1 Col) -->
        <div class="space-y-6">
            <!-- Publish Action Box -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-3 flex items-center justify-between">
                    <span>Publish Settings</span>
                    @if($isEdit)
                        <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="text-xs font-semibold text-brand-600 hover:underline">Preview ↗</a>
                    @endif
                </h3>

                <!-- Status Selector -->
                <div>
                    <label for="pageStatus" class="block text-xs font-semibold text-slate-700 mb-1.5">Status</label>
                    <select id="pageStatus" name="status" class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                        <option value="draft" {{ old('status', $page->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $page->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                            class="w-full py-2.5 px-4 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold shadow-md shadow-brand-500/20 transition flex items-center justify-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>{{ $isEdit ? 'Update Page' : 'Publish / Save Page' }}</span>
                    </button>
                </div>
            </div>

            <!-- Page Template Attributes -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider border-b border-slate-100 pb-2">Page Attributes</h3>
                <div>
                    <label for="pageTemplate" class="block text-xs font-semibold text-slate-700 mb-1.5">Template</label>
                    <select id="pageTemplate" name="template" class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                        @foreach($templates as $key => $name)
                            <option value="{{ $key }}" {{ old('template', $page->template ?? 'default') === $key ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Featured Image Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider border-b border-slate-100 pb-2">Featured Image</h3>
                
                <input type="hidden" id="featuredImageInput" name="featured_image" value="{{ old('featured_image', $page->featured_image ?? '') }}">

                <div id="featuredImageContainer" class="space-y-3">
                    @php
                        $featImg = old('featured_image', $page->featured_image ?? null);
                        $featUrl = null;
                        if ($featImg) {
                            if (str_starts_with($featImg, 'http://') || str_starts_with($featImg, 'https://')) {
                                $featUrl = $featImg;
                            } elseif (str_starts_with($featImg, '/storage/') || str_starts_with($featImg, 'storage/')) {
                                $featUrl = asset(ltrim($featImg, '/'));
                            } else {
                                $featUrl = asset('storage/' . ltrim($featImg, '/'));
                            }
                        }
                    @endphp

                    <div id="featuredImagePreview" class="relative aspect-video rounded-xl overflow-hidden bg-slate-100 border border-slate-200 {{ $featUrl ? '' : 'hidden' }}">
                        <img src="{{ $featUrl }}" alt="Featured Image" id="featuredImgTag" class="w-full h-full object-cover">
                    </div>

                    <div id="noFeaturedImageText" class="p-6 text-center border-2 border-dashed border-slate-200 rounded-xl bg-slate-50 {{ $featUrl ? 'hidden' : '' }}">
                        <i data-lucide="image" class="w-8 h-8 text-slate-300 mx-auto mb-1"></i>
                        <p class="text-xs text-slate-400">No featured image set</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="chooseFeaturedImage()" class="flex-1 py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold rounded-xl transition text-center flex items-center justify-center gap-1.5">
                            <i data-lucide="image" class="w-3.5 h-3.5 text-slate-500"></i>
                            <span>Select Media</span>
                        </button>
                        <button type="button" onclick="document.getElementById('pageFeaturedImageFileInput').click()" class="flex-1 py-2 px-3 bg-brand-50 hover:bg-brand-100 text-brand-700 text-xs font-semibold rounded-xl transition text-center flex items-center justify-center gap-1.5">
                            <i data-lucide="upload" class="w-3.5 h-3.5 text-brand-600"></i>
                            <span>Upload Image</span>
                        </button>
                        <input type="file" id="pageFeaturedImageFileInput" name="featured_image_file" accept="image/*" class="hidden" onchange="uploadPageFeaturedImageDirectly(this.files[0])">
                        <button type="button" id="removeFeaturedBtn" onclick="removeFeaturedImage()" class="py-2 px-3 text-rose-600 hover:bg-rose-50 text-xs font-semibold rounded-xl transition {{ $featUrl ? '' : 'hidden' }}">
                            Remove
                        </button>
                    </div>
                    <div id="pageFeaturedImageUploadStatus" class="hidden text-center pt-1"></div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
function pageContentEditor(config) {
    return {
        activeTab: 'code',
        content: config.initial || '',
        setTab(tab) {
            if (this.activeTab === 'visual') {
                const vis = document.getElementById('pageVisualEditorContainer');
                if (vis) this.content = vis.innerHTML;
            }
            this.activeTab = tab;
            if (tab === 'visual') {
                this.$nextTick(() => {
                    const vis = document.getElementById('pageVisualEditorContainer');
                    if (vis) vis.innerHTML = this.content;
                });
            }
        },
        syncFromCode() {
            document.getElementById('pageContentInput').value = this.content;
        },
        syncFromVisual() {
            const vis = document.getElementById('pageVisualEditorContainer');
            if (vis) {
                this.content = vis.innerHTML;
                document.getElementById('pageContentInput').value = this.content;
            }
        },
        execCommand(cmd, val = null) {
            document.execCommand(cmd, false, val);
            this.syncFromVisual();
        },
        execFormat(tag) {
            document.execCommand('formatBlock', false, tag);
            this.syncFromVisual();
        },
        execPromptLink() {
            const url = prompt('Enter link URL:');
            if (url) {
                document.execCommand('createLink', false, url);
                this.syncFromVisual();
            }
        }
    };
}

function chooseMediaForPage() {
    openMediaModal(function(item) {
        const imgHtml = `\n<img src="${item.url}" alt="${item.name || 'LifeGuard Data Recovery'}" class="rounded-2xl max-w-full h-auto my-4 shadow-sm" />\n`;
        const codeArea = document.getElementById('pageCodeTextarea');
        const vis = document.getElementById('visualEditorContainer');
        const hiddenInput = document.getElementById('pageContentInput');

        if (codeArea) {
            codeArea.value += imgHtml;
            codeArea.dispatchEvent(new Event('input', { bubbles: true }));
        }
        if (vis) {
            vis.innerHTML += imgHtml;
            vis.dispatchEvent(new Event('input', { bubbles: true }));
        }
        if (hiddenInput) {
            hiddenInput.value += imgHtml;
            hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
    });
}

function chooseFeaturedImage() {
    openMediaModal(function(item) {
        document.getElementById('featuredImageInput').value = item.file_path || item.url;
        document.getElementById('featuredImgTag').src = item.url;
        document.getElementById('featuredImagePreview').classList.remove('hidden');
        document.getElementById('noFeaturedImageText').classList.add('hidden');
        document.getElementById('removeFeaturedBtn').classList.remove('hidden');
    });
}

function removeFeaturedImage() {
    document.getElementById('featuredImageInput').value = '';
    document.getElementById('featuredImagePreview').classList.add('hidden');
    document.getElementById('noFeaturedImageText').classList.remove('hidden');
    document.getElementById('removeFeaturedBtn').classList.add('hidden');
}

// Slug generator
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('pageTitle');
    const slugInput = document.getElementById('pageSlug');
    const slugDisplay = document.getElementById('slugDisplay');

    @if(!$isEdit)
    if (titleInput && slugInput && slugDisplay) {
        titleInput.addEventListener('input', function() {
            const slug = this.value.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
            if (!slugInput.dataset.manual) {
                slugInput.value = slug;
                slugDisplay.textContent = slug || 'your-page-slug';
            }
        });
    }
    @endif
});

// Direct Featured Image Upload with Automated WebP Conversion
function uploadPageFeaturedImageDirectly(file) {
    if (!file) return;
    const statusEl = document.getElementById('pageFeaturedImageUploadStatus');
    if (statusEl) {
        statusEl.classList.remove('hidden');
        statusEl.innerHTML = '<span class="inline-flex items-center gap-1.5 text-[11px] text-brand-600 font-semibold"><span class="animate-spin w-3 h-3 border-2 border-brand-600 border-t-transparent rounded-full"></span> Converting to WebP & compressing...</span>';
    }

    const formData = new FormData();
    formData.append('file', file);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch('{{ route("admin.media.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.media && data.media.length > 0) {
            const item = data.media[0];
            document.getElementById('featuredImageInput').value = item.url;
            document.getElementById('featuredImgTag').src = item.url;
            document.getElementById('featuredImagePreview').classList.remove('hidden');
            document.getElementById('noFeaturedImageText').classList.add('hidden');
            document.getElementById('removeFeaturedBtn').classList.remove('hidden');
            if (statusEl) {
                statusEl.innerHTML = '<span class="text-[11px] text-emerald-600 font-bold">✓ Converted to WebP (' + (item.dimensions || item.human_size) + ')</span>';
            }
        }
    })
    .catch(err => {
        if (statusEl) {
            statusEl.innerHTML = '<span class="text-[11px] text-amber-600 font-semibold">Saved for automatic WebP conversion on submit.</span>';
        }
    });
}

// Visual Editor Paste Image to WebP Handler
document.addEventListener('DOMContentLoaded', function () {
    const visualEditor = document.getElementById('pageVisualEditorContainer');
    if (visualEditor) {
        visualEditor.addEventListener('paste', function (e) {
            const items = (e.clipboardData || e.originalEvent.clipboardData).items;
            for (let i in items) {
                const item = items[i];
                if (item.kind === 'file' && item.type.startsWith('image/')) {
                    e.preventDefault();
                    const blob = item.getAsFile();
                    const formData = new FormData();
                    formData.append('file', blob);
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch('{{ route("admin.media.store") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.media && data.media.length > 0) {
                            document.execCommand('insertImage', false, data.media[0].url);
                        }
                    });
                    break;
                }
            }
        });
    }
});
</script>
@endpush
