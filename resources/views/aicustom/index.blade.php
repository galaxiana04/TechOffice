@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="/">AI Model</a></li>
                        <li class="breadcrumb-item active text-bold">Setting</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-danger card-outline">
                <div class="card-header">
                    <h3>AI Custom</h3>
                </div>
                <div class="card-body">
                    <button class="btn btn-primary" onclick="addData()">Tambah Data</button>
                    <table class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>Keyword</th>
                                <th class="col-xl-6 text-wrap">Description</th>
                                <th>Speciality</th>
                                <th>Tipe Pembacaan</th>
                                <th>Output</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $item)
                                <tr id="row-{{ $item->id }}">
                                    <td>{{ $item->keyword }}</td>
                                    <td class="col-xl-6 text-wrap">{{ $item->description }}</td>
                                    <td>{{ $item->speciality->speciality ?? 'N/A' }}</td>
                                    <td>{{ $item->scanning ?? 'N/A' }}</td>
                                    <td>{{ $item->output ?? 'N/A' }}</td>
                                    <td>
                                        <button class="btn btn-warning"
                                            onclick="editData({{ $item->id }}, '{{ addslashes($item->keyword) }}','{{ addslashes($item->output) }}', '{{ addslashes($item->description) }}', {{ $item->aicustomspeciality_id ?? 'null' }})">
                                            Edit
                                        </button>
                                        <button class="btn btn-danger" onclick="deleteData({{ $item->id }})">Hapus</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let specialities = @json($specialities);

        function generateSpecialityOptions(selectedId = null) {
            let options = '<option value="">Pilih Speciality</option>';
            specialities.forEach(speciality => {
                options += `<option value="${speciality.id}" ${selectedId == speciality.id ? 'selected' : ''}>${speciality.speciality}</option>`;
            });
            return options;
        }

        function addData() {
            Swal.fire({
                title: 'Tambah Data',
                icon: 'info',
                width: '100em',
                html: `
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <input id="keyword" class="swal2-input" placeholder="Keyword">
                        <textarea id="description" class="swal2-textarea" placeholder="Description"></textarea>
                        <select id="speciality" class="swal2-select">${generateSpecialityOptions()}</select>
                        <select id="output" class="swal2-select">
                            <option value="pdf">PDF</option>
                            <option value="text">Text</option>
                        </select>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                preConfirm: () => {
                    let keyword = document.getElementById('keyword').value;
                    let output = document.getElementById('output').value;
                    let description = document.getElementById('description').value.replace(/"/g, '&quot;');
                    let specialityId = document.getElementById('speciality').value;

                    return fetch("{{ route('aicustom.store') }}", {
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            keyword: keyword,
                            description: description,
                            output: output,
                            aicustomspeciality_id: specialityId
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire('Sukses', data.success, 'success').then(() => location.reload());
                        });
                }
            });
        }


        function editData(id, keyword, output, description, specialityId) {
            Swal.fire({
                title: 'Edit Data',
                icon: 'info',
                width: '100em',
                html: `
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <input id="keyword" class="swal2-input" value="${keyword}">
                    <textarea id="description" class="swal2-textarea">${description.replace(/&quot;/g, '"')}</textarea>
                    <select id="speciality" class="swal2-select">${generateSpecialityOptions(specialityId)}</select>
                    <select id="output" class="swal2-select">
                        <option value="pdf" ${output === 'pdf' ? 'selected' : ''}>PDF</option>
                        <option value="text" ${output === 'text' ? 'selected' : ''}>Text</option>
                    </select>
                </div>
            `,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                preConfirm: () => {
                    let newKeyword = document.getElementById('keyword').value;
                    let newDescription = document.getElementById('description').value.replace(/"/g, '&quot;');
                    let newSpecialityId = document.getElementById('speciality').value;
                    let newOutput = document.getElementById('output').value;

                    return fetch("{{ url('aicustom') }}/" + id, {
                        method: "PUT",
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            keyword: newKeyword,
                            output: newOutput,
                            description: newDescription,
                            aicustomspeciality_id: newSpecialityId
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire('Sukses', data.success, 'success').then(() => location.reload());
                        });
                }
            });
        }


        function deleteData(id) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                preConfirm: () => {
                    return fetch("{{ url('aicustom') }}/" + id, {
                        method: "DELETE",
                        headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'Content-Type': 'application/json' }
                    })
                        .then(response => response.json())
                        .then(data => { Swal.fire('Sukses', data.success, 'success').then(() => location.reload()); });
                }
            });
        }
    </script>
@endsection