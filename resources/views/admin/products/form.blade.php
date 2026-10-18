@php
    $isEdit = isset($product);
    $actionUrl = $isEdit ? route('admin.products.update', $product) : route('admin.products.store');
@endphp

<form action="{{ $actionUrl }}" method="POST" id="productForm" class="space-y-8">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Left Column (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Title & Slug Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label for="productTitle" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Product Title</label>
                        <input type="text" id="productTitle" name="title" required value="{{ old('title', $product->title ?? '') }}" 
                               placeholder="e.g. Cleanroom Diagnostics, Enterprise Lab Recovery"
                               class="w-full px-4 py-3 text-lg font-bold rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 transition">
                    </div>
                    <div>
                        <label for="productBadge" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Badge / Tag</label>
                        <input type="text" id="productBadge" name="badge" value="{{ old('badge', $product->badge ?? '') }}" 
                               placeholder="e.g. Flagship HCM, AI-Native"
                               class="w-full px-3.5 py-3 text-sm font-semibold rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                    </div>
                </div>

                <!-- Slug / Permalink Box -->
                <div class="flex items-center gap-2 text-xs text-slate-500 bg-slate-50 p-2.5 rounded-xl border border-slate-200/60" x-data="{ editingSlug: false }">
                    <span class="font-semibold text-slate-600">Permalink:</span>
                    <span class="text-slate-400">{{ url('/product') }}/</span>
                    <span x-show="!editingSlug" id="slugDisplay" class="font-mono text-purple-600 font-bold">{{ old('slug', $product->slug ?? 'your-product-slug') }}</span>
                    <input type="text" id="productSlug" name="slug" value="{{ old('slug', $product->slug ?? '') }}" x-show="editingSlug"
                           class="px-2 py-0.5 rounded border border-slate-300 font-mono text-xs focus:outline-none focus:border-purple-600 w-64">
                    <button type="button" @click="editingSlug = !editingSlug" class="ml-auto text-xs text-purple-600 font-semibold hover:underline">
                        <span x-text="editingSlug ? 'Done' : 'Edit'"></span>
                    </button>
                </div>

                <!-- Tagline & Category -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="productTagline" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tagline / Subheadline</label>
                        <input type="text" id="productTagline" name="tagline" value="{{ old('tagline', $product->tagline ?? '') }}" 
                               placeholder="e.g. New-Age AI-Native HCM for UAE Enterprises"
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                    </div>
                    <div>
                        <label for="productCategory" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Product Category</label>
                        <input type="text" id="productCategory" name="category" value="{{ old('category', $product->category ?? '') }}" 
                               placeholder="e.g. Human Resources & Payroll, Sales CRM"
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                    </div>
                </div>
            </div>

            <!-- Product Content Editor (Triple-Mode: Visual WYSIWYG / HTML Source / Live Preview) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden" 
                 x-data="productContentEditor({ initial: @js(old('content', $product->content ?? '')) })">
                
                <!-- Toolbar Header -->
                <div class="p-4 bg-slate-900 text-white flex flex-wrap items-center justify-between gap-3 border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-300 flex items-center gap-1.5">
                            <i data-lucide="file-text" class="w-4 h-4 text-purple-400"></i>
                            <span>Product Overview & Architecture</span>
                        </span>
                        
                        <!-- Mode Selector Tabs -->
                        <div class="flex items-center bg-slate-800 p-1 rounded-xl text-xs font-semibold">
                            <button type="button" @click="setTab('visual')" 
                                    :class="activeTab === 'visual' ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-300 hover:text-white'"
                                    class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                <span>Visual WYSIWYG</span>
                            </button>
                            <button type="button" @click="setTab('code')" 
                                    :class="activeTab === 'code' ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-300 hover:text-white'"
                                    class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                                <i data-lucide="code" class="w-3.5 h-3.5"></i>
                                <span>HTML Source</span>
                            </button>
                            <button type="button" @click="setTab('preview')" 
                                    :class="activeTab === 'preview' ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-300 hover:text-white'"
                                    class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                                <i data-lucide="monitor" class="w-3.5 h-3.5"></i>
                                <span>Live Preview</span>
                            </button>
                        </div>
                    </div>

                    <button type="button" onclick="chooseMediaForProduct()" 
                            class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center gap-1.5 border border-slate-700 transition">
                        <i data-lucide="image-plus" class="w-3.5 h-3.5 text-purple-400"></i>
                        <span>Add Media</span>
                    </button>
                </div>

                <!-- Hidden Input Bound to Form Submission & Yoast SEO -->
                <textarea name="content" id="productContentInput" class="hidden" x-model="content">{{ old('content', $product->content ?? '') }}</textarea>

                <!-- TAB 1: Visual WYSIWYG Editor -->
                <div x-show="activeTab === 'visual'" class="bg-white">
                    <div class="p-2.5 bg-slate-100 border-b border-slate-200 flex flex-wrap items-center gap-1.5 text-xs text-slate-700">
                        <button type="button" @click="execCommand('bold')" class="p-1.5 hover:bg-slate-200 rounded font-bold" title="Bold"><b>B</b></button>
                        <button type="button" @click="execCommand('italic')" class="p-1.5 hover:bg-slate-200 rounded italic" title="Italic"><i>I</i></button>
                        <button type="button" @click="execCommand('underline')" class="p-1.5 hover:bg-slate-200 rounded underline" title="Underline"><u>U</u></button>
                        <span class="text-slate-300">|</span>
                        <button type="button" @click="execFormat('h2')" class="px-2 py-1 hover:bg-slate-200 rounded font-bold text-xs" title="Heading 2">H2</button>
                        <button type="button" @click="execFormat('h3')" class="px-2 py-1 hover:bg-slate-200 rounded font-bold text-xs" title="Heading 3">H3</button>
                        <button type="button" @click="execFormat('h4')" class="px-2 py-1 hover:bg-slate-200 rounded font-bold text-xs" title="Heading 4">H4</button>
                        <button type="button" @click="execFormat('p')" class="px-2 py-1 hover:bg-slate-200 rounded text-xs" title="Paragraph">P</button>
                        <span class="text-slate-300">|</span>
                        <button type="button" @click="execCommand('insertUnorderedList')" class="p-1.5 hover:bg-slate-200 rounded" title="Bullet List">&bull; List</button>
                        <button type="button" @click="execCommand('insertOrderedList')" class="p-1.5 hover:bg-slate-200 rounded" title="Numbered List">1. List</button>
                        <span class="text-slate-300">|</span>
                        <button type="button" @click="execPromptLink()" class="px-2 py-1 hover:bg-slate-200 rounded text-xs" title="Insert Link">🔗 Link</button>
                        <button type="button" @click="execCommand('removeFormat')" class="px-2 py-1 hover:bg-slate-200 rounded text-xs text-slate-500" title="Clear Formatting">Clear</button>
                    </div>

                    <div id="productVisualEditorContainer" contenteditable="true" @input="syncFromVisual()" 
                         class="p-6 min-h-[400px] focus:outline-none prose prose-slate max-w-none text-slate-800 text-sm sm:text-base leading-relaxed"
                         >{!! old('content', $product->content ?? '') !!}</div>
                </div>

                <!-- TAB 2: HTML Source Code View -->
                <div x-show="activeTab === 'code'" class="p-4 bg-slate-950 font-mono text-xs">
                    <div class="flex items-center justify-between text-[11px] text-slate-400 mb-2 pb-2 border-b border-slate-800">
                        <span>Raw HTML editor &bull; Preserves all custom classes, tables, grids, and formatting.</span>
                        <span x-text="content.length + ' characters'"></span>
                    </div>
                    <textarea id="productCodeTextarea" x-model="content" @input="syncFromCode()" rows="20"
                              class="w-full bg-slate-900 text-emerald-300 p-4 rounded-xl border border-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-500 font-mono text-xs leading-relaxed">{{ old('content', $product->content ?? '') }}</textarea>
                </div>

                <!-- TAB 3: Full Frontend Live Preview -->
                <div x-show="activeTab === 'preview'" class="p-8 bg-slate-50 min-h-[400px]">
                    <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm max-w-4xl mx-auto">
                        <div class="mb-4 pb-3 border-b border-slate-100 flex items-center justify-between text-xs text-slate-400">
                            <span>Frontend Rendering Simulation</span>
                            <span class="font-bold text-emerald-600">Live Rendered View</span>
                        </div>
                        <div class="prose prose-slate max-w-none text-slate-800 text-sm sm:text-base leading-relaxed" x-html="content"></div>
                    </div>
                </div>
            </div>

            <!-- Short Summary Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-2">
                <label for="productSummary" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Short Summary</label>
                <p class="text-xs text-slate-400">Used on navigation mega menu, cards, and product showcase switcher.</p>
                <textarea id="productSummary" name="summary" rows="3" placeholder="Brief summary of the software product..."
                          class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 transition">{{ old('summary', $product->summary ?? '') }}</textarea>
            </div>

            <!-- Modules & Highlights -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Modules Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-2">
                    <label for="modulesInput" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Product Modules (Format: Title | Description)</label>
                    <p class="text-[11px] text-slate-400">One per line. Example: <code class="bg-slate-100 px-1 py-0.5 rounded">WPS Payroll | Instant SIF file generation</code></p>
                    <textarea id="modulesInput" name="modules_input" rows="6" 
                              placeholder="WPS Payroll & Gratuity | Instant UAE WPS SIF file generation&#10;Smart Attendance | Geofenced mobile check-in&#10;Employee Self Service | Mobile-first leaves & claims"
                              class="w-full px-3.5 py-2.5 text-xs font-mono rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">{{ old('modules_input', $modulesString ?? '') }}</textarea>
                </div>

                <!-- Highlights Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-2">
                    <label for="highlightsInput" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Key Highlights & Stats</label>
                    <p class="text-[11px] text-slate-400">Bullet points for high-converting sales points. One per line.</p>
                    <textarea id="highlightsInput" name="highlights_input" rows="6" 
                              placeholder="100% UAE Labour Law & WPS Compliant&#10;Zero Payroll Calculation Errors&#10;95% Faster Leave Approvals&#10;Bilingual Arabic & English UI"
                              class="w-full px-3.5 py-2.5 text-xs font-mono rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">{{ old('highlights_input', $highlightsString ?? '') }}</textarea>
                </div>
            </div>

            <!-- Yoast SEO Box -->
            @include('admin.partials.yoast-seo-box', [
                'model' => $product ?? null,
                'titleInputId' => 'productTitle',
                'slugInputId' => 'productSlug',
                'contentInputId' => 'productContentInput',
                'urlPrefix' => url('/product'),
                'type' => 'product'
            ])

        </div>

        <!-- Right Sidebar (1 Col) -->
        <div class="space-y-6">

            <!-- Publish Settings Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-3 flex items-center justify-between">
                    <span>Publish Settings</span>
                    @if($isEdit)
                        <a href="{{ route('fleet.show', $product->slug) }}" target="_blank" class="text-xs font-semibold text-purple-600 hover:underline">Preview ↗</a>
                    @endif
                </h3>

                <!-- Status Selector -->
                <div>
                    <label for="productStatus" class="block text-xs font-semibold text-slate-700 mb-1.5">Status</label>
                    <select id="productStatus" name="status" class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-purple-500/20">
                        <option value="published" {{ old('status', $product->status ?? 'published') === 'published' ? 'selected' : '' }}>Published (Active)</option>
                        <option value="draft" {{ old('status', $product->status ?? '') === 'draft' ? 'selected' : '' }}>Draft (Hidden)</option>
                    </select>
                </div>

                <!-- Display Order -->
                <div>
                    <label for="productOrder" class="block text-xs font-semibold text-slate-700 mb-1.5">Display Order</label>
                    <input type="number" id="productOrder" name="order" value="{{ old('order', $product->order ?? 0) }}"
                           class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-purple-500/20">
                    <p class="text-[11px] text-slate-400 mt-1">Lower numbers appear first on product tab switcher.</p>
                </div>

                <!-- Featured Toggle -->
                <div class="pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured ?? true) ? 'checked' : '' }}
                               class="rounded border-slate-300 text-purple-600 focus:ring-purple-500 h-4 w-4">
                        <span class="text-xs font-semibold text-slate-700">Feature on Homepage Switcher</span>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 flex flex-col gap-2 border-t border-slate-100">
                    <button type="submit" 
                            class="w-full py-2.5 px-4 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold shadow-md shadow-purple-500/20 transition flex items-center justify-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>{{ $isEdit ? 'Update Product' : 'Save & Publish Product' }}</span>
                    </button>
                    @if($isEdit)
                        <button type="button" onclick="if(confirm('Are you sure you want to delete this product?')) { document.getElementById('deleteProductForm').submit(); }" 
                                class="w-full py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-xl transition">
                            Delete Product
                        </button>
                    @endif
                </div>
            </div>

            <!-- Demo & Brochure Links -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider border-b border-slate-100 pb-2">CTA & Demo Links</h3>
                
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Demo Request URL</label>
                    <input type="text" name="demo_url" value="{{ old('demo_url', $product->demo_url ?? '/contact') }}"
                           placeholder="/contact or demo link"
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Brochure PDF / Link</label>
                    <input type="text" name="brochure_url" value="{{ old('brochure_url', $product->brochure_url ?? '') }}"
                           placeholder="https://... or PDF path"
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20">
                </div>
            </div>

            <!-- Icon Picker Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-3" x-data="{ selectedIcon: '{{ old('icon', $product->icon ?? 'box') }}' }">
                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider border-b border-slate-100 pb-2">Lucide Icon</h3>
                
                <input type="text" id="iconInput" name="icon" x-model="selectedIcon"
                       placeholder="e.g. users-round, target, layers-3, cpu, box"
                       class="w-full px-3 py-2 text-xs font-mono rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20">

                <div class="grid grid-cols-5 gap-2 pt-1">
                    @php
                        $presetIcons = ['users-round', 'target', 'layers-3', 'cpu', 'box', 'sparkles', 'briefcase', 'bar-chart-2', 'shield-check', 'zap'];
                    @endphp
                    @foreach($presetIcons as $ico)
                        <button type="button" @click="selectedIcon = '{{ $ico }}'"
                                :class="selectedIcon === '{{ $ico }}' ? 'border-purple-600 bg-purple-50 text-purple-700' : 'border-slate-200 hover:border-slate-300 text-slate-600'"
                                class="p-2 border rounded-xl flex items-center justify-center transition" title="{{ $ico }}">
                            <i data-lucide="{{ $ico }}" class="w-4 h-4"></i>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Featured Image / Screenshot Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider border-b border-slate-100 pb-2">Product Mockup / Image</h3>
                <p class="text-[11px] text-slate-400">Image URL or path for software UI preview.</p>
                <input type="text" name="featured_image" value="{{ old('featured_image', $product->featured_image ?? '') }}"
                       placeholder="https://... or image path"
                       class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20">
                @if(isset($product) && $product->featured_image_url)
                    <div class="rounded-xl overflow-hidden border border-slate-200 aspect-video">
                        <img src="{{ $product->featured_image_url }}" alt="{{ $product->title }}" class="w-full h-full object-cover">
                    </div>
                @endif
            </div>

        </div>
    </div>
</form>

@if($isEdit)
    <form id="deleteProductForm" action="{{ route('admin.products.destroy', $product) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endif

@push('scripts')
<script>
    // 1. Product Content Editor Component (Visual / Code / Preview)
    function productContentEditor(config) {
        return {
            activeTab: 'visual',
            content: config.initial || '',

            setTab(tab) {
                if (this.activeTab === 'visual') {
                    const vis = document.getElementById('productVisualEditorContainer');
                    if (vis) {
                        this.content = vis.innerHTML;
                    }
                }
                this.activeTab = tab;
                if (tab === 'visual') {
                    this.$nextTick(() => {
                        const vis = document.getElementById('productVisualEditorContainer');
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
                const vis = document.getElementById('productVisualEditorContainer');
                if (vis) {
                    this.content = vis.innerHTML;
                    this.syncToHidden();
                }
            },

            syncToHidden() {
                const hiddenInput = document.getElementById('productContentInput');
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
            }
        };
    }

    // 2. Media insertion into product editor
    function chooseMediaForProduct() {
        openMediaModal(function(item) {
            const imgHtml = `\n<img src="${item.url}" alt="${item.name || 'LifeGuard Data Recovery'}" class="rounded-2xl max-w-full h-auto my-4 shadow-sm" />\n`;
            const codeArea = document.getElementById('productCodeTextarea');
            const vis = document.getElementById('productVisualEditorContainer');
            const hiddenInput = document.getElementById('productContentInput');

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

    // 3. Slug generator
    document.addEventListener('DOMContentLoaded', function () {
        const titleInput = document.getElementById('productTitle');
        const slugInput = document.getElementById('productSlug');
        const slugDisplay = document.getElementById('slugDisplay');

        @if(! $isEdit)
        if (titleInput && slugInput && slugDisplay) {
            titleInput.addEventListener('input', function() {
                const raw = titleInput.value.toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                slugInput.value = raw;
                slugDisplay.textContent = raw || 'your-product-slug';
                slugInput.dispatchEvent(new Event('input', { bubbles: true }));
            });
        }
        @endif
    });
</script>
@endpush
