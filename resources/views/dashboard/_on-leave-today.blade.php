{{-- "On leave today" ticker. Expects $employeesOnLeave and $adminsOnLeave collections. --}}
@php
    $onLeave = collect($employeesOnLeave)->concat($adminsOnLeave);
    $count   = $onLeave->count();
    $duration = max(18, $count * 6); // used only if the list overflows and starts scrolling
@endphp

<div class="onleave mt-6">
    <div class="onleave-head">
        <span class="onleave-dot"></span>
        <span class="onleave-title">On Leave Today</span>
        <span class="onleave-count">{{ $count }}</span>
    </div>

    @if ($count === 0)
        <div class="onleave-empty">🎉 Nobody is on leave today — everyone’s in!</div>
    @else
        <div class="onleave-viewport" data-onleave>
            {{-- rendered once; JS clones only when it overflows, for a seamless scroll --}}
            <div class="onleave-track" style="--onleave-duration: {{ $duration }}s">
                @foreach ($onLeave as $leave)
                    @php $role = $leave->employee?->role; @endphp
                    <div class="onleave-pill">
                        @if ($leave->employee?->avatar_url)
                            <img src="{{ $leave->employee->avatar_url }}" alt="{{ $leave->employee?->name }}" class="onleave-avatar onleave-avatar-img">
                        @else
                            <span class="onleave-avatar {{ $role === 'admin' ? 'is-admin' : 'is-emp' }}">
                                {{ strtoupper(mb_substr($leave->employee?->name ?? '?', 0, 1)) }}
                            </span>
                        @endif
                        <span class="onleave-name">{{ $leave->employee?->name }}</span>
                        <span class="onleave-role {{ $role === 'admin' ? 'is-admin' : 'is-emp' }}">{{ ucfirst($role) }}</span>
                        <span class="onleave-dates">{{ $leave->from_date->format('j M') }} – {{ $leave->to_date->format('j M') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<style>
    .onleave {
        background: linear-gradient(135deg, #eff6ff 0%, #ffffff 60%);
        border: 1px solid #e5edfb;
        border-radius: 1rem;
        padding: 0.85rem 0 0.95rem;
        box-shadow: 0 4px 14px -8px rgba(37,99,235,.35);
        overflow: hidden;
    }
    .onleave-head { display: flex; align-items: center; gap: 0.5rem; padding: 0 1.1rem 0.7rem; }
    .onleave-dot {
        width: 0.55rem; height: 0.55rem; border-radius: 9999px; background: #22c55e;
        box-shadow: 0 0 0 3px rgba(34,197,94,.2);
        animation: onleave-pulse 1.6s ease-in-out infinite;
    }
    @keyframes onleave-pulse { 0%,100% { opacity: 1; } 50% { opacity: .35; } }
    .onleave-title { font-weight: 600; color: #0f172a; font-size: 0.95rem; }
    .onleave-count {
        margin-left: 0.15rem; min-width: 1.35rem; text-align: center;
        background: #2563eb; color: #fff; font-size: 0.72rem; font-weight: 700;
        border-radius: 9999px; padding: 0.05rem 0.45rem;
    }
    .onleave-empty { padding: 0.35rem 1.1rem 0.1rem; color: #475569; font-size: 0.9rem; }

    .onleave-viewport { overflow: hidden; padding: 0 1.1rem; }
    /* fade edges + scrolling only when the list actually overflows */
    .onleave-viewport.is-scrolling { padding: 0; -webkit-mask-image: linear-gradient(90deg, transparent, #000 4%, #000 96%, transparent); mask-image: linear-gradient(90deg, transparent, #000 4%, #000 96%, transparent); }
    .onleave-track { display: inline-flex; white-space: nowrap; }
    .onleave-track.is-scrolling { animation: onleave-scroll var(--onleave-duration) linear infinite; will-change: transform; }
    .onleave-viewport.is-scrolling:hover .onleave-track { animation-play-state: paused; }
    @keyframes onleave-scroll { from { transform: translateX(0); } to { transform: translateX(-50%); } }

    .onleave-pill {
        display: inline-flex; align-items: center; gap: 0.55rem;
        background: #fff; border: 1px solid #e5e7eb; border-radius: 9999px;
        padding: 0.4rem 0.9rem 0.4rem 0.4rem; margin-right: 0.7rem;
        box-shadow: 0 1px 2px rgba(16,24,40,.05);
    }
    .onleave-track.is-scrolling .onleave-pill { margin: 0 0.4rem; }
    .onleave-avatar {
        width: 1.85rem; height: 1.85rem; border-radius: 9999px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 0.8rem; font-weight: 700; color: #fff; flex: none;
    }
    .onleave-avatar-img { object-fit: cover; box-shadow: 0 0 0 2px #fff; }
    .onleave-avatar.is-emp { background: linear-gradient(135deg,#3b82f6,#2563eb); }
    .onleave-avatar.is-admin { background: linear-gradient(135deg,#8b5cf6,#6d28d9); }
    .onleave-name { font-weight: 600; color: #1f2937; font-size: 0.88rem; }
    .onleave-role {
        font-size: 0.66rem; font-weight: 700; text-transform: uppercase; letter-spacing: .03em;
        border-radius: 9999px; padding: 0.08rem 0.45rem;
    }
    .onleave-role.is-emp { background: #dbeafe; color: #1d4ed8; }
    .onleave-role.is-admin { background: #ede9fe; color: #6d28d9; }
    .onleave-dates { font-size: 0.78rem; color: #6b7280; }

    @media (prefers-reduced-motion: reduce) {
        .onleave-track.is-scrolling { animation: none; }
    }
</style>

<script>
    (function () {
        function initOnLeave() {
            document.querySelectorAll('[data-onleave]').forEach(function (vp) {
                if (vp.dataset.onleaveReady) return;
                var track = vp.querySelector('.onleave-track');
                if (!track) return;
                // Only scroll (and duplicate for a seamless loop) if the pills overflow the row.
                if (track.scrollWidth > vp.clientWidth + 4) {
                    track.innerHTML += track.innerHTML; // duplicate the set once
                    vp.classList.add('is-scrolling');
                    track.classList.add('is-scrolling');
                }
                vp.dataset.onleaveReady = '1';
            });
        }
        if (document.readyState !== 'loading') initOnLeave();
        else document.addEventListener('DOMContentLoaded', initOnLeave);
    })();
</script>
