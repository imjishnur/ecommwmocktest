@extends('frontend.layout.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Shopping Cart</h2>

    @if(count($cartItems) > 0)
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Subtotal</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cartItems as $item)
            <tr>
                <td>{{ $item['product']->name }}</td>
                <td>{{ $item['qty'] }}</td>
                <td>${{ number_format($item['product']->price,2) }}</td>
                <td>${{ number_format($item['subtotal'],2) }}</td>
                <td>
                    <button class="btn btn-danger btn-sm remove-cart-btn" data-id="{{ $item['product']->id }}">Remove</button>
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3" class="text-end">Total</td>
                <td colspan="2" id="cart-total">${{ number_format($total,2) }}</td>
            </tr>
            @if(session()->has('coupon'))
            <tr>
                <td colspan="3" class="text-end">Discount ({{ session('coupon.code') }})</td>
                <td colspan="2">-${{ number_format(session('coupon.discount'),2) }}</td>
            </tr>
            <tr>
                <td colspan="3" class="text-end"><strong>Final Total</strong></td>
                <td colspan="2"><strong>${{ number_format($total - session('coupon.discount'),2) }}</strong></td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="d-flex mb-3">
        <input type="text" id="coupon-code" class="form-control me-2" placeholder="Coupon code"
               value="{{ session('coupon.code') ?? '' }}">
        <button id="apply-coupon-btn" class="btn btn-success">Apply</button>
    </div>
    <button id="checkout-btn" class="btn btn-primary">Checkout</button>

    @else
    <p>Cart is empty.</p>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    
    document.querySelectorAll('.remove-cart-btn').forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.dataset.id;

            fetch(`/cart/remove/${productId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).then(res => res.json())
              .then(data => {
                  if(data.success){
                      location.reload();
                  }
              });
        });
    });

    document.getElementById('apply-coupon-btn')?.addEventListener('click', function () {
        const code = document.getElementById('coupon-code').value;

        fetch('/cart/apply-coupon', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ code })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message); // optional
            if(data.success){
                location.reload(); // refresh to show discount row
            }
        });
    });

    document.getElementById('checkout-btn')?.addEventListener('click', function () {
    fetch('/cart/checkout', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
         
            window.location.href = `/order-success/${data.order_id}`;
        } else {
            alert(data.message);
        }
    });
});


});
</script>
@endsection
