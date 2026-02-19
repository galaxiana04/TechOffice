<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kejadian Kegagalan Komponen</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Roboto:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f1f3f4;
            font-family: 'Roboto', sans-serif;
            color: #202124;
        }

        .gform-wrapper {
            min-height: 100vh;
            background: linear-gradient(to bottom, #f8f9fa, #e8eaed);
            padding: 32px 16px;
        }

        .gform-card {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
            overflow: hidden;
        }

        .gform-header {
            background: linear-gradient(135deg, #673ab7, #9c27b0);
            padding: 48px 48px 32px;
            color: white;
        }

        .gform-header h1 {
            font-family: 'Google Sans', sans-serif;
            font-size: 2.1rem;
            font-weight: 500;
            margin: 0 0 8px;
        }

        .gform-header p {
            font-size: 1rem;
            opacity: 0.92;
            margin: 0;
        }

        .gform-body {
            padding: 40px 48px;
        }

        .gform-description {
            font-size: 0.95rem;
            color: #5f6368;
            line-height: 1.6;
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 1px solid #dadce0;
        }

        .gform-section {
            margin-bottom: 36px;
        }

        .gform-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: #202124;
            margin-bottom: 6px;
            display: block;
            transition: opacity 0.25s ease;
        }

        .gform-required {
            color: #d93025;
        }

        .gform-input,
        .gform-textarea {
            width: 100%;
            padding: 12px 16px;
            font-size: 1rem;
            border: 1px solid #dadce0;
            border-radius: 6px;
            background: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .gform-input:focus,
        .gform-textarea:focus {
            border-color: #673ab7;
            box-shadow: 0 0 0 3px rgba(103, 58, 183, 0.15);
            outline: none;
        }

        .gform-row {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
        }

        .gform-col {
            flex: 1;
            min-width: 260px;
        }

        /* Radio styling */
        .radio-group {
            display: flex;
            gap: 32px;
            margin-top: 8px;
        }

        .radio-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 0.95rem;
            position: relative;
        }

        .radio-label input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .radio-label .radio-dot {
            width: 18px;
            height: 18px;
            border: 2px solid #9e9e9e;
            border-radius: 50%;
            position: relative;
            transition: all 0.2s;
        }

        .radio-label input:checked+.radio-dot {
            border-color: #673ab7;
        }

        .radio-label input:checked+.radio-dot::after {
            content: '';
            width: 10px;
            height: 10px;
            background: #673ab7;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .gform-actions {
            display: flex;
            justify-content: flex-end;
            gap: 16px;
            margin-top: 48px;
            padding-top: 24px;
            border-top: 1px solid #dadce0;
        }

        .gform-btn {
            padding: 12px 28px;
            font-size: 0.95rem;
            font-weight: 500;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .gform-btn-primary {
            background: #673ab7;
            color: white;
            border: none;
        }

        .gform-btn-primary:hover {
            background: #512da8;
            transform: translateY(-1px);
        }

        input[list] {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%236c757d' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 12px;
        }
    </style>
</head>

<body>
    <div class="gform-wrapper">
        <div class="gform-card">
            <div class="gform-header">
                <h1>Tambah Kejadian Kegagalan</h1>
                <p>Pencatatan data untuk analisis Weibull</p>
            </div>

            <div class="gform-body">
                <div class="gform-description">
                    Isi data dengan lengkap. Jika nama komponen belum ada di sistem, ketik saja — akan otomatis
                    ditambahkan.
                </div>

                <form action="{{ route('weibull.store') }}" method="POST">
                    @csrf
                    <div class="gform-section">
                        <label class="gform-label">Project Operation Profile <span
                                class="gform-required">*</span></label>
                        <select name="project_operation_profile_id" class="gform-input" required>
                            <option value="">-- Pilih Project --</option>
                            @foreach ($profiles as $profile)
                                <option value="{{ $profile->id }}"
                                    {{ old('project_operation_profile_id') == $profile->id ? 'selected' : '' }}>
                                    {{ $profile->projectType->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>



                    <div class="gform-row">
                        <div class="gform-col">
                            <label class="gform-label">L1 <span class="gform-required">*</span></label>
                            <input type="text" name="component_l1" class="gform-input" list="l1-options" required
                                placeholder="Contoh: AC System" value="{{ old('component_l1') }}">
                            <datalist id="l1-options">
                                @foreach ($l1s as $l1)
                                    <option value="{{ $l1 }}">
                                @endforeach
                            </datalist>
                        </div>
                        <div class="gform-col">
                            <label class="gform-label">L2</label>
                            <input type="text" name="component_l2" class="gform-input" list="l2-options"
                                placeholder="Contoh: AC Control" value="{{ old('component_l2') }}">
                            <datalist id="l2-options">
                                @foreach ($l2s as $l2)
                                    <option value="{{ $l2 }}">
                                @endforeach
                            </datalist>
                        </div>
                        <div class="gform-col">
                            <label class="gform-label">LRU</label>
                            <input type="text" name="component_l3" class="gform-input" list="lru-options"
                                placeholder="Contoh: PLC" value="{{ old('component_l3') }}">
                            <datalist id="lru-options">
                                @foreach ($l3s as $l3)
                                    <option value="{{ $l3 }}">
                                @endforeach
                            </datalist>
                        </div>
                        <div class="gform-col">
                            <label class="gform-label">L4</label>
                            <input type="text" name="component_l4" class="gform-input" list="l4-options"
                                placeholder="Contoh: Instalasi kabel" value="{{ old('component_l4') }}">
                            <datalist id="l4-options">
                                @foreach ($l4s as $l4)
                                    <option value="{{ $l4 }}">
                                @endforeach
                            </datalist>
                        </div>

                        <div class="gform-section">
                            <label class="gform-label">Tipe Komponen <span class="gform-required">*</span></label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="is_repairable" value="1" class="component-type"
                                        {{ old('is_repairable', '1') == '1' ? 'checked' : '' }}>
                                    <span class="radio-dot"></span>
                                    Repairable (MTBF)
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="is_repairable" value="0" class="component-type"
                                        {{ old('is_repairable') == '0' ? 'checked' : '' }}>
                                    <span class="radio-dot"></span>
                                    Non-Repairable (MTTF)
                                </label>
                            </div>
                            <small style="color:#5f6368; display:block; margin-top:8px;">
                                Repairable = dapat diperbaiki (contoh: pompa, motor)<br>
                                Non-repairable = sekali pakai (contoh: fuse, bearing)
                            </small>
                        </div>

                        <div class="gform-row">
                            <div class="gform-col">
                                <label class="gform-label gform-label-transition" id="start-date-label"
                                    title="Untuk repairable: tanggal setelah komponen diperbaiki dan kembali dipasang. Untuk non-repairable: tanggal pemasangan awal.">
                                    Tanggal Mulai Operasi <span class="gform-required">*</span>
                                </label>
                                <input type="text" name="start_date" class="gform-input flatpickr"
                                    id="start-date-input" placeholder="dd/mm/yyyy" required
                                    value="{{ old('start_date') }}">
                            </div>
                            <div class="gform-col">
                                <label class="gform-label">Tanggal Kegagalan <span
                                        class="gform-required">*</span></label>
                                <input type="text" name="failure_date" class="gform-input flatpickr"
                                    placeholder="dd/mm/yyyy" required value="{{ old('failure_date') }}">
                            </div>
                            <div class="gform-col">
                                <label class="gform-label">Waktu Kegagalan <span
                                        class="gform-required">*</span></label>
                                <input type="text" name="failure_time" class="gform-input flatpickr-time"
                                    placeholder="HH:MM" required value="{{ old('failure_time') }}">
                            </div>
                        </div>
                    </div>
                    <div class="gform-section">
                        <label class="gform-label">Jenis Service</label>
                        <input type="text" name="service_type" class="gform-input"
                            value="{{ old('service_type') }}">
                    </div>
                    <div class="gform-section">
                        <label class="gform-label">Apakah Komponen Baru? <span class="gform-required">*</span></label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="is_new" value="1" class="component-type"
                                    {{ old('is_new', '1') == '1' ? 'checked' : '' }}>
                                <span class="radio-dot"></span>
                                Komponen Baru
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="is_new" value="0" class="component-type"
                                    {{ old('is_new') == '0' ? 'checked' : '' }}>
                                <span class="radio-dot"></span>
                                Komponen Lama (MTTF)
                            </label>
                        </div>
                        <small style="color:#5f6368; display:block; margin-top:8px;">
                            Komponen Baru = komponen yang baru dipasang<br>
                            Komponen Lama = komponen yang sudah pernah dipakai sebelumnya
                        </small>
                    </div>
                    <div class="gform-section">
                        <label class="gform-label">Trainset</label>
                        <input type="text" name="trainset" class="gform-input" value="{{ old('trainset') }}">
                    </div>

                    <div class="gform-section">
                        <label class="gform-label">Train No</label>
                        <input type="text" name="train_no" class="gform-input" value="{{ old('train_no') }}">
                    </div>

                    <div class="gform-section">
                        <label class="gform-label">Car Type</label>
                        <input type="text" name="car_type" class="gform-input" value="{{ old('car_type') }}">
                    </div>

                    <div class="gform-section">
                        <label class="gform-label">Relation</label>
                        <input type="text" name="relation" class="gform-input" value="{{ old('relation') }}">
                    </div>

                    <div class="gform-section">
                        <label class="gform-label">Problem Description</label>
                        <textarea name="problemdescription" class="gform-textarea">{{ old('problemdescription') }}</textarea>
                    </div>

                    <div class="gform-section">
                        <label class="gform-label">Solution</label>
                        <textarea name="solution" class="gform-textarea">{{ old('solution') }}</textarea>
                    </div>

                    <div class="gform-section">
                        <label class="gform-label">Cause Classification</label>
                        <input type="text" name="cause_classification" class="gform-input"
                            value="{{ old('cause_classification') }}">
                    </div>

                    <div class="gform-actions">
                        <button type="submit" class="gform-btn gform-btn-primary">Simpan Data Kegagalan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        flatpickr(".flatpickr", {
            dateFormat: "d/m/Y",
            locale: {
                firstDayOfWeek: 1
            }
        });

        flatpickr(".flatpickr-time", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            minuteIncrement: 1
        });

        const startDateLabel = document.getElementById('start-date-label');
        const startDateInput = document.getElementById('start-date-input');
        const componentTypes = document.querySelectorAll('.component-type');

        function updateStartDateLabel(isRepairable) {
            startDateLabel.style.opacity = '0';
            setTimeout(() => {
                if (isRepairable === '1') {
                    startDateLabel.innerHTML =
                        'Tanggal Mulai Operasi (baru / setelah perbaikan) <span class="gform-required">*</span>';
                    startDateInput.placeholder = 'dd/mm/yyyy (setelah perbaikan)';
                } else {
                    startDateLabel.innerHTML =
                        'Tanggal Mulai Operasi (baru dipasang) <span class="gform-required">*</span>';
                    startDateInput.placeholder = 'dd/mm/yyyy (baru dipasang)';
                }
                startDateLabel.style.opacity = '1';
            }, 200);
        }

        updateStartDateLabel('{{ old('is_repairable', '1') }}');

        componentTypes.forEach(radio => {
            radio.addEventListener('change', () => {
                updateStartDateLabel(radio.value);
            });
        });

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 5000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                confirmButtonText: 'OK'
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                icon: 'warning',
                title: 'Periksa isian',
                html: '<ul style="text-align:left; margin:0 0 0 20px;">{!! implode('', $errors->all('<li>:message</li>')) !!}</ul>',
                confirmButtonText: 'OK'
            });
        @endif
    </script>
</body>

</html>
