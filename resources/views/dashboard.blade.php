<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    {{--  <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div> --}}

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @livewire('toast-notification')

        <!-- Success Message -->
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Products Section -->
        <div class="bg-white shadow-md rounded-lg mb-6">
            <div class="p-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Products</h3>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('products.create') }}"
                        class="inline-block mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add
                        Product</a>
                @endif
            </div>
            <div class="p-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            @if (auth()->user()->isAdmin())
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($products as $product)
                            <tr>
                                <td class="px-6 py-4">{{ $product->name }}</td>
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

        <!-- Quotations Section -->
        <div class="bg-white shadow-md rounded-lg mb-6">
            <div class="p-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Quotations</h3>
                <a href="{{ route('quotations.create') }}"
                    class="inline-block mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Create
                    Quotation</a>
            </div>
            <div class="p-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($quotations as $quotation)
                            <tr>
                                <td class="px-6 py-4">{{ $quotation->customer_name }}</td>
                                <td class="px-6 py-4">{{ $quotation->total_price }}</td>
                                <td class="px-6 py-4">{{ ucfirst($quotation->status) }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('quotations.show', $quotation) }}"
                                        class="text-blue-600 hover:text-blue-800">View</a>
                                    <a href="{{ route('quotations.pdf', $quotation) }}"
                                        class="text-gray-600 hover:text-gray-800 ml-4">PDF</a>
                                    @if (auth()->user()->isAdmin() && $quotation->status == 'pending')
                                        <form action="{{ route('quotations.status', $quotation) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <select name="status" onchange="this.form.submit()"
                                                class="ml-4 border-gray-300 rounded">
                                                <option value="">Update Status</option>
                                                <option value="approved">Approve</option>
                                                <option value="rejected">Reject</option>
                                            </select>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Notifications Section -->
        @if ($notifications->count())
            <div class="bg-white shadow-md rounded-lg">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Notifications</h3>
                </div>
                <div class="p-4">
                    <ul class="list-disc pl-5">
                        @foreach ($notifications as $notification)
                            <li class="text-gray-700 {{ $notification->read_at ? 'opacity-50' : '' }}">
                                {{ $notification->data['message'] }}
                                <a href="{{ route('quotations.show', $notification->data['quotation_id']) }}"
                                    class="text-blue-600 hover:text-blue-800">
                                    (Quotation #{{ $notification->data['quotation_id'] }})
                                </a>
                                @if (!$notification->read_at)
                                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        <button type="submit" class="text-gray-600 hover:text-gray-800 ml-2">Mark as
                                            Read</button>
                                    </form>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
