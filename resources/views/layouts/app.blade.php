<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Leave Desk') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Tailwind (CDN, no build step) -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: { sans: ['Figtree', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                        colors: { brand: { 50:'#eff6ff',100:'#dbeafe',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8' } },
                    },
                },
            }
        </script>
        <!-- Alpine (CDN) -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- flatpickr (CDN) — human-readable date pickers -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" crossorigin="anonymous">
        <script defer src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <style>
            /* ---- flatpickr: modern brand-blue theme ---- */
            .flatpickr-calendar {
                width: 20.5rem;
                padding: 0.75rem 0.85rem 0.95rem;
                border: 1px solid #eef1f5;
                border-radius: 1rem;
                box-shadow: 0 20px 45px -12px rgba(16,24,40,.22), 0 4px 12px -4px rgba(16,24,40,.10);
                font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif;
            }
            .flatpickr-calendar::before, .flatpickr-calendar::after { display: none; } /* drop the little arrow */
            .flatpickr-innerContainer, .flatpickr-rContainer,
            .flatpickr-days, .dayContainer { width: 100%; min-width: 0; max-width: none; }
            .dayContainer { padding: 0; }

            /* Header: month + year, centered */
            .flatpickr-months { align-items: center; margin-bottom: 0.35rem; }
            .flatpickr-months .flatpickr-month { height: 2.5rem; color: #0f172a; }
            .flatpickr-current-month {
                font-size: 1.05rem; font-weight: 600; color: #0f172a;
                display: flex; align-items: center; justify-content: center; gap: 0.35rem;
                padding: 0; height: 2.5rem;
            }
            .flatpickr-current-month input.cur-year {
                font-weight: 600; color: #0f172a;
                pointer-events: none;   /* year is read-only; change it via the month arrows */
                cursor: default;
            }
            .flatpickr-current-month .numInputWrapper { width: 5.5ch; }
            .flatpickr-current-month .numInputWrapper:hover { background: transparent; }
            .flatpickr-current-month .cur-year::-webkit-inner-spin-button,
            .flatpickr-current-month .cur-year::-webkit-outer-spin-button { display: none; }
            .numInputWrapper span { display: none; } /* hide the tiny year spinner arrows */

            /* Prev / next arrows -> rounded icon buttons */
            .flatpickr-months .flatpickr-prev-month,
            .flatpickr-months .flatpickr-next-month {
                width: 2rem; height: 2rem; padding: 0;
                display: flex; align-items: center; justify-content: center;
                border-radius: 9999px; color: #475569; transition: all .15s ease;
                top: 0.25rem;
            }
            .flatpickr-months .flatpickr-prev-month svg,
            .flatpickr-months .flatpickr-next-month svg { width: 0.7rem; height: 0.7rem; }
            .flatpickr-months .flatpickr-prev-month:hover,
            .flatpickr-months .flatpickr-next-month:hover { background: #eff6ff; }
            .flatpickr-months .flatpickr-prev-month:hover svg,
            .flatpickr-months .flatpickr-next-month:hover svg { fill: #2563eb; }

            /* Weekday labels */
            .flatpickr-weekdays { margin-bottom: 0.35rem; }
            span.flatpickr-weekday {
                color: #94a3b8; font-weight: 600;
                font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.04em;
            }

            /* Day cells -> circular, roomy, smooth */
            .flatpickr-day {
                max-width: none; height: 2.35rem; line-height: 2.35rem;
                margin: 0.1rem 0; border-radius: 9999px;
                color: #334155; font-weight: 500; border: 2px solid transparent;
                transition: background .15s ease, color .15s ease, transform .1s ease;
            }
            .flatpickr-day:hover, .flatpickr-day:focus {
                background: #eff6ff; color: #1d4ed8;
            }
            .flatpickr-day.today { border-color: #bfdbfe; color: #1d4ed8; }
            .flatpickr-day.today:hover { background: #eff6ff; }
            .flatpickr-day.selected,
            .flatpickr-day.selected:hover,
            .flatpickr-day.startRange, .flatpickr-day.endRange {
                background: #2563eb; border-color: #2563eb; color: #fff;
                box-shadow: 0 6px 14px -4px rgba(37,99,235,.5);
                transform: translateY(-1px);
            }
            .flatpickr-day.flatpickr-disabled,
            .flatpickr-day.prevMonthDay, .flatpickr-day.nextMonthDay { color: #cbd5e1; }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    @include('layouts.flash')
                    {{ $slot }}
                </div>
            </main>
        </div>

        <!-- Date pickers: user sees "17 July, 2025", form submits "2025-07-17" -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (!window.flatpickr) return;
                var pickers = {};
                document.querySelectorAll('.js-datepicker').forEach(function (el) {
                    pickers[el.id] = flatpickr(el, {
                        dateFormat: 'Y-m-d',        // value posted to the server
                        altInput: true,
                        altFormat: 'j F, Y',        // what the user sees, e.g. 17 July, 2025
                        allowInput: false,          // pick from the calendar only — no free-text typing
                        monthSelectorType: 'static', // clean month label + arrows (no bulky native dropdown)
                        // leave-request dates can't be in the past
                        minDate: el.hasAttribute('data-min-today') ? 'today' : null,
                    });
                });

                // Link a from/to pair: "to date" can never be before "from date".
                var fromFp = pickers['from_date'], toFp = pickers['to_date'];
                if (fromFp && toFp) {
                    fromFp.set('onChange', function (dates) {
                        if (!dates.length) return;
                        toFp.set('minDate', dates[0]);
                        // if the chosen "to date" is now earlier than "from date", bump it up
                        if (toFp.selectedDates[0] && toFp.selectedDates[0] < dates[0]) {
                            toFp.setDate(dates[0], true);
                        }
                    });
                    // on edit pages from_date already has a value — seed the limit
                    if (fromFp.selectedDates[0]) {
                        toFp.set('minDate', fromFp.selectedDates[0]);
                    }
                }
            });
        </script>
    </body>
</html>
