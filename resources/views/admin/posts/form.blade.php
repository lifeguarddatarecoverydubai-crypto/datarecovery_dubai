@php
    $isEdit = isset($post);
    $actionUrl = $isEdit ? route('admin.posts.update', $post) : route('admin.posts.store');
@endphp

<form action="{{ $actionUrl }}" method="POST" id="postForm" class="space-y-8" enctype="multipart/form-data">
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
                    <label for="postTitle" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Post Title</label>
                    <input type="text" id="postTitle" name="title" required value="{{ old('title', $post->title ?? '') }}" placeholder="Enter an engaging post title..."
                           class="w-full px-4 py-3 text-lg font-bold rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>

                <!-- Slug / Permalink Box -->
                <div class="flex items-center gap-2 text-xs text-slate-500 bg-slate-50 p-2.5 rounded-xl border border-slate-200/60" x-data="{ editingSlug: false }">
                    <span class="font-semibold text-slate-600">Permalink:</span>
                    <span class="text-slate-400">{{ url('/') }}/</span>
                    <span x-show="!editingSlug" id="slugDisplay" class="font-mono text-brand-600 font-bold">{{ old('slug', $post->slug ?? 'your-post-slug') }}</span>
                    <input type="text" id="postSlug" name="slug" value="{{ old('slug', $post->slug ?? '') }}" x-show="editingSlug"
                           class="px-2 py-0.5 rounded border border-slate-300 font-mono text-xs focus:outline-none focus:border-brand-500 w-64">
                    <button type="button" @click="editingSlug = !editingSlug" class="ml-auto text-xs text-brand-600 font-semibold hover:underline">
                        <span x-text="editingSlug ? 'Done' : 'Edit'"></span>
                    </button>
                </div>
            </div>

            <!-- Content Editor (Triple-Mode: Visual WYSIWYG / HTML Source Code / Live Preview) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden" 
                 x-data="postContentEditor({ initial: @js(old('content', $post->content ?? '')) })">
                
                <!-- Toolbar Header -->
                <div class="p-4 bg-slate-900 text-white flex flex-wrap items-center justify-between gap-3 border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-300 flex items-center gap-1.5">
                            <i data-lucide="file-text" class="w-4 h-4 text-brand-400"></i>
                            <span>Article Content</span>
                        </span>
                        
                        <!-- Mode Selector Tabs -->
                        <div class="flex items-center bg-slate-800 p-1 rounded-xl text-xs font-semibold">
                            <button type="button" @click="setTab('visual')" 
                                    :class="activeTab === 'visual' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:text-white'"
                                    class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                <span>Visual WYSIWYG</span>
                            </button>
                            <button type="button" @click="setTab('code')" 
                                    :class="activeTab === 'code' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:text-white'"
                                    class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                                <i data-lucide="code" class="w-3.5 h-3.5"></i>
                                <span>HTML Source</span>
                            </button>
                            <button type="button" @click="setTab('preview')" 
                                    :class="activeTab === 'preview' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:text-white'"
                                    class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                                <i data-lucide="monitor" class="w-3.5 h-3.5"></i>
                                <span>Live Preview</span>
                            </button>
                        </div>
                    </div>

                    <!-- Quick Snippets & Media Inserter -->
                    <div class="flex items-center gap-2">
                        <div class="relative" x-data="{ openSnippets: false }">
                            <button type="button" @click="openSnippets = !openSnippets" 
                                    class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center gap-1.5 border border-slate-700 transition">
                                <i data-lucide="plus-circle" class="w-3.5 h-3.5 text-brand-400"></i>
                                <span>Insert Section</span>
                                <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400"></i>
                            </button>
                            <div x-show="openSnippets" x-cloak @click.outside="openSnippets = false" 
                                 class="absolute right-0 mt-1 w-56 rounded-xl bg-slate-800 text-slate-200 border border-slate-700 shadow-xl py-1 text-xs z-30">
                                <button type="button" @click="insertSnippet('faq'); openSnippets = false" class="w-full text-left px-3 py-2 hover:bg-slate-700 flex items-center gap-2">
                                    <i data-lucide="help-circle" class="w-3.5 h-3.5 text-brand-400"></i> FAQ Accordion Item
                                </button>
                                <button type="button" @click="insertSnippet('table'); openSnippets = false" class="w-full text-left px-3 py-2 hover:bg-slate-700 flex items-center gap-2">
                                    <i data-lucide="table" class="w-3.5 h-3.5 text-emerald-400"></i> Specifications Table
                                </button>
                                <button type="button" @click="insertSnippet('callout'); openSnippets = false" class="w-full text-left px-3 py-2 hover:bg-slate-700 flex items-center gap-2">
                                    <i data-lucide="shield" class="w-3.5 h-3.5 text-amber-400"></i> Highlight Callout Box
                                </button>
                                <button type="button" @click="insertSnippet('grid3'); openSnippets = false" class="w-full text-left px-3 py-2 hover:bg-slate-700 flex items-center gap-2">
                                    <i data-lucide="grid" class="w-3.5 h-3.5 text-sky-400"></i> 3-Column Engineering Grid
                                </button>
                            </div>
                        </div>

                        <button type="button" onclick="chooseMediaForPost()" 
                                class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center gap-1.5 border border-slate-700 transition">
                            <i data-lucide="image-plus" class="w-3.5 h-3.5 text-brand-400"></i>
                            <span>Add Media</span>
                        </button>
                    </div>
                </div>

                <!-- Hidden Input Bound to Form Submission & Yoast SEO -->
                <textarea name="content" id="postContentInput" class="hidden" x-model="content">{{ old('content', $post->content ?? '') }}</textarea>

                <!-- TAB 1: Visual WYSIWYG Editor (Zero Tag Stripping, Full Class Preservation) -->
                <div x-show="activeTab === 'visual'" class="bg-white">
                    <div class="p-2.5 bg-slate-100 border-b border-slate-200 flex flex-wrap items-center gap-1.5 text-xs text-slate-700">
                        <button type="button" @click="execCommand('bold')" class="p-1.5 hover:bg-slate-200 rounded font-bold" title="Bold"><b>B</b></button>
                        <button type="button" @click="execCommand('italic')" class="p-1.5 hover:bg-slate-200 rounded italic" title="Italic"><i>I</i></button>
                        <button type="button" @click="execCommand('underline')" class="p-1.5 hover:bg-slate-200 rounded underline" title="Underline"><u>U</u></button>
                        <button type="button" @click="execCommand('strikeThrough')" class="p-1.5 hover:bg-slate-200 rounded line-through text-slate-600" title="Strikethrough">S</button>
                        <span class="text-slate-300">|</span>
                        <button type="button" @click="execFormat('h2')" class="px-2 py-1 hover:bg-slate-200 rounded font-bold text-xs" title="Heading 2">H2</button>
                        <button type="button" @click="execFormat('h3')" class="px-2 py-1 hover:bg-slate-200 rounded font-bold text-xs" title="Heading 3">H3</button>
                        <button type="button" @click="execFormat('h4')" class="px-2 py-1 hover:bg-slate-200 rounded font-bold text-xs" title="Heading 4">H4</button>
                        <button type="button" @click="execFormat('p')" class="px-2 py-1 hover:bg-slate-200 rounded text-xs" title="Paragraph">P</button>
                        <span class="text-slate-300">|</span>
                        <button type="button" @click="execCommand('insertUnorderedList')" class="p-1.5 hover:bg-slate-200 rounded" title="Bullet List">&bull; List</button>
                        <button type="button" @click="execCommand('insertOrderedList')" class="p-1.5 hover:bg-slate-200 rounded" title="Numbered List">1. List</button>
                        <button type="button" @click="execFormat('blockquote')" class="px-2 py-1 hover:bg-slate-200 rounded font-serif italic text-xs" title="Quote">&ldquo; Quote</button>
                        <span class="text-slate-300">|</span>
                        <button type="button" @click="execPromptLink()" class="px-2 py-1 hover:bg-slate-200 rounded text-xs" title="Insert Link">🔗 Link</button>
                        <button type="button" @click="execCommand('removeFormat')" class="px-2 py-1 hover:bg-slate-200 rounded text-xs text-slate-500" title="Clear Formatting">Clear</button>
                    </div>

                    <div id="visualEditorContainer" contenteditable="true" @input="syncFromVisual()" 
                         class="p-6 min-h-[460px] focus:outline-none prose prose-slate max-w-none text-slate-800 text-sm sm:text-base leading-relaxed"
                         >{!! old('content', $post->content ?? '') !!}</div>
                </div>

                <!-- TAB 2: HTML Source Code View (Monospace, Indented, 100% Preservation) -->
                <div x-show="activeTab === 'code'" class="p-4 bg-slate-950 font-mono text-xs">
                    <div class="flex items-center justify-between text-[11px] text-slate-400 mb-2 pb-2 border-b border-slate-800">
                        <span>Raw HTML editor &bull; Preserves all custom classes, tables, grids, and formatting.</span>
                        <span x-text="content.length + ' characters'"></span>
                    </div>
                    <textarea id="postCodeTextarea" x-model="content" @input="syncFromCode()" rows="24"
                              class="w-full bg-slate-900 text-emerald-300 p-4 rounded-xl border border-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500 font-mono text-xs leading-relaxed">{{ old('content', $post->content ?? '') }}</textarea>
                </div>

                <!-- TAB 3: Full Frontend Live Preview -->
                <div x-show="activeTab === 'preview'" class="p-8 bg-slate-50 min-h-[460px]">
                    <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm max-w-4xl mx-auto">
                        <div class="mb-4 pb-3 border-b border-slate-100 flex items-center justify-between text-xs text-slate-400">
                            <span>Frontend Rendering Simulation</span>
                            <span class="font-bold text-emerald-600">Live Rendered View</span>
                        </div>
                        <div class="prose prose-slate max-w-none text-slate-800 text-sm sm:text-base leading-relaxed" x-html="content"></div>
                    </div>
                </div>
            </div>

            <!-- Excerpt Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-2">
                <label for="postExcerpt" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Excerpt</label>
                <p class="text-xs text-slate-400">Write an optional short summary of your post. Used in search results and archive cards.</p>
                <textarea id="postExcerpt" name="excerpt" rows="3" placeholder="Brief summary of the article..."
                          class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
            </div>

            <!-- Yoast SEO Box Component -->
            @include('admin.partials.yoast-seo-box', [
                'model' => $post ?? null,
                'titleInputId' => 'postTitle',
                'slugInputId' => 'postSlug',
                'contentInputId' => 'postContentInput',
                'urlPrefix' => url('/'),
                'type' => 'post'
            ])
        </div>

        <!-- Right Sidebar (1 Col) -->
        <div class="space-y-6">
            <!-- Publish Action Box -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-3 flex items-center justify-between">
                    <span>Publish Settings</span>
                    @if($isEdit)
                        <a href="{{ route('post.show', $post->slug) }}" target="_blank" class="text-xs font-semibold text-brand-600 hover:underline">Preview ↗</a>
                    @endif
                </h3>

                <!-- Status Selector -->
                <div>
                    <label for="postStatus" class="block text-xs font-semibold text-slate-700 mb-1.5">Status</label>
                    <select id="postStatus" name="status" class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                        <option value="draft" {{ old('status', $post->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $post->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="scheduled" {{ old('status', $post->status ?? '') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    </select>
                </div>

                <!-- Published At Date -->
                <div>
                    <label for="publishedAt" class="block text-xs font-semibold text-slate-700 mb-1.5">Publish Date</label>
                    <input type="datetime-local" id="publishedAt" name="published_at" 
                           value="{{ old('published_at', isset($post->published_at) ? $post->published_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                           class="w-full text-xs rounded-xl border border-slate-200 p-2 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                </div>

                <!-- Buttons -->
                <div class="pt-2 flex flex-col gap-2">
                    <button type="submit" 
                            class="w-full py-2.5 px-4 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold shadow-md shadow-brand-500/20 transition flex items-center justify-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>{{ $isEdit ? 'Update Post' : 'Publish / Save Post' }}</span>
                    </button>
                    @if($isEdit)
                        <button type="button" onclick="if(confirm('Are you sure you want to trash this post?')) { document.getElementById('deletePostForm').submit(); }" 
                                class="w-full py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-xl transition">
                            Move to Trash
                        </button>
                    @endif
                </div>
            </div>

            <!-- Categories Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-3" x-data="{ newCatOpen: false, searchCat: '' }">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Categories</h3>
                        @php
                            $selectedCats = old('categories', $selectedCategories ?? []);
                        @endphp
                        @if(count($selectedCats))
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full bg-brand-50 text-brand-700 border border-brand-200">
                                {{ count($selectedCats) }} selected
                            </span>
                        @endif
                    </div>
                    <button type="button" @click="newCatOpen = !newCatOpen" class="text-xs text-brand-600 font-semibold hover:underline">+ Add New</button>
                </div>

                <!-- Search / Filter Categories -->
                <div>
                    <input type="text" x-model="searchCat" placeholder="Filter categories..." 
                           class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <!-- Inline New Category Form -->
                <div x-show="newCatOpen" x-cloak class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                    <input type="text" id="inlineCatName" placeholder="New category name" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200">
                    <button type="button" onclick="createInlineCategory()" class="w-full py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold transition">Add Category</button>
                </div>

                <!-- Category Checkboxes -->
                <div class="max-h-60 overflow-y-auto space-y-2 pr-1" id="categoriesCheckboxList">
                    @forelse($categories as $category)
                        <label class="flex items-center gap-2 text-xs text-slate-700 hover:text-slate-900 cursor-pointer"
                               x-show="!searchCat || '{{ strtolower(addslashes($category->name)) }}'.includes(searchCat.toLowerCase())">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}" {{ in_array($category->id, $selectedCats) ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 h-3.5 w-3.5">
                            <span class="{{ in_array($category->id, $selectedCats) ? 'font-bold text-brand-700' : '' }}">{{ $category->name }}</span>
                            @if(in_array($category->id, $selectedCats))
                                <span class="ml-auto text-[9px] px-1.5 py-0.2 rounded bg-brand-100 text-brand-800 font-bold">Selected</span>
                            @endif
                        </label>
                    @empty
                        <p class="text-xs text-slate-400">No categories found.</p>
                    @endforelse
                </div>
            </div>

            <!-- Tags Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider border-b border-slate-100 pb-2">Tags</h3>
                <p class="text-[11px] text-slate-400">Separate tags with commas.</p>
                <input type="text" id="tagsInput" name="tags_input" value="{{ old('tags_input', $tagsString ?? '') }}" placeholder="technology, web design, ai"
                       class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                
                @if($tags->count())
                    <div class="pt-1">
                        <span class="text-[10px] font-semibold text-slate-400 block mb-1">Choose from popular tags:</span>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($tags->take(10) as $t)
                                <button type="button" onclick="addTagToInput('{{ addslashes($t->name) }}')" class="text-[10px] bg-slate-100 hover:bg-brand-50 hover:text-brand-600 px-2 py-0.5 rounded text-slate-600 transition">
                                    + {{ $t->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Featured Image Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider border-b border-slate-100 pb-2">Featured Image</h3>
                
                <input type="hidden" id="featuredImageInput" name="featured_image" value="{{ old('featured_image', $post->featured_image ?? '') }}">

                <div id="featuredImageContainer" class="space-y-3">
                    @php
                        $featImg = old('featured_image', $post->featured_image ?? null);
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
                        <button type="button" onclick="document.getElementById('featuredImageFileInput').click()" class="flex-1 py-2 px-3 bg-brand-50 hover:bg-brand-100 text-brand-700 text-xs font-semibold rounded-xl transition text-center flex items-center justify-center gap-1.5">
                            <i data-lucide="upload" class="w-3.5 h-3.5 text-brand-600"></i>
                            <span>Upload Image</span>
                        </button>
                        <input type="file" id="featuredImageFileInput" name="featured_image_file" accept="image/*" class="hidden" onchange="uploadFeaturedImageDirectly(this.files[0])">
                        <button type="button" id="removeFeaturedBtn" onclick="removeFeaturedImage()" class="py-2 px-3 text-rose-600 hover:bg-rose-50 text-xs font-semibold rounded-xl transition {{ $featUrl ? '' : 'hidden' }}">
                            Remove
                        </button>
                    </div>
                    <div id="featuredImageUploadStatus" class="hidden text-center pt-1"></div>
                </div>
            </div>
        </div>
    </div>
</form>

@if($isEdit)
    <form id="deletePostForm" action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endif

@push('scripts')
<script>
    // 1. Post Content Editor Component (Visual / Code / Preview)
    function postContentEditor(config) {
        return {
            activeTab: 'visual',
            content: config.initial || '',

            setTab(tab) {
                if (this.activeTab === 'visual') {
                    const vis = document.getElementById('visualEditorContainer');
                    if (vis) {
                        this.content = vis.innerHTML;
                    }
                }
                this.activeTab = tab;
                if (tab === 'visual') {
                    this.$nextTick(() => {
                        const vis = document.getElementById('visualEditorContainer');
                        if (vis) {
                            vis.innerHTML = this.content;
                        }
                    });
                }
            },

            syncFromCode() {
                this.syncToHidden();
            },

            syncFromVisual() {
                const vis = document.getElementById('visualEditorContainer');
                if (vis) {
                    this.content = vis.innerHTML;
                    this.syncToHidden();
                }
            },

            syncToHidden() {
                const hiddenInput = document.getElementById('postContentInput');
                if (hiddenInput) {
                    hiddenInput.value = this.content;
                    hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
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
                const url = prompt('Enter destination link URL:');
                if (url) {
                    document.execCommand('createLink', false, url);
                    this.syncFromVisual();
                }
            },

            insertSnippet(type) {
                let snippet = '';
                if (type === 'faq') {
                    snippet = `\n<div class="border border-slate-200 rounded-xl p-5 bg-white my-4">\n    <h4 class="text-base font-bold text-slate-900">What are the critical engineering standards for generator power in the UAE?</h4>\n    <p class="mt-2 text-sm text-slate-600 leading-relaxed">Generator equipment operating in UAE summer conditions must adhere to ISO 8528-1:2018 prime power ratings with heavy tropical radiators certified for 50°C+ ambient temperatures.</p>\n</div>\n`;
                } else if (type === 'table') {
                    snippet = `\n<div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm my-6">\n    <table class="min-w-full bg-white text-xs">\n        <thead><tr><th class="py-3 px-4 text-left font-bold text-xs uppercase tracking-wider text-slate-700 bg-slate-100 border-b border-slate-200">Specification</th><th class="py-3 px-4 text-left font-bold text-xs uppercase tracking-wider text-slate-700 bg-slate-100 border-b border-slate-200">Standard Value</th><th class="py-3 px-4 text-left font-bold text-xs uppercase tracking-wider text-slate-700 bg-slate-100 border-b border-slate-200">UAE Field Compliance</th></tr></thead>\n        <tbody>\n            <tr class="border-b border-slate-100 hover:bg-slate-50 transition"><td class="py-3 px-4 text-xs font-bold text-slate-900">Ambient Capability</td><td class="py-3 px-4 text-xs text-slate-600">50°C Heavy Tropical Radiator</td><td class="py-3 px-4 text-xs text-slate-600">Meets GCC summer thermal benchmarks</td></tr>\n            <tr class="border-b border-slate-100 hover:bg-slate-50 transition"><td class="py-3 px-4 text-xs font-bold text-slate-900">Alternator Insulation</td><td class="py-3 px-4 text-xs text-slate-600">Class H with Anti-Condensation</td><td class="py-3 px-4 text-xs text-slate-600">Coastal humidity protection</td></tr>\n        </tbody>\n    </table>\n</div>\n`;
                } else if (type === 'callout') {
                    snippet = `\n<div class="p-6 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-slate-900 my-6 space-y-2">\n    <h4 class="text-base font-bold text-slate-950">Technical Field Recommendation</h4>\n    <p class="text-xs sm:text-sm text-slate-800 leading-relaxed font-medium">Always verify dual-stage fuel filtration with coalescing water separation to prevent contaminated fuel issues on remote construction sites.</p>\n</div>\n`;
                } else if (type === 'grid3') {
                    snippet = `\n<div class="grid grid-cols-1 md:grid-cols-3 gap-4 my-6">\n    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">\n        <h5 class="font-bold text-slate-900 text-sm">1. Sizing Audit</h5>\n        <p class="text-xs text-slate-600 leading-relaxed">Exact kVA calculation including inductive motor inrush multipliers.</p>\n    </div>\n    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">\n        <h5 class="font-bold text-slate-900 text-sm">2. Rapid Dispatch</h5>\n        <p class="text-xs text-slate-600 leading-relaxed">Transport, offloading, and certified electrical cabling termination.</p>\n    </div>\n    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">\n        <h5 class="font-bold text-slate-900 text-sm">3. Maintenance SLA</h5>\n        <p class="text-xs text-slate-600 leading-relaxed">250-hour preventative maintenance and 24/7 on-call engineers.</p>\n    </div>\n</div>\n`;
                }
                this.content += snippet;
                this.syncToHidden();
                if (this.activeTab === 'visual') {
                    const vis = document.getElementById('visualEditorContainer');
                    if (vis) vis.innerHTML = this.content;
                }
            }
        };
    }

    // 2. Global media helper to insert into Post Content Editor
    function chooseMediaForPost() {
        openMediaModal(function(item) {
            const imgHtml = `\n<img src="${item.url}" alt="${item.name || 'LifeGuard Data Recovery'}" class="rounded-2xl max-w-full h-auto my-4 shadow-sm" />\n`;
            const codeArea = document.getElementById('postCodeTextarea');
            const vis = document.getElementById('visualEditorContainer');
            const hiddenInput = document.getElementById('postContentInput');

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

    // 3. Featured Image Selection via Media Modal
    function chooseFeaturedImage() {
        openMediaModal(function(item) {
            document.getElementById('featuredImageInput').value = item.file_path || item.url;
            document.getElementById('featuredImgTag').src = item.url;
            document.getElementById('featuredImagePreview').classList.remove('hidden');
            document.getElementById('noFeaturedImageText').classList.add('hidden');
            document.getElementById('removeFeaturedBtn').classList.remove('hidden');

            const ogInput = document.getElementById('ogImageInput');
            if (ogInput && !ogInput.value) {
                ogInput.value = item.url;
            }
        });
    }

    function removeFeaturedImage() {
        document.getElementById('featuredImageInput').value = '';
        document.getElementById('featuredImagePreview').classList.add('hidden');
        document.getElementById('noFeaturedImageText').classList.remove('hidden');
        document.getElementById('removeFeaturedBtn').classList.add('hidden');
    }

    // 4. Automatic Slug Generation on Create
    document.addEventListener('DOMContentLoaded', function () {
        const titleInput = document.getElementById('postTitle');
        const slugInput = document.getElementById('postSlug');
        const slugDisplay = document.getElementById('slugDisplay');

        @if(! $isEdit)
        if (titleInput && slugInput && slugDisplay) {
            titleInput.addEventListener('input', function() {
                if (!slugInput.dataset.manual) {
                    const raw = titleInput.value.toLowerCase()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/[\s_-]+/g, '-')
                        .replace(/^-+|-+$/g, '');
                    slugInput.value = raw;
                    slugDisplay.textContent = raw || 'your-post-slug';
                    slugInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });
        }
        @endif

        if (slugInput && slugDisplay) {
            slugInput.addEventListener('input', function() {
                this.dataset.manual = 'true';
                slugDisplay.textContent = this.value || 'your-post-slug';
            });
        }
    });

    // 5. Inline Category Creation
    function createInlineCategory() {
        const catNameInput = document.getElementById('inlineCatName');
        const name = catNameInput ? catNameInput.value.trim() : '';
        if (!name) return;

        fetch('{{ route("admin.categories.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name: name })
        })
        .then(res => res.json())
        .then(data => {
            location.reload();
        })
        .catch(err => alert('Error creating category'));
    }

    // 6. Tag Quick Add
    function addTagToInput(tagName) {
        const input = document.getElementById('tagsInput');
        if (!input) return;
        let current = input.value.split(',').map(s => s.trim()).filter(Boolean);
        if (!current.includes(tagName)) {
            current.push(tagName);
            input.value = current.join(', ');
        }
    }

    // 7. Direct Featured Image Upload with Automated WebP Conversion
    function uploadFeaturedImageDirectly(file) {
        if (!file) return;
        const statusEl = document.getElementById('featuredImageUploadStatus');
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

    // 8. Visual Editor Paste Image to WebP Handler
    document.addEventListener('DOMContentLoaded', function () {
        const visualEditor = document.getElementById('visualEditorContainer');
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
