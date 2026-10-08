@extends('layouts.customer')

@section('title', 'Manage card PIN')
@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('cards') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--surface-raised)]" aria-label="Back">
            <x-icon name="arrow-left" class="w-5 h-5" />
        </a>
        <div>
            <h1 class="text-xl font-bold text-[var(--text)]">Manage PIN</h1>
            <p class="text-sm text-[var(--text-muted)]">Card ****{{ substr($card->card_number_masked, -4) }}</p>
        </div>
    </div>

    <div class="bg-white border border-[var(--line)] rounded-2xl p-6">
        @if($hasPin)
            <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-4">Change PIN</h2>
            <form method="POST" action="{{ route('card.pin.change', $card) }}" class="space-y-4" id="form-change">
                @csrf
                @method('PUT')
                <input type="hidden" name="current_pin" id="input-current">
                <input type="hidden" name="pin" id="input-new">
                <input type="hidden" name="pin_confirmation" id="input-confirm">

                <div>
                    <label class="block text-sm font-semibold text-[var(--text)] mb-1.5">Current PIN</label>
                    <div id="dots-current" class="flex justify-center gap-2 mb-3"></div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[var(--text)] mb-1.5">New PIN</label>
                    <div id="dots-new" class="flex justify-center gap-2 mb-3"></div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[var(--text)] mb-1.5">Confirm new PIN</label>
                    <div id="dots-confirm" class="flex justify-center gap-2 mb-3"></div>
                </div>
                <button type="submit" id="btn-change" class="w-full py-3 btn-primary" disabled>Change PIN</button>
            </form>
        @else
            <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-4">Set PIN</h2>
            <form method="POST" action="{{ route('card.pin.set', $card) }}" class="space-y-4" id="form-set">
                @csrf
                <input type="hidden" name="pin" id="input-pin">
                <input type="hidden" name="pin_confirmation" id="input-confirm-pin">

                <div>
                    <label class="block text-sm font-semibold text-[var(--text)] mb-1.5">New PIN</label>
                    <div id="dots-pin" class="flex justify-center gap-2 mb-3"></div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[var(--text)] mb-1.5">Confirm PIN</label>
                    <div id="dots-confirm-pin" class="flex justify-center gap-2 mb-3"></div>
                </div>
                <button type="submit" id="btn-set" class="w-full py-3 btn-primary" disabled>Set PIN</button>
            </form>
        @endif

        <div id="keypad" class="grid grid-cols-3 gap-2 mt-6"></div>
    </div>

    <div class="bg-white border border-[var(--line)] rounded-2xl p-6">
        <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-4">Report a problem</h2>
        <form method="POST" action="{{ route('messages.send') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="subject" value="Card problem: {{ $card->card_type }} ****{{ substr($card->card_number_masked, -4) }}">
            <div>
                <label class="block text-sm font-semibold text-[var(--text)] mb-1.5">What's wrong with your card?</label>
                <textarea name="body" rows="3" required placeholder="Describe the issue..." class="input-field"></textarea>
            </div>
            <button type="submit" class="w-full py-3 btn-primary">Send to support</button>
        </form>
    </div>

    @if(session('status'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 font-medium">
            {{ session('status') }}
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

<script>
(function() {
    var hasPin = @json($hasPin);
    var pin = '';
    var step = hasPin ? 0 : 0;
    var steps = hasPin ? ['current', 'new', 'confirm'] : ['pin', 'confirm'];
    var values = {};

    function renderDots(containerId) {
        var container = document.getElementById(containerId);
        if (!container) return;
        container.innerHTML = '';
        for (var i = 0; i < 4; i++) {
            var dot = document.createElement('div');
            dot.className = 'w-10 h-10 border-2 border-gray-300 rounded-xl flex items-center justify-center text-xl font-bold text-gray-700';
            dot.textContent = i < pin.length ? '•' : '•';
            dot.style.opacity = i < pin.length ? '1' : '0.3';
            container.appendChild(dot);
        }
    }

    function renderKeypad() {
        var container = document.getElementById('keypad');
        if (!container) return;
        container.innerHTML = '';
        var keys = ['1','2','3','4','5','6','7','8','9'];
        keys.forEach(function(k) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'py-4 text-2xl font-bold text-[var(--text)] bg-[var(--surface-raised)] border border-[var(--line)] rounded-xl hover:bg-[var(--line)]/30 transition-colors';
            btn.textContent = k;
            btn.onclick = function() { appendDigit(k); };
            container.appendChild(btn);
        });
        var zeroBtn = document.createElement('button');
        zeroBtn.type = 'button';
        zeroBtn.className = 'py-4 text-2xl font-bold text-[var(--text)] bg-[var(--surface-raised)] border border-[var(--line)] rounded-xl hover:bg-[var(--line)]/30 transition-colors';
        zeroBtn.textContent = '0';
        zeroBtn.onclick = function() { appendDigit('0'); };
        container.appendChild(zeroBtn);
        var clearBtn = document.createElement('button');
        clearBtn.type = 'button';
        clearBtn.className = 'py-4 text-lg font-bold text-red-600 bg-[var(--surface-raised)] border border-[var(--line)] rounded-xl hover:bg-red-50 transition-colors';
        clearBtn.textContent = '⌫';
        clearBtn.onclick = function() {
            pin = pin.slice(0, -1);
            updateDisplay();
        };
        container.appendChild(clearBtn);
    }

    function appendDigit(digit) {
        if (pin.length < 4) {
            pin += digit;
            updateDisplay();
        }
    }

    function getCurrentStepId() {
        var stepName = steps[Math.min(step, steps.length - 1)];
        if (hasPin) {
            return 'dots-' + stepName;
        } else {
            if (stepName === 'pin') return 'dots-pin';
            return 'dots-confirm-pin';
        }
    }

    function updateDisplay() {
        renderDots(getCurrentStepId());

        if (pin.length === 4) {
            var currentStepName = steps[Math.min(step, steps.length - 1)];
            values[currentStepName] = pin;

            if (step < steps.length - 1) {
                step++;
                pin = '';
                var nextEl = document.getElementById('dots-' + (hasPin ? steps[step] : (steps[step] === 'confirm' ? 'confirm-pin' : steps[step])));
                renderDots(nextEl ? nextEl.id : getCurrentStepId());
            } else if (step === steps.length - 1) {
                if (hasPin) {
                    var allMatch = values['current'] === values['new'] && values['confirm'] === values['new'];
                    var btn = document.getElementById('btn-change');
                    if (btn) {
                        btn.disabled = !allMatch;
                        if (allMatch) {
                            document.getElementById('input-current').value = values['current'];
                            document.getElementById('input-new').value = values['new'];
                            document.getElementById('input-confirm').value = values['confirm'];
                        }
                    }
                } else {
                    var btn = document.getElementById('btn-set');
                    if (btn) {
                        btn.disabled = false;
                        document.getElementById('input-pin').value = values['pin'];
                        document.getElementById('input-confirm-pin').value = values['confirm'];
                    }
                }
            }
        }
    }

    renderDots(getCurrentStepId());
    renderKeypad();
})();
</script>
@endsection
