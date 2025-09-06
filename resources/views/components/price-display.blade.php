@props(['amount', 'currency' => null, 'showSymbol' => true, 'class' => ''])

@php
    use App\Helpers\CurrencyHelper;
    
    $currentCurrency = $currency ?: CurrencyHelper::getCurrentCurrency();
    $formattedPrice = CurrencyHelper::formatPrice($amount, $currentCurrency);
@endphp

<span class="{{ $class }}">
    @if($showSymbol)
        {{ $formattedPrice }}
    @else
        {{ number_format($amount, 0, ',', '.') }}
    @endif
</span>
