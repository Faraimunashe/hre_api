<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Payments
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white  shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    Process Payment
                </div>
                <div class="px-10 ml-10 mr-10">
                    <x-slot name="logo">
                        <a href="/">
                            <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                        </a>
                    </x-slot>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{route('payment')}}">
                        @csrf

                        <div class="flex">
                            <label for="acc" class="text-bold">Acount Number</label>

                            <x-input id="acc" class="mt-1 w-full" type="text" name="accnum" placeholder="Account #" required />
                        </div>

                        <div class="flex mt-4">
                            <label for="amnt">Enter Amount</label>

                            <x-input id="amnt" class="mt-1 w-full" placeholder="Amount"
                                            type="number"
                                            name="amount"
                                            required />
                        </div>
                        <div class="flex mt-4">
                            <label for="amnt" value="Enter Payment Method" >Select Method</label>

                            <select id="test" onchange="showDiv(this)" class="mt-1 w-full" name="method">
                                <option value="cash">Cash Payment</option>
                                <option value="paynow">Paynow Payment</option>
                            </select>
                        </div>
                        <div id="hidden_div" style="display: none;" class="flex mt-4">
                            <label for="mth">Enter Phone</label>

                            <x-input id="mth" class="mt-1 w-full" placeholde="Enter Phone"
                                            type="text"
                                            name="phone"
                                            required />
                        </div>
                        <div class="flex items-center justify-end mt-4">

                            <x-button type="submit" class="ml-3">
                                Process Payment
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function showDiv(select){
        if(select.value=='paynow'){
                document.getElementById('hidden_div').style.display = "block";
            } else{
                document.getElementById('hidden_div').style.display = "none";
            }
        }
    </script>
</x-app-layout>
