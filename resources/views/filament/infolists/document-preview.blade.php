@php
    $src = url('/dokumen/' . $getRecord()->documentID);
@endphp

<div x-data="{ open: false }">
    {{-- Thumbnail kecil --}}
    <img src="{{ $src }}" alt="Dokumen"
         @click="open = true"
         style="height: 48px; width: auto; border-radius: 8px; cursor: zoom-in; object-fit: cover; border: 1px solid rgba(255,255,255,.15);">

    {{-- Overlay besar, dipindah ke body supaya tidak terpotong section --}}
    <template x-teleport="body">
    <div x-show="open" x-cloak x-transition.opacity
         @keydown.escape.window="open = false"
         @click="open = false"
         style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 9999; background: rgba(0,0,0,.8);">

        {{-- Gambar: titik tengahnya dipaku ke tengah layar --}}
        <div @click.stop
             style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); max-width: 90vw; max-height: 85vh;">
            <img src="{{ $src }}" alt="Dokumen"
                 style="display: block; margin: 0; max-width: 1080px; max-height: 70vh; width: auto; height: auto; border-radius: 10px; object-fit: contain;">

            <button type="button" @click="open = false"
                    style="position: absolute; top: -12px; right: -12px; width: 32px; height: 32px; border-radius: 9999px; background: #fff; color: #000; font-weight: 700; cursor: pointer; border: none;">
                ✕
            </button>
        </div>
    </div>
</template>
</div>