<div>
<div class="grid grid-cols-2 gap-2">
                <div class="flex justify-center">
                    <x-card class="border-2 border-gray-200">
                    <img src="{{ $customer?->profile ? '/storage/' . $customer?->profile : '/imgs/noimage.jpg' }}" class="w-100 h-100" alt="">
                    </x-card>
                </div>
                <div>
                    <x-card>
                    <table class="table table-xs table-zebra">
                        <tbody>
                        <tr>
                            <td>Registration Number</td>
                            <td>{{ $customer?->regnumber }}</td>
                        </tr>
                        <tr>
                            <td>Name</td>
                            <td>{{ $customer?->name }}</td>
                        </tr>
                        <tr>
                            <td>Surname</td>
                            <td>{{ $customer?->surname }}</td>
                        </tr>
                        <tr>
                            <td>Previous Name</td>
                            <td>{{ $customer?->previousname }}</td>
                        </tr>
                        <tr>
                            <td>Date of Birth</td>
                            <td>{{ $customer?->dob }}</td>
                        </tr>
                        <tr>
                            <td>Gender</td>
                            <td>{{ $customer?->gender }}</td>
                        </tr>
                        <tr>
                            <td>Marital Status</td>
                            <td>{{ $customer?->maritalstatus }}</td>
                        </tr>
                        <tr>
                            <td>Identity Type</td>
                            <td>{{ $customer?->identificationtype }}</td>
                        </tr>
                        <tr>
                            <td>Identity Number</td>
                            <td>{{ $customer?->identificationnumber}}</td>
                        </tr>
                        <tr>
                            <td>Nationality</td>
                            <td>{{ $customer?->nationality?->name }}</td>
                        </tr>
                        <tr>
                            <td>Province</td>
                            <td>{{ $customer?->province?->name }}</td>
                        </tr>
                        <tr>
                            <td>City</td>
                            <td>{{ $customer?->city?->name }}</td>
                        </tr>
                        <tr>
                            <td>Employment Status</td>
                            <td>{{ $customer?->employmentstatus?->name }}</td>
                        </tr>
                        <tr>
                            <td>Employment Location</td>
                            <td>{{ $customer?->employmentlocation?->name }}</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>{{ $customer?->email }}</td>
                        </tr>
                        <tr>
                            <td>Phone</td>
                            <td>{{ $customer?->phone }}</td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td>{{ $customer?->address }}</td>
                        </tr>
                        <tr>
                            <td>Place of Birth</td>
                            <td>{{ $customer?->place_of_birth }}</td>
                        </tr>
                        </tbody>
                    </table>
                    </x-card>
        </div>
    </div>
</div>
