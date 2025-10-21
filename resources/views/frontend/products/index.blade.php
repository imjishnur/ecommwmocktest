@extends('frontend.layout.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Products</h2>

    <div class="row">
        @forelse($products as $product)
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                @if($product->image)
                <img src="{{ asset('storage/'.$product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                @else
                <img src="{{ asset('images/no-image.png') }}" class="card-img-top" alt="No image">
                @endif
                <div class="card-body d-flex flex-column">
    <h5 class="card-title">{{ $product->name }}</h5>
    <p class="card-text">Category: {{ $product->category?->name ?? 'N/A' }}</p>
    @if($product->color?->name)
        <p class="card-text">Color: {{ $product->color->name }}</p>
    @endif
    @if($product->size?->name)
        <p class="card-text">Size: {{ $product->size->name }}</p>
    @endif
    <p class="card-text">Price: ${{ number_format($product->price, 2) }}</p>
    <p class="card-text">Qty: {{ $product->qty }}</p>
    <button class="btn btn-primary mt-auto add-to-cart-btn" data-id="{{ $product->id }}">Add to Cart</button>
</div>

            </div>
        </div>
        @empty
        <div class="col-12">
            <p class="text-center">No active products available.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = "{{ csrf_token() }}";

    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.dataset.id;

            fetch("{{ route('cart.add') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({ product_id: productId, qty: 1 })
            })
            .then(res => res.json())
            .then(data => {
                // Show success message with "Go to Cart" link
                const message = document.createElement('div');
                message.classList.add('alert','alert-success','mt-2');
                message.innerHTML = `${data.message} <a href="{{ route('cart.index') }}" class="btn btn-sm btn-primary ms-2">Go to Cart</a>`;
                this.parentElement.appendChild(message);
            })
            .catch(err => console.error(err));
        });
    });
});
</script>
@endsection




