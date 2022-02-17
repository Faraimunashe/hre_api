<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add New Account
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    Create An Account
                </div>
                @if (\Session::has('success'))
                    <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md" role="alert">
                        <div class="flex">
                            <div class="py-1"></div>
                            <div>
                                <p class="font-bold">New account number :  {!! \Session::get('success') !!}</p>
                                <p class="text-sm">Account created successfully</p>
                            </div>
                        </div>
                    </div>
                @endif
                @if (\Session::has('error'))
                    <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md" role="alert">
                        <div class="flex">
                            <div class="py-1"></div>
                            <div>
                                <p class="font-bold">Error :  {!! \Session::get('error') !!}</p>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="flex items-center justify-center">

                    <form method="POST" action="{{route('add-account')}}">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <x-input class="mt-1 w-full" type="text" name="fname" placeholder="Firstname" required />
                        </div>
                        <div>
                            <x-input class="mt-1 w-full" type="text" name="lname" placeholder="Lastname" required />
                        </div>
                        <div>
                            <x-input class="mt-1 w-full" type="text" name="address" placeholder="Address" required />
                        </div>
                        <div class="mt-4">
                            <x-label for="gender" value="Select Gender" />

                            <select id="gender" class="mt-1 w-full" name="gender">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>


                        <div class="flex items-center justify-center mt-4">

                            <x-button class="ml-2">
                                Create Account
                            </x-button>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</x-app-layout>
