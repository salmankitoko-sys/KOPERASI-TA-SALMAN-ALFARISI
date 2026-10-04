@props(['label', 'value', 'note' => null, 'tone' => 'amber'])
@php $classes = $tone === 'rose' ? 'border-rose-200 bg-rose-50' : 'border-amber-200 bg-amber-50'; @endphp
<article {{ $attributes->merge(['class' => "rounded-xl border p-5 $classes"]) }}><p class="text-sm text-stone-600">{{ $label }}</p><p class="mt-2 text-2xl font-bold text-stone-950">{{ $value }}</p>@if($note)<p class="mt-1 text-xs text-stone-500">{{ $note }}</p>@endif</article>
