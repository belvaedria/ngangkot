@extends('layouts.app_dashboard')

@section('title', 'Edukasi - Ngangkot')

@section('content')
<div class="max-w-5xl mx-auto p-6">
    <div class="bg-white p-6 rounded-lg shadow">
        <h1 class="text-2xl font-black mb-4">Edukasi & Panduan</h1>

        <section class="mb-6">
            <h2 class="text-lg font-bold mb-2">Tips</h2>
            @if(isset($tips) && $tips->count())
                <ul class="space-y-3">
                    @foreach($tips as $tip)
                        <li class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                            <div class="font-bold">{{ $tip->judul ?? 'Untitled' }}</div>
                            <div class="text-sm text-slate-500 mt-1">{!! Str::limit($tip->konten ?? '', 300) !!}</div>
                            <a href="{{ route('edukasi.show', $tip->slug ?? $tip->id) }}" class="text-xs text-blue-600 mt-2 inline-block">Baca selengkapnya &rarr;</a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-sm text-slate-400">Belum ada tips tersedia.</div>
            @endif
        </section>

        <section>
            <h2 class="text-lg font-bold mb-2">FAQ</h2>
            @if(isset($faqs) && $faqs->count())
                <div class="space-y-3">
                    @foreach($faqs as $faq)
                        <details class="p-4 bg-white border border-slate-100 rounded-lg">
                            <summary class="font-bold cursor-pointer">{{ $faq->pertanyaan }}</summary>
                            <div class="text-sm text-slate-500 mt-2">{!! $faq->jawaban !!}</div>
                        </details>
                    @endforeach
                </div>
            @else
                <div class="text-sm text-slate-400">Belum ada FAQ yang tersedia.</div>
            @endif
        </section>
    </div>
</div>
@endsection