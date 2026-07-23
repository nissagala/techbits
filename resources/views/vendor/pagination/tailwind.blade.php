@if ($paginator->hasPages())
<nav aria-label="pagination" style="display:flex;gap:.35rem;align-items:center;flex-wrap:wrap;margin-top:1.5rem;">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span style="padding:.4rem .8rem;border:1px solid var(--border);border-radius:var(--radius);color:var(--text-muted);cursor:default;font-size:.88rem;">‹ Prev</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" style="padding:.4rem .8rem;border:1px solid var(--border);border-radius:var(--radius);color:var(--primary);font-size:.88rem;">‹ Prev</a>
    @endif

    {{-- Page numbers --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span style="padding:.4rem .5rem;font-size:.88rem;color:var(--text-muted);">…</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span style="padding:.4rem .75rem;border:1px solid var(--primary);border-radius:var(--radius);background:var(--primary);color:#fff;font-size:.88rem;font-weight:600;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" style="padding:.4rem .75rem;border:1px solid var(--border);border-radius:var(--radius);color:var(--primary);font-size:.88rem;">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" style="padding:.4rem .8rem;border:1px solid var(--border);border-radius:var(--radius);color:var(--primary);font-size:.88rem;">Next ›</a>
    @else
        <span style="padding:.4rem .8rem;border:1px solid var(--border);border-radius:var(--radius);color:var(--text-muted);cursor:default;font-size:.88rem;">Next ›</span>
    @endif
</nav>
@endif
