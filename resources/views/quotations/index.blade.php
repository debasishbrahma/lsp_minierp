<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Quotations') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Quotations list.') }}
                        </p>
                    </header>
                </div>

                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <a href="{{ route('quotations.create') }}"
                        class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Create
                        Quotation</a>
                    <div class="bg-white shadow-md rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total
                                        Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions
                                    </th>
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
                                                <form action="{{ route('quotations.status', $quotation) }}"
                                                    method="POST" class="inline">
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
            </div>
        </div>
    </div>
</x-app-layout>
