<!-- Product Add/Edit Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="productForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="product_id">

                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>

                    <div class="mb-3">
                        <label>Category</label>
                        <select class="form-control" name="category_id" id="category_id" required>
                            <option value="">Select Category</option>
                            @foreach(\App\Models\Category::all() as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Color</label>
                        <select class="form-control" name="color_id" id="color_id">
                            <option value="">Select Color</option>
                            @foreach(\App\Models\Color::all() as $color)
                                <option value="{{ $color->id }}">{{ $color->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Size</label>
                        <select class="form-control" name="size_id" id="size_id">
                            <option value="">Select Size</option>
                            @foreach(\App\Models\Size::all() as $size)
                                <option value="{{ $size->id }}">{{ $size->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Qty</label>
                        <input type="number" class="form-control" name="qty" id="qty" required>
                    </div>

                    <div class="mb-3">
                        <label>Price</label>
                        <input type="number" class="form-control" step="0.01" name="price" id="price" required>
                    </div>

                    <div class="mb-3">
                        <label>Image (JPG/PNG)</label>
                        <input type="file" class="form-control" name="image" id="image">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="modalBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
function openAddModal() {
    document.getElementById('modalTitle').innerText = 'Add Product';
    document.getElementById('productForm').action = "{{ route('admin.products.store') }}";
    document.getElementById('product_id').value = '';
    document.getElementById('name').value = '';
    document.getElementById('category_id').value = '';
    document.getElementById('color_id').value = '';
    document.getElementById('size_id').value = '';
    document.getElementById('qty').value = '';
    document.getElementById('price').value = '';
    document.getElementById('image').value = '';

    // Remove previous _method input if exists
    const methodInput = document.querySelector('#productForm input[name="_method"]');
    if(methodInput) methodInput.remove();
}

function openEditModal(product) {
    document.getElementById('modalTitle').innerText = 'Edit Product';
    document.getElementById('productForm').action = '/admin/products/' + product.id;

    let methodInput = document.querySelector('#productForm input[name="_method"]');
    if(!methodInput){
        methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        document.getElementById('productForm').appendChild(methodInput);
    }

    document.getElementById('product_id').value = product.id;
    document.getElementById('name').value = product.name;
    document.getElementById('category_id').value = product.category_id ?? '';
    document.getElementById('color_id').value = product.color_id ?? '';
    document.getElementById('size_id').value = product.size_id ?? '';
    document.getElementById('qty').value = product.qty;
    document.getElementById('price').value = product.price;
    document.getElementById('image').value = '';
}
</script>

