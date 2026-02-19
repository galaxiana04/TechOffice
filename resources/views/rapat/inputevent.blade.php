@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-transparent p-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('events.all') }}" class="text-danger">
                                <i class="fas fa-calendar-alt mr-1"></i> Jadwal
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-muted">Buat Jadwal Baru</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg rounded-lg overflow-hidden">
                    <div class="card-header bg-gradient-red-custom text-white p-4">
                        <div class="d-flex flex-column align-items-start">
                            <h4 class="card-title font-weight-bold mb-1"> <i class="fas fa-plus-circle mr-2"></i> Buat
                                Jadwal Baru
                            </h4>
                            <p class="mb-0 small text-white-50">
                                Silakan isi detail rapat di bawah ini (Link Eksternal sudah ada)
                            </p>
                        </div>
                    </div>

                    <div class="card-body p-5">
                        <form action="{{ route('events.store') }}" method="POST" id="create-event-form">
                            @csrf

                            <h6 class="text-danger font-weight-bold mb-3 border-bottom pb-2">
                                <i class="fas fa-info-circle mr-1"></i> Detail Acara
                            </h6>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="title" class="font-weight-medium">Judul Rapat <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0"><i
                                                    class="fas fa-heading text-muted"></i></span>
                                        </div>
                                        <input type="text" class="form-control border-left-0" id="title" name="title"
                                            placeholder="Contoh: Rapat Koordinasi Mingguan" required>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="pic" class="font-weight-medium">PIC (Penanggung Jawab) <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0"><i
                                                    class="fas fa-user-tie text-muted"></i></span>
                                        </div>
                                        <input type="text" class="form-control border-left-0" id="pic" name="pic"
                                            placeholder="Nama PIC" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="agenda_desc" class="font-weight-medium">Agenda Rapat <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="agenda_desc" name="agenda_desc" rows="2"
                                    placeholder="Deskripsikan agenda singkat..." required></textarea>
                            </div>

                            <h6 class="text-danger font-weight-bold mb-3 mt-4 border-bottom pb-2">
                                <i class="fas fa-clock mr-1"></i> Waktu & Lokasi
                            </h6>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="start" class="font-weight-medium">Mulai <span
                                            class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="start" name="start" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="end" class="font-weight-medium">Selesai <span
                                            class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="end" name="end" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="room" class="font-weight-medium">Ruangan <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control custom-select" id="room" name="room" required>
                                        <option value="Tidak Menggunakan Ruangan">Tidak Menggunakan Ruangan</option>
                                        @foreach($ruangrapat as $room)
                                            <option value="{{ $room }}">{{ $room }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="use_zoom" class="font-weight-medium">Opsi Online Meeting <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control custom-select" id="use_zoom" name="use_zoom" required>
                                        <option value="yes">Ya, Gunakan Zoom Internal</option>
                                        <option value="no">Tidak</option>
                                        <option value="linkeksternal">Link Eksternal (Gmeet/Zoom Lain)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group bg-light p-3 rounded border border-dashed" id="zoom_link_container"
                                style="display: none;">
                                <label for="zoom_link" class="text-danger font-weight-bold"><i class="fas fa-link mr-1"></i>
                                    Link Meeting Eksternal</label>
                                <input type="text" class="form-control" id="zoom_link" name="zoom_link"
                                    placeholder="https://...">
                            </div>

                            <div class="text-right mb-4">
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 shadow-sm"
                                    id="check-room-availability">
                                    <i class="fas fa-search mr-1"></i> Cek Ketersediaan Ruangan/Zoom
                                </button>
                            </div>

                            <input type="hidden" class="form-control" id="backgroundColor" name="backgroundColor"
                                value="#d32f2f">

                            <h6 class="text-danger font-weight-bold mb-3 mt-4 border-bottom pb-2">
                                <i class="fas fa-users mr-1"></i> Peserta
                            </h6>

                            <div class="form-group">
                                <label class="font-weight-medium">Pilih Tipe Peserta:</label>
                                <select class="form-control custom-select" id="participant-type" required>
                                    <option value="">-- Pilih Tipe --</option>
                                    <option value="user">Kirim ke Perorangan (User)</option>
                                    <option value="unit">Kirim ke Unit/Divisi</option>
                                </select>
                            </div>

                            <div class="card bg-light border-0" id="participants-container" style="display: none;">
                                <div class="card-body">
                                    <div id="unit-participants" style="display: none;">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="font-weight-bold mb-0">Cari Unit:</label>
                                        </div>
                                        <div class="input-group mb-2">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            </div>
                                            <input type="text" id="unit-search" class="form-control"
                                                placeholder="Ketik nama unit...">
                                        </div>

                                        <div class="scrollable-checkboxes bg-white border rounded p-2 shadow-sm">
                                            @foreach($telegramaccounts as $pic)
                                                <div class="custom-control custom-checkbox unit-checkbox py-1">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="unit_{{ $loop->index }}" name="agenda_unit[]"
                                                        value="{{ $pic->account }}">
                                                    <label class="custom-control-label w-100 cursor-pointer"
                                                        for="unit_{{ $loop->index }}">{{ $pic->account }}</label>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div id="custom-units-container" class="mt-2"></div>
                                        <button type="button" class="btn btn-sm btn-link text-danger pl-0 mt-2"
                                            id="add-custom-unit">
                                            <i class="fas fa-plus mr-1"></i> Tambah Unit Manual (Custom)
                                        </button>
                                    </div>

                                    <div id="user-participants" style="display: none;">
                                        <label class="font-weight-bold">Cari Pengguna:</label>
                                        <div class="input-group mb-2">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            </div>
                                            <input type="text" id="user-search" class="form-control"
                                                placeholder="Ketik nama pengguna...">
                                        </div>

                                        <div class="scrollable-checkboxes bg-white border rounded p-2 shadow-sm">
                                            @foreach($availableUsers as $user)
                                                <div class="custom-control custom-checkbox user-checkbox py-1">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="user_{{ $user->id }}" name="personal_users_id[]"
                                                        value="{{ $user->id }}">
                                                    <label class="custom-control-label w-100 cursor-pointer"
                                                        for="user_{{ $user->id }}">{{ $user->name }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5">
                                <button type="submit" class="btn btn-gradient-red btn-block btn-lg shadow"
                                    id="submit-event">
                                    <i class="fas fa-save mr-2"></i> Simpan Jadwal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        /* Red Gradient Theme */
        .bg-gradient-red-custom {
            background: linear-gradient(135deg, #c62828 0%, #e53935 100%);
        }

        .btn-gradient-red {
            background: linear-gradient(135deg, #c62828 0%, #e53935 100%);
            color: white;
            border: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-gradient-red:hover {
            background: linear-gradient(135deg, #b71c1c 0%, #d32f2f 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(229, 57, 53, 0.4);
        }

        /* Form Tweaks */
        .form-control {
            border-radius: 6px;
            border: 1px solid #ced4da;
            padding: 10px 15px;
            height: auto;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #e53935;
            box-shadow: 0 0 0 0.2rem rgba(229, 57, 53, 0.15);
        }

        .input-group-text {
            border-radius: 6px 0 0 6px;
        }

        .form-control.border-left-0 {
            border-radius: 0 6px 6px 0;
        }

        /* Custom Scrollbar for participants */
        .scrollable-checkboxes {
            max-height: 200px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #e53935 #f1f1f1;
        }

        .scrollable-checkboxes::-webkit-scrollbar {
            width: 6px;
        }

        .scrollable-checkboxes::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .scrollable-checkboxes::-webkit-scrollbar-thumb {
            background-color: #e53935;
            border-radius: 10px;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .border-dashed {
            border-style: dashed !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('create-event-form');
            const participantTypeSelect = document.getElementById('participant-type');
            const participantsContainer = document.getElementById('participants-container');
            const unitParticipants = document.getElementById('unit-participants');
            const userParticipants = document.getElementById('user-participants');
            const useZoomSelect = document.getElementById('use_zoom');
            const zoomLinkContainer = document.getElementById('zoom_link_container');

            // Efek Fade In/Out Sederhana
            function fadeShow(element) {
                element.style.opacity = 0;
                element.style.display = 'block';
                setTimeout(() => {
                    element.style.transition = 'opacity 0.3s';
                    element.style.opacity = 1;
                }, 10);
            }

            participantTypeSelect.addEventListener('change', function () {
                if (this.value) {
                    fadeShow(participantsContainer);
                } else {
                    participantsContainer.style.display = 'none';
                }

                if (this.value === 'unit') {
                    fadeShow(unitParticipants);
                    userParticipants.style.display = 'none';
                } else if (this.value === 'user') {
                    unitParticipants.style.display = 'none';
                    fadeShow(userParticipants);
                } else {
                    participantsContainer.style.display = 'none';
                }
            });

            useZoomSelect.addEventListener('change', function () {
                if (this.value === 'linkeksternal') {
                    fadeShow(zoomLinkContainer);
                    // Fokus otomatis ke input link
                    document.getElementById('zoom_link').focus();
                } else {
                    zoomLinkContainer.style.display = 'none';
                }
            });

            document.getElementById('add-custom-unit').addEventListener('click', function () {
                const container = document.getElementById('custom-units-container');
                const div = document.createElement('div');
                div.className = 'input-group mt-2 fade-in';

                const input = document.createElement('input');
                input.type = 'text';
                input.name = 'custom_units[]';
                input.classList.add('form-control');
                input.placeholder = 'Nama Unit Baru';

                const appendDiv = document.createElement('div');
                appendDiv.className = 'input-group-append';

                const btnRemove = document.createElement('button');
                btnRemove.type = 'button';
                btnRemove.className = 'btn btn-outline-danger';
                btnRemove.innerHTML = '<i class="fas fa-times"></i>';
                btnRemove.onclick = function () {
                    div.remove();
                };

                appendDiv.appendChild(btnRemove);
                div.appendChild(input);
                div.appendChild(appendDiv);
                container.appendChild(div);
                input.focus();
            });

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Sedang Memproses...',
                    text: 'Mohon tunggu, jadwal sedang dibuat.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(this.action, {
                    method: this.method,
                    body: new FormData(this)
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Gagal. Ruangan mungkin sudah dipesan atau opsi tidak valid.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Jadwal berhasil dibuat.',
                            confirmButtonColor: '#d32f2f'
                        }).then(() => {
                            window.location.href = `/events/show/${data.message}`;
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: error.message,
                            confirmButtonColor: '#d32f2f'
                        });
                    });
            });

            const checkRoomAvailabilityBtn = document.getElementById('check-room-availability');
            checkRoomAvailabilityBtn.addEventListener('click', function () {
                const start = document.getElementById('start').value;
                const end = document.getElementById('end').value;
                const room = document.getElementById('room').value;
                const useZoom = document.getElementById('use_zoom').value;

                if (start && end) {
                    Swal.fire({
                        title: 'Memeriksa ketersediaan...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const data = {
                        start,
                        end,
                        room,
                        use_zoom: useZoom
                    };

                    fetch("{{ route('checkRoomAvailability') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close();
                            handleAvailabilityResponse(data.roomavailable);
                        })
                        .catch(error => {
                            Swal.close();
                            console.error('Error:', error);
                        });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Mohon isi tanggal mulai dan selesai terlebih dahulu.',
                        confirmButtonColor: '#d32f2f'
                    });
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const unitSearchInput = document.getElementById('unit-search');
            const userSearchInput = document.getElementById('user-search');

            unitSearchInput.addEventListener('keyup', function () {
                const filter = this.value.toLowerCase();
                const checkboxes = document.querySelectorAll('#unit-participants .unit-checkbox');
                checkboxes.forEach(checkbox => {
                    const label = checkbox.querySelector('.custom-control-label').textContent.toLowerCase();
                    checkbox.style.display = label.includes(filter) ? '' : 'none';
                });
            });

            userSearchInput.addEventListener('keyup', function () {
                const filter = this.value.toLowerCase();
                const checkboxes = document.querySelectorAll('#user-participants .user-checkbox');
                checkboxes.forEach(checkbox => {
                    const label = checkbox.querySelector('.custom-control-label').textContent.toLowerCase();
                    checkbox.style.display = label.includes(filter) ? '' : 'none';
                });
            });
        });
    </script>
@endpush

@push('scripts')
    <script>
        function handleAvailabilityResponse(roomAvailable) {
            const btnColor = '#d32f2f';
            switch (roomAvailable) {
                case "truetrue":
                    Swal.fire({ icon: 'success', title: 'Tersedia', text: 'Ruangan dan Zoom tersedia!', confirmButtonColor: btnColor });
                    break;
                case "truefalse":
                    Swal.fire({ icon: 'warning', title: 'Zoom Penuh', text: 'Ruangan tersedia, namun slot Zoom tidak tersedia.', confirmButtonColor: btnColor });
                    break;
                case "falsetrue":
                    Swal.fire({ icon: 'warning', title: 'Ruangan Penuh', text: 'Ruangan tidak tersedia, namun Zoom tersedia.', confirmButtonColor: btnColor });
                    break;
                case "falsefalse":
                    Swal.fire({ icon: 'error', title: 'Penuh', text: 'Ruangan dan Zoom tidak tersedia pada jam tersebut.', confirmButtonColor: btnColor });
                    break;
                case "zerotrue":
                    Swal.fire({ icon: 'success', title: 'Tersedia', text: 'Slot Zoom tersedia.', confirmButtonColor: btnColor });
                    break;
                case "zerofalse":
                    Swal.fire({ icon: 'error', title: 'Penuh', text: 'Slot Zoom tidak tersedia.', confirmButtonColor: btnColor });
                    break;
                case "truezero":
                    Swal.fire({ icon: 'success', title: 'Tersedia', text: 'Ruangan tersedia.', confirmButtonColor: btnColor });
                    break;
                case "falsezero":
                    Swal.fire({ icon: 'error', title: 'Penuh', text: 'Ruangan sudah dipesan pada jam tersebut.', confirmButtonColor: btnColor });
                    break;
                case "endearlierstart":
                    Swal.fire({ icon: 'error', title: 'Waktu Tidak Valid', text: 'Waktu selesai harus lebih akhir dari waktu mulai.', confirmButtonColor: btnColor });
                    break;
                default:
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan validasi data.', confirmButtonColor: btnColor });
                    break;
            }
        }
    </script>
@endpush