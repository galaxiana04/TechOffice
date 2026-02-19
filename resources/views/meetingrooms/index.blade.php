@extends('layouts.universal')

@section('container2')
    <div class="content-header mb-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark font-bold text-2xl tracking-tight">Dashboard Notulen</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="/" class="text-red-600 hover:text-red-800 transition font-medium">Notulen</a></li>
                        <li class="breadcrumb-item active text-gray-500 font-medium">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container-fluid px-4 pb-10 font-sans">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                    
                    <div class="bg-gradient-to-r from-red-700 to-red-500 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-white text-xl font-bold flex items-center gap-2 tracking-wide">
                            <i class="fas fa-door-open"></i> Meeting Rooms
                        </h3>
                        @if (auth()->id() == 1)
                            <button onclick="showCreateModal()" 
                                class="bg-white text-red-600 hover:bg-gray-100 px-4 py-2 rounded-lg shadow-md font-semibold transition-all duration-300 transform hover:scale-105 flex items-center gap-2 text-sm">
                                <i class="fas fa-plus"></i> Add Room
                            </button>
                        @endif
                    </div>

                    <div class="p-6">
                        
                        @if(session('success'))
                            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-r shadow-sm" role="alert">
                                <p class="font-medium">{{ session('success') }}</p>
                            </div>
                        @endif

                        <div class="overflow-x-auto">
                            <table class="min-w-full leading-normal">
                                <thead>
                                    <tr>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                                            Capacity
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                            Description
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    @foreach($meetingRooms as $room)
                                        <tr class="hover:bg-red-50 transition duration-200 border-b border-gray-200">
                                            <td class="px-5 py-4 bg-white text-sm">
                                                <div class="flex items-center">
                                                    <div class="ml-3">
                                                        <p class="whitespace-no-wrap font-semibold text-gray-800 text-base">
                                                            {{ $room->name }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4 bg-white text-sm text-center">
                                                <span class="inline-block bg-gray-100 rounded-full px-3 py-1 text-xs font-bold text-gray-600 border border-gray-200 shadow-sm">
                                                    {{ $room->capacity }} Pax
                                                </span>
                                            </td>
                                            <td class="px-5 py-4 bg-white text-sm">
                                                <p class="text-gray-500 whitespace-no-wrap font-medium">
                                                    {{ Str::limit($room->description, 50) }}
                                                </p>
                                            </td>
                                            <td class="px-5 py-4 bg-white text-sm text-center">
                                                @if($room->is_available)
                                                    <span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                                        <span aria-hidden="true" class="absolute inset-0 bg-green-100 opacity-50 rounded-full border border-green-200"></span>
                                                        <span class="relative text-xs tracking-wide">AVAILABLE</span>
                                                    </span>
                                                @else
                                                    <span class="relative inline-block px-3 py-1 font-semibold text-red-900 leading-tight">
                                                        <span aria-hidden="true" class="absolute inset-0 bg-red-100 opacity-50 rounded-full border border-red-200"></span>
                                                        <span class="relative text-xs tracking-wide">CLOSED</span>
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-5 py-4 bg-white text-sm text-center">
                                                <div class="flex justify-center items-center gap-2">
                                                    <button onclick="showEditModal({{ $room }})" 
                                                        class="flex items-center gap-2 bg-yellow-50 hover:bg-yellow-100 text-yellow-600 hover:text-yellow-700 px-3 py-1.5 rounded-md text-xs font-bold transition shadow-sm border border-yellow-200 uppercase tracking-wide">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    
                                                    @if (auth()->id() == 1)
                                                        <form action="{{ route('meetingrooms.destroy', $room->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" onclick="confirmDelete(this)" 
                                                                class="flex items-center gap-2 bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 px-3 py-1.5 rounded-md text-xs font-bold transition shadow-sm border border-red-200 uppercase tracking-wide">
                                                                <i class="fas fa-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if($meetingRooms->isEmpty())
                                <div class="text-center py-10 text-gray-400">
                                    <i class="fas fa-folder-open text-5xl mb-3 text-red-100"></i>
                                    <p class="font-medium">No meeting rooms found.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('success'))
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        </script>
    @endif

    <script>
        // Updated styling classes for SweetAlert Inputs (Font Included)
        const swalInputClasses = "w-full p-2.5 mb-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium text-gray-700";
        const swalSelectClasses = "w-full p-2.5 mb-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 bg-white font-medium text-gray-700";
        const labelClasses = "block text-sm font-bold text-gray-600 mb-1";

        function showCreateModal() {
            Swal.fire({
                title: '<span class="text-xl font-bold text-gray-800">Add Meeting Room</span>',
                html: `
                    <form id="create-form" action="{{ route('meetingrooms.store') }}" method="POST" class="text-left mt-2">
                        @csrf
                        <label class="${labelClasses}">Room Name</label>
                        <input type="text" name="name" class="${swalInputClasses}" placeholder="e.g., Sadewa Room" required>
                        
                        <label class="${labelClasses}">Capacity</label>
                        <input type="number" name="capacity" class="${swalInputClasses}" placeholder="e.g., 20" required>
                        
                        <label class="${labelClasses}">Description</label>
                        <textarea name="description" class="${swalInputClasses}" placeholder="Optional details..." rows="2"></textarea>
                        
                        <label class="${labelClasses}">Status</label>
                        <select name="is_available" class="${swalSelectClasses}">
                            <option value="1">Available</option>
                            <option value="0">Not Available</option>
                        </select>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: '<i class="fas fa-save mr-1"></i> Save',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'font-poppins-popup' 
                },
                focusConfirm: false,
                preConfirm: () => {
                    const form = document.getElementById('create-form');
                    if (!form.checkValidity()) {
                        Swal.showValidationMessage('Please fill out all required fields');
                        return false;
                    }
                    form.submit();
                }
            });
        }

        function showEditModal(room) {
            Swal.fire({
                title: '<span class="text-xl font-bold text-gray-800">Edit Room</span>',
                html: `
                    <form id="edit-form" action="/meetingrooms/${room.id}" method="POST" class="text-left mt-2">
                        @csrf
                        @method('PUT')
                        <label class="${labelClasses}">Room Name</label>
                        <input type="text" name="name" class="${swalInputClasses}" value="${room.name}" required>
                        
                        <label class="${labelClasses}">Capacity</label>
                        <input type="number" name="capacity" class="${swalInputClasses}" value="${room.capacity}" required>
                        
                        <label class="${labelClasses}">Description</label>
                        <textarea name="description" class="${swalInputClasses}" rows="2">${room.description || ''}</textarea>
                        
                        <label class="${labelClasses}">Status</label>
                        <select name="is_available" class="${swalSelectClasses}">
                            <option value="1" ${room.is_available ? 'selected' : ''}>Available</option>
                            <option value="0" ${!room.is_available ? 'selected' : ''}>Not Available</option>
                        </select>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: '<i class="fas fa-sync-alt mr-1"></i> Update',
                customClass: {
                    popup: 'font-poppins-popup'
                },
                preConfirm: () => {
                    document.getElementById('edit-form').submit();
                }
            });
        }

        function confirmDelete(button) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc2626",
                cancelButtonColor: "#9ca3af",
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    popup: 'font-poppins-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            });
        }
    </script>
@endsection

@push('css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'], // Set Poppins as default sans font
                    },
                }
            }
        }
    </script>
    
    <style>
        body, .swal2-popup, .swal2-title, .swal2-content {
            font-family: 'Poppins', sans-serif !important;
        }
        
        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .swal2-popup {
            border-radius: 1rem !important;
            padding: 2rem !important;
        }
    </style>
@endpush