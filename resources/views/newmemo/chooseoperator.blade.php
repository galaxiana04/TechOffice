@extends('layouts.universal')

@php
    use Carbon\Carbon; // Import Carbon class
    $categoryprojectbaru = json_decode($category, true)[0];
    $categoryproject = trim($categoryprojectbaru, '"');
    $listpic = json_decode($categoryproject, true);
@endphp

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="">Memo</a></li>
                        <li class="breadcrumb-item"><a href="">Edit Dokumen</a></li>

                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('container3')
    <div class="row justify-content-center">



        <div class="col-10">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Edit Memo</h3>
                </div>

                <div class="card-body">
                    <form action="{{ route('new-memo.postchooseoperator', ['memoId' => $document->id]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="documentname">Nama Memo:</label>
                            <textarea class="form-control" name="documentname" id="documentname" rows="5">{{ $document->documentname }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="komat">Masukan 1 komat (Untuk memunculkan Pemilik Kode Material):</label>
                            <input type="text" id="komat" name="komat" class="form-control" required>
                            <button type="button" class="btn btn-secondary mt-2" onclick="generateIR()">Pemilik Kode
                                Material Generate</button>
                        </div>

                        <div class="form-group">
                            <label for="operator">Pemilik Kode Material</label>
                            <input type="text" id="operator" name="operator" class="form-control" required>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>


        </div>

    </div>
@endsection

@push('css')
@endpush

@push('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="{{ asset('adminlte3/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addFileButton = document.getElementById('addFile');
            const additionalFileContainer = document.getElementById('additionalFileContainer');
            const selectedFilesContainer = document.getElementById('selectedFiles');
            const operatorField = document.getElementById('operator');
            const uploadForm = document.getElementById('uploadForm');

            const validOperators = [
                'Product Engineering',
                'Desain Mekanik & Interior',
                'Desain Bogie & Wagon',
                'Desain Carbody',
                'Desain Elektrik',
                'Preparation & Support',
                'Welding Technology',
                'Shop Drawing',
                'Teknologi Proses'
            ];

            addFileButton.addEventListener('click', function() {
                const newFileInput = document.createElement('div');
                newFileInput.className = 'form-group mt-2';

                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.name = 'file[]';
                fileInput.className = 'form-control-file';
                fileInput.required = true;

                const deleteButton = document.createElement('button');
                deleteButton.textContent = 'Hapus';
                deleteButton.type = 'button';
                deleteButton.className = 'btn btn-danger mt-2';
                deleteButton.addEventListener('click', function() {
                    additionalFileContainer.removeChild(newFileInput);
                    updateSelectedFiles();
                });

                newFileInput.appendChild(fileInput);
                newFileInput.appendChild(deleteButton);

                additionalFileContainer.appendChild(newFileInput);
                updateSelectedFiles();
            });

            function updateSelectedFiles() {
                const fileInputs = document.querySelectorAll('input[type="file"]');
                selectedFilesContainer.innerHTML = '';

                fileInputs.forEach(function(input) {
                    if (input.files.length > 0) {
                        const files = Array.from(input.files);
                        files.forEach(function(file) {
                            const fileNameElement = document.createElement('div');
                            fileNameElement.textContent = file.name;
                            selectedFilesContainer.appendChild(fileNameElement);
                        });
                    }
                });
            }

            uploadForm.addEventListener('submit', function(event) {
                event.preventDefault();

                // Cek apakah field operator ada
                const operatorField = document.getElementById('operator');
                if (operatorField) {
                    const operatorValue = operatorField.value.trim();
                    if (!validOperators.includes(operatorValue)) {
                        Swal.fire({
                            title: 'Invalid Operator',
                            text: 'Operator harus salah satu dari: ' + validOperators.join(', '),
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }
                }

                Swal.fire({
                    title: 'Confirmation',
                    text: 'Do you want to submit the form?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, submit'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: "Updated!",
                            text: "Your information has been uploaded.",
                            icon: "success"
                        });
                        uploadForm.submit();
                    }
                });
            });




        });
    </script>


    <script>
        function generateIR() {
            var komatValue = document.getElementById('komat').value;
            var operatorField = document.getElementById('operator');

            // Show SweetAlert loading spinner
            Swal.fire({
                title: 'Loading...',
                text: 'Fetching operator...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // AJAX request to fetch the operator based on 'komat'
            fetch('{{ route('newbom.operatorfindbykomat') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token
                    },
                    body: JSON.stringify({
                        komat: komatValue
                    })
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close(); // Close the loading spinner

                    // Modify output if it matches specific values
                    if (data.operator === 'Sistem Mekanik' || data.operator === 'Desain Interior') {
                        operatorField.value = 'Desain Mekanik & Interior';
                    } else {
                        operatorField.value = data.operator; // Update the operator field normally
                    }
                })
                .catch(error => {
                    Swal.close(); // Close the loading spinner
                    console.error('Error:', error);
                });
        }
    </script>
@endpush
