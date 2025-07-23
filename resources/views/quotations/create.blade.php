<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Create Quotation') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('New Quotation.') }}
                        </p>
                    </header>
                </div>

                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    @if ($errors->has('products'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                         role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">{{ $errors->first('products') }}</span>
                    </div>
                    @endif
                    <form action="{{ route('quotations.store') }}" method="POST"
                          class="bg-white shadow-md rounded-lg p-6">
                        @csrf
                        <div class="mb-4">
                            <label for="customer_name" class="block text-sm font-medium text-gray-700">Customer
                                Name</label>
                            <input type="text" name="customer_name" id="customer_name"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   required>
                        </div>
                        <div id="items">
                            <div class="item mb-4">
                                <label for="product_id" class="block text-sm font-medium text-gray-700">Product</label>
                                <select name="products[0][product_id]"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        required>
                                    @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}
                                        (price - {{ $product->unit_price }})(Stock - {{ $product->quantity_available }})
                                    </option>
                                    @endforeach
                                </select>
                                <label for="quantity"
                                       class="block text-sm font-medium text-gray-700 mt-2">Quantity</label>
                                <input type="number" name="products[0][quantity]"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                       required>
                            </div>
                        </div>
                        <button type="button" onclick="addItem()"
                                class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Add Item
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 ml-2">Save
                            Quotation
                        </button>
                    </form>
                </div>

                <script>
                    let itemCount = 1;

                    function addItem() {
                        const items = document.getElementById('items');
                        const newItem = document.createElement('div');
                        newItem.className = 'item mb-4';
                        newItem.innerHTML = `
                <label for="product_id" class="block text-sm font-medium text-gray-700">Product</label>
                <select name="products[${itemCount}][product_id]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} (price - {{ $product->unit_price }})(Stock - {{ $product->quantity_available }})</option>
                    @endforeach
                </select>
                <label for="quantity" class="block text-sm font-medium text-gray-700 mt-2">Quantity</label>
                <input type="number" name="products[${itemCount}][quantity]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
            `;
                        items.appendChild(newItem);
                        itemCount++;
                    }
                </script>
            </div>
        </div>
    </div>
</x-app-layout>
