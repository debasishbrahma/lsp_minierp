<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <header>
                        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                            {{ __('Products') }}
                        </h2>
                        <p class="mt-4 mb-5 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Product List information.') }}
                        </p>
                    </header>
                </div>



                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('products.create') }}"
                            class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add
                            Product</a>
                    @endif
                    <div class="bg-white shadow-md rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit
                                        Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity
                                    </th>
                                    @if (auth()->user()->isAdmin())
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($products as $product)
                                    <tr>
                                        <td class="px-6 py-4">{{ $product->name }}</td>
                                        <td class="px-6 py-4">{{ $product->description }}</td>
                                        <td class="px-6 py-4">{{ $product->unit_price }}</td>
                                        <td class="px-6 py-4">{{ $product->quantity_available }}</td>
                                        @if (auth()->user()->isAdmin())
                                            <td class="px-6 py-4">
                                                <a href="{{ route('products.edit', $product) }}"
                                                    class="text-blue-600 hover:text-blue-800">Edit</a>
                                                <form action="{{ route('products.destroy', $product) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-800 ml-4">Delete</button>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
