<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set Up Google Authenticator - {{ config('app.name', 'LifeGuard Data Recovery') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        brand: { 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1' }
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 font-sans">
    <div class="max-w-xl w-full space-y-6 bg-white p-8 sm:p-10 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100"
         x-data="twoFactorSetup()">
        
        <!-- Header -->
        <div class="text-center">
            <a href="{{ route('home') }}" class="inline-block mb-3">
                <img src="{{ asset('images/logo.png') }}" alt="LifeGuard Data Recovery" class="h-12 w-auto mx-auto object-contain">
            </a>

            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-brand-50 text-brand-600 mb-3 border border-brand-100 shadow-sm">
                <i data-lucide="shield-alert" class="w-7 h-7"></i>
            </div>

            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Set Up Google Authenticator</h2>
            <p class="mt-2 text-xs text-slate-500 leading-relaxed max-w-md mx-auto">
                Two-Factor Authentication is mandatory for all accounts on LifeGuard Data Recovery CMS. Link your Google Authenticator app to secure <strong class="text-slate-800">{{ $user->email }}</strong>.
            </p>
        </div>

        @if(session('success'))
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs text-emerald-800 flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 shrink-0 text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-xl bg-amber-50 border border-amber-200 p-3 text-xs text-amber-800 flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0 text-amber-600"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-xl bg-rose-50 border border-rose-200 p-3.5 text-xs text-rose-800 space-y-1">
                @foreach($errors->all() as $error)
                    <div class="flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 text-rose-600"></i>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Step 1: Scan QR Code -->
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-4">
            <div class="flex items-center gap-2.5">
                <span class="w-6 h-6 rounded-full bg-brand-600 text-white font-bold text-xs flex items-center justify-center shrink-0">1</span>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Scan QR Code in Google Authenticator</h3>
            </div>

            <p class="text-xs text-slate-600 leading-relaxed">
                Open <strong class="text-slate-900">Google Authenticator</strong> (or Microsoft Authenticator / 1Password) on your mobile device, tap the <strong class="text-slate-900">+</strong> button, and scan the QR code below:
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-6 py-2">
                <!-- QR Code Box -->
                <div class="bg-white p-3 rounded-2xl border border-slate-200 shadow-sm shrink-0">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($qrCodeUri) }}"
                         alt="Two-Factor Authentication Setup QR Code"
                         width="176"
                         height="176"
                         class="w-44 h-44 rounded-xl">

                </div>

                <!-- Manual Key Alternative -->
                <div class="space-y-2 text-xs text-slate-600 w-full">
                    <span class="font-semibold text-slate-700 block">Can't scan? Enter setup key manually:</span>
                    <div class="p-3 bg-white rounded-xl border border-slate-200 font-mono font-bold text-slate-800 text-sm tracking-wider select-all break-all">
                        {{ $formattedSecret }}
                    </div>
                    <button type="button" @click="copySecret('{{ $secret }}')" class="px-3 py-1.5 rounded-lg bg-slate-200/80 hover:bg-slate-300 text-slate-700 font-semibold text-xs inline-flex items-center gap-1.5 transition">
                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                        <span x-text="secretCopied ? 'Copied to Clipboard!' : 'Copy Secret Key'"></span>
                    </button>
                    <p class="text-[11px] text-slate-400">Account: LifeGuard Data Recovery Dubai ({{ $user->email }}) • Type: Time-based</p>
                </div>
            </div>
        </div>

        <!-- Step 2: Emergency Recovery Codes -->
        <div class="p-5 rounded-2xl bg-amber-50/60 border border-amber-200/80 space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-full bg-amber-600 text-white font-bold text-xs flex items-center justify-center shrink-0">2</span>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900">Emergency Recovery Codes</h3>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="copyCodes()" class="px-2.5 py-1 bg-white hover:bg-amber-100/80 border border-amber-200 rounded-lg text-amber-800 text-[11px] font-semibold transition inline-flex items-center gap-1">
                        <i data-lucide="copy" class="w-3 h-3"></i>
                        <span x-text="codesCopied ? 'Copied!' : 'Copy All'"></span>
                    </button>
                    <button type="button" @click="downloadCodes()" class="px-2.5 py-1 bg-white hover:bg-amber-100/80 border border-amber-200 rounded-lg text-amber-800 text-[11px] font-semibold transition inline-flex items-center gap-1">
                        <i data-lucide="download" class="w-3 h-3"></i>
                        <span>Download (.txt)</span>
                    </button>
                </div>
            </div>

            <p class="text-xs text-amber-800 leading-relaxed">
                Save these backup codes securely. If you lose your phone, each code can be used once to sign in:
            </p>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1">
                @foreach($recoveryCodes as $rc)
                    <div class="bg-white/90 border border-amber-200/80 rounded-lg px-2.5 py-1.5 text-center font-mono font-bold text-xs text-slate-800 select-all shadow-xs">
                        {{ $rc }}
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Step 3: Enter Confirmation Code -->
        <form action="{{ route('login.two-factor.setup') }}" method="POST" class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-4">
            @csrf

            <div class="flex items-center gap-2.5">
                <span class="w-6 h-6 rounded-full bg-brand-600 text-white font-bold text-xs flex items-center justify-center shrink-0">3</span>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Verify & Activate Two-Factor Authentication</h3>
            </div>

            <div>
                <label for="code" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Enter the current 6-digit code shown in Google Authenticator:
                </label>
                <div class="relative">
                    <input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" maxlength="7" required autofocus
                           class="w-full px-4 py-3 text-center font-mono font-black text-2xl tracking-[0.35em] rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition shadow-sm placeholder:tracking-normal placeholder:font-normal placeholder:text-sm"
                           placeholder="123 456">
                </div>
            </div>

            <div>
                <button type="submit"
                        class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-xl text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 shadow-md shadow-brand-500/30 transition">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span>Activate Two-Factor Authentication</span>
                </button>
            </div>
        </form>

        <form action="{{ route('login.two-factor.cancel') }}" method="POST" class="text-center pt-2">
            @csrf
            <button type="submit" class="text-slate-400 hover:text-slate-600 text-xs inline-flex items-center gap-1 transition">
                <span>← Cancel and return to sign in</span>
            </button>
        </form>

    </div>

    <script>
        function twoFactorSetup() {
            return {
                secretCopied: false,
                codesCopied: false,
                codes: {!! json_encode($recoveryCodes) !!},
                copySecret(text) {
                    navigator.clipboard.writeText(text);
                    this.secretCopied = true;
                    setTimeout(() => this.secretCopied = false, 3000);
                },
                copyCodes() {
                    const text = this.codes.join('\n');
                    navigator.clipboard.writeText(text);
                    this.codesCopied = true;
                    setTimeout(() => this.codesCopied = false, 3000);
                },
                downloadCodes() {
                    const content = "LIFEGUARD DATA RECOVERY CMS - EMERGENCY 2FA RECOVERY CODES\n" +
                                    "User: {{ $user->email }}\n" +
                                    "Generated: " + new Date().toISOString() + "\n\n" +
                                    this.codes.join("\n") + "\n\n" +
                                    "Keep these codes secret. Each code can only be used once.\n";
                    const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = 'lifeguard-2fa-backup-codes.txt';
                    link.click();
                    URL.revokeObjectURL(url);
                }
            };
        }

        if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
            lucide.createIcons();
        } else if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    </script>
</body>
</html>
