@extends('busmetro.layouts.adherent')
@section('title', 'Cotisations')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap');

    .cot-page {
        font-family: 'Sora', sans-serif;
    }

    /* ── Native-like Select ── */
    .select-wrapper {
        position: relative;
        width: 100%;
    }

    .select-wrapper::after {
        content: '';
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-top: 6px solid #6b7280;
        pointer-events: none;
        transition: transform 0.2s ease;
    }

    .native-select {
        width: 100%;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        padding: 13px 42px 13px 14px;
        font-family: 'Sora', sans-serif;
        font-size: 14px;
        font-weight: 500;
        color: #111827;
        background: #f9fafb;
        border: 1.5px solid #e5e7eb;
        border-radius: 14px;
        outline: none;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
        line-height: 1.4;
        -webkit-tap-highlight-color: transparent;
    }

    .native-select:focus {
        border-color: #2563eb;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .native-select:active {
        background: #eff6ff;
    }

    .native-select option {
        font-size: 14px;
        font-weight: 500;
        padding: 10px;
        color: #111827;
    }

    /* ── Inputs ── */
    .cot-input {
        width: 100%;
        padding: 13px 14px;
        font-family: 'Sora', sans-serif;
        font-size: 14px;
        font-weight: 500;
        color: #111827;
        background: #f9fafb;
        border: 1.5px solid #e5e7eb;
        border-radius: 14px;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        -webkit-appearance: none;
    }

    .cot-input::placeholder {
        color: #9ca3af;
        font-weight: 400;
    }

    .cot-input:focus {
        border-color: #2563eb;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    /* ── Card ── */
    .cot-card {
        background: #fff;
        border-radius: 20px;
        border: 1.5px solid #f0f0f0;
        padding: 20px;
        box-shadow: 0 1px 8px rgba(0,0,0,0.04);
    }

    /* ── Label ── */
    .cot-label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 7px;
    }

    .cot-label span.dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #2563eb;
        display: inline-block;
    }

    /* ── Button ── */
    .cot-btn {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
        color: #fff;
        font-family: 'Sora', sans-serif;
        font-size: 14px;
        font-weight: 600;
        border: none;
        border-radius: 14px;
        cursor: pointer;
        letter-spacing: 0.01em;
        transition: transform 0.15s, box-shadow 0.15s, filter 0.15s;
        box-shadow: 0 4px 16px rgba(37, 99, 235, 0.3);
        -webkit-tap-highlight-color: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .cot-btn:active {
        transform: scale(0.98);
        filter: brightness(0.95);
    }

    .cot-btn svg {
        width: 16px;
        height: 16px;
    }

    /* ── Alerts ── */
    .alert {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 13px;
        font-weight: 500;
        padding: 13px 15px;
        border-radius: 14px;
        line-height: 1.5;
    }

    .alert-success { background: #f0fdf4; border: 1.5px solid #bbf7d0; color: #15803d; }
    .alert-error   { background: #fef2f2; border: 1.5px solid #fecaca; color: #b91c1c; }
    .alert-info    { background: #eff6ff; border: 1.5px solid #bfdbfe; color: #1d4ed8; }

    .alert-icon {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
        margin-top: 1px;
    }

    /* ── History item ── */
    .hist-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .hist-item:last-child { border-bottom: none; }

    .hist-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 10px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 20px;
        margin-top: 4px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .badge-valid  { background: #dcfce7; color: #16a34a; }
    .badge-wait   { background: #fef9c3; color: #ca8a04; }
    .badge-reject { background: #fee2e2; color: #dc2626; }

    .hist-code {
        display: inline-block;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        background: #eff6ff;
        color: #2563eb;
        border-radius: 6px;
        letter-spacing: 0.03em;
        margin-bottom: 4px;
    }

    /* ── Section title ── */
    .section-title {
        font-size: 13px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-title::before {
        content: '';
        display: block;
        width: 3px;
        height: 16px;
        background: #2563eb;
        border-radius: 2px;
    }

    /* ── Page title ── */
    .page-title {
        font-size: 20px;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.02em;
    }

    .page-subtitle {
        font-size: 12px;
        color: #9ca3af;
        font-weight: 400;
        margin-top: 2px;
    }
</style>

<div class="cot-page space-y-5">

    <!-- Header -->
    <div>
        <h2 class="page-title">Mes cotisations</h2>
        <p class="page-subtitle">Gérez vos paiements et consultez votre historique</p>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="alert alert-success">
        <svg class="alert-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-error">
        <svg class="alert-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif
    @if(session('info'))
    <div class="alert alert-info">
        <svg class="alert-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('info') }}
    </div>
    @endif

    <!-- Formulaire de paiement -->
    <div class="cot-card">
        <div class="section-title">Payer une cotisation</div>

        <form action="{{ route('busmetro.adherent.cotisations.payer') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Select type de cotisation -->
            <div>
                <label class="cot-label">
                    <span class="dot"></span>
                    Type de cotisation
                </label>
                <div class="select-wrapper">
                    <select name="type_cotisation_id" required class="native-select">
                        <option value="">Choisir un type…</option>
                        @forelse($typesCotisation as $tc)
                        <option value="{{ $tc->id }}">
                            {{ $tc->code }} — {{ $tc->nom }} · {{ number_format($tc->montant_minimum) }} F min.
                        </option>
                        @empty
                        <option disabled>Aucun type disponible</option>
                        @endforelse
                    </select>
                </div>
            </div>

            <!-- Montant -->
            <div>
                <label class="cot-label">
                    <span class="dot"></span>
                    Montant (F CFA)
                </label>
                <input
                    type="number"
                    name="montant"
                    placeholder="Ex : 5 000"
                    required
                    min="1"
                    class="cot-input"
                >
            </div>

            <!-- Téléphone -->
            <div>
                <label class="cot-label">
                    <span class="dot"></span>
                    Numéro Mobile Money
                </label>
                <input
                    type="tel"
                    name="telephone"
                    placeholder="Ex : 6XX XXX XXX"
                    required
                    value="{{ auth()->guard('adherent')->user()->telephone }}"
                    class="cot-input"
                >
            </div>

            <button type="submit" class="cot-btn">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Payer par Mobile Money
            </button>
        </form>
    </div>

    <!-- Historique -->
    <div class="cot-card">
        <div class="section-title">Historique</div>

        <div>
            @forelse($cotisations as $cot)
            <div class="hist-item">
                <div>
                    <span class="hist-code">{{ $cot->typeCotisation->code ?? '—' }}</span>
                    <p style="font-size:12px; font-weight:500; color:#374151; margin:0;">
                        {{ $cot->typeCotisation->nom ?? '' }}
                    </p>
                    <p style="font-size:11px; color:#9ca3af; margin:3px 0 0;">
                        {{ $cot->date_cotisation->format('d/m/Y') }}
                    </p>
                </div>
                <div style="text-align:right;">
                    <span style="font-size:15px; font-weight:700; color:#0f172a;">
                        {{ number_format($cot->montant) }} F
                    </span>
                    <div>
                        @if($cot->statut === 'valide')
                            <span class="hist-badge badge-valid">
                                <svg width="8" height="8" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
                                Validé
                            </span>
                        @elseif($cot->statut === 'en_attente')
                            <span class="hist-badge badge-wait">
                                <svg width="8" height="8" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
                                En attente
                            </span>
                        @else
                            <span class="hist-badge badge-reject">
                                <svg width="8" height="8" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
                                Rejeté
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div style="text-align:center; padding: 32px 0;">
                <svg width="36" height="36" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" style="margin: 0 auto 10px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p style="font-size:12px; color:#9ca3af; font-weight:500;">Aucune cotisation enregistrée</p>
            </div>
            @endforelse
        </div>

        @if($cotisations->hasPages())
        <div class="mt-4">
            {{ $cotisations->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
