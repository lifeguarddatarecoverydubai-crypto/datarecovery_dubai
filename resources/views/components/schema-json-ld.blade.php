@props(['schema' => null, 'data' => null])

@php
    $payload = $data ?? $schema;
@endphp

@if(!empty($payload))
    @if(is_array($payload))
        <script type="application/ld+json">
        {!! json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @else
        @php
            $trimmed = trim((string) $payload);
        @endphp
        @php
            $customSchema = json_decode($trimmed, true);
        @endphp
        @if(is_array($customSchema))
            <script type="application/ld+json">
            {!! json_encode($customSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
            </script>
        @endif
    @endif
@endif
