@php
    $serviceCardImages = [
        'hard-drive-recovery' => 'media-bundle/media/services/hard-drive-recovery.webp',
        'ssd-recovery-dubai' => 'media-bundle/media/services/ssd-data-recovery-dubai.webp',
        'nvme-ssd-recovery-dubai' => 'media-bundle/media/2026/01/emergency-ssd-recovery-serv.webp',
        'raid-recovery-dubai' => 'media-bundle/media/services/raid-data-recovery-dubai.webp',
        'nas-recovery-dubai' => 'media-bundle/media/services/ts-412-hardware-specs-removebg-preview.webp',
        'cctv-footage-recovery-dubai' => 'media-bundle/media/services/cctv-footage-recovery-dubai.webp',
        'usb-data-recovery-dubai' => 'media-bundle/media/2022/09/usb-flash-drive-data-recovery-in-sharjah-768x768.webp',
        'iphone-data-recovery-dubai' => 'media-bundle/media/services/iphone-data-recovery.webp',
        'ransomware-data-recovery-dubai' => 'media-bundle/media/services/ransomware.webp',
    ];
    $serviceCardImage = isset($serviceCardImages[$service->slug])
        ? asset($serviceCardImages[$service->slug])
        : $service->featured_image_url;
@endphp

@if($serviceCardImage)
    <div class="relative aspect-[16/9] overflow-hidden bg-slate-100">
        <img src="{{ $serviceCardImage }}"
             alt="{{ $service->title }} service in Dubai"
             width="800"
             height="450"
             loading="lazy"
             decoding="async"
             class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-950/20 via-transparent to-transparent"></div>
    </div>
@endif
