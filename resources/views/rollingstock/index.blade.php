@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="#">Rollingstock Specifications</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card card-danger card-outline">
                    <div class="card-header">
                        <h3 class="card-title text-bold">Rollingstock Specifications</h3>
                    </div>
                    <div class="card-body">

                        <button class="btn btn-primary mb-3" id="btn-add">Tambah Data</button>
                        <button id="createProjectType" class="btn btn-primary mb-3">Tambah Project</button>

                        <table class="table table-bordered" id="rollingstock-table">
                            <thead>
                                <tr>
                                    <th>#</th>

                                    <th>Project Type</th>
                                    <th>Rollingstock Type</th>
                                    <th>Designation</th>

                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rollingstockSpecs as $key => $spec)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>

                                        <td>{{ $spec->projectType->title }}</td>
                                        <td>{{ $spec->rollingstockType->name }}</td>
                                        <td>{{ $spec->rollingstockDesignation->name }}</td>
                                        <td>
                                            <button class="btn btn-warning btn-edit" data-id="{{ $spec->id }}"
                                                data-climate="{{ $spec->climate }}"
                                                data-type="{{ $spec->rollingstock_type_id }}"
                                                data-designation="{{ $spec->rollingstock_designation_id }}"
                                                data-project="{{ $spec->proyek_type_id }}">Edit</button>

                                            @if (auth()->user()->id == 1)
                                                <button class="btn btn-danger btn-delete"
                                                    data-id="{{ $spec->id }}">Hapus</button>
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
@endsection

@push('scripts')
    {{-- AJAX dan SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {



            $('#createProjectType').on('click', function() {
                Swal.fire({
                    title: 'Create Project Type',
                    html: '<input id="swal-title" class="swal2-input" placeholder="Enter Title">',
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    preConfirm: () => {
                        let title = document.getElementById('swal-title').value.trim();

                        if (!title) {
                            Swal.showValidationMessage('Title is required!');
                            return false;
                        }

                        return fetch("{{ route('project_types.store') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    title: title
                                })
                            })
                            .then(response => {
                                if (!response.ok) {
                                    return response.json().then(err => Promise.reject(err));
                                }
                                return response.json();
                            })
                            .then(data => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: data.message
                                }).then(() => {
                                    location
                                        .reload(); // Reload halaman setelah sukses
                                });
                            })
                            .catch(error => {
                                let errorMessage = 'Something went wrong!';
                                if (error.errors) {
                                    errorMessage = Object.values(error.errors).join(' ');
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: errorMessage
                                });
                            });
                    }
                });
            });

            $('#btn-add').click(function() {
                showRollingStockForm(); // Menampilkan form dengan fungsi utama
            });

            function showRollingStockForm(data = {}) {
                let rollingstockTypes = @json($rollingstockTypes);
                let rollingstockDesignations = @json($rollingstockDesignations);
                let projectTypes = @json($projectTypes);

                let rollingstockTypeOptions = generateSelectOptions(rollingstockTypes, 'rollingstock_type', data
                    .rollingstock_type_id);
                let rollingstockDesignationOptions = generateSelectOptions(rollingstockDesignations,
                    'rollingstock_designation', data.rollingstock_designation_id);
                let projectTypeOptions = generateSelectOptions(projectTypes, 'proyek_type', data.proyek_type_id);

                Swal.fire({
                    title: 'Tambah Data Rolling Stock',
                    customClass: {
                        popup: 'large-swal'
                    },
                    width: '70%',
                    html: `
                                                                                        <div class="form-container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                                                                        <!-- Kolom 1 -->
                                                                                        <div>
                                                                                        <h3>Spesifikasi Iklim</h3>
                                                                                        ${generateInput('climate', 'Climate', data.climate)}
                                                                                        ${generateInput('average_temperature', 'Avg Temperature (degree Celcius)', data.average_temperature)}
                                                                                        ${generateInput('lowest_temperature', 'Lowest Temperature (degree Celcius)', data.lowest_temperature)}
                                                                                        ${generateInput('highest_temperature', 'Highest Temperature (degree Celcius)', data.highest_temperature)}
                                                                                        ${generateInput('highest_operating_altitude', 'Highest Operating Altitude (meter)', data.highest_operating_altitude)}
                                                                                        ${generateInput('minimum_horizontal_curve_radius', 'Min Horizontal Curve Radius (meter)', data.minimum_horizontal_curve_radius)}
                                                                                        ${generateInput('maximum_sustained_gradient_at_main_line', 'Max Sustained Gradient At Main Line (%)', data.maximum_sustained_gradient_at_main_line)}
                                                                                        ${generateInput('maximum_sustained_gradient_at_depot', 'Max Sustained Gradient At Depot (%)', data.maximum_sustained_gradient_at_depot)}

                                                                                        <h3>Dimensi & Kapasitas</h3>
                                                                                        ${generateInput('load_capacity', 'Load Capacity (Ton or Person)', data.load_capacity)}
                                                                                        ${generateInput('track_gauge', 'Track Gauge (mm)', data.track_gauge)}
                                                                                        ${generateInput('max_height_of_rollingstock', 'Max Height (mm)', data.max_height_of_rollingstock)}
                                                                                        ${generateInput('max_width_of_rollingstock', 'Max Width (mm)', data.max_width_of_rollingstock)}
                                                                                        ${generateInput('wheel_diameter', 'Wheel Diameter (mm)', data.wheel_diameter)}
                                                                                        </div>

                                                                                        <!-- Kolom 2 -->
                                                                                        <div>
                                                                                        <h3>Rolling Stock</h3>
                                                                                        ${rollingstockTypeOptions}
                                                                                        ${rollingstockDesignationOptions}
                                                                                        ${generateInput('axle_load_of_rollingstock', 'Axle Load (Ton)', data.axle_load_of_rollingstock)}

                                                                                        <h3>Dimensi Tambahan</h3>
                                                                                        ${generateInput('max_length_of_rollingstock_include_coupler', 'Max Length Coupler (mm)', data.max_length_of_rollingstock_include_coupler)}
                                                                                        ${generateInput('coupler_height', 'Coupler Height (mm)', data.coupler_height)}
                                                                                        ${generateInput('coupler_type', 'Coupler Type', data.coupler_type)}

                                                                                        ${generateInput('distance_between_bogie_centers', 'Distance Between Bogie Centers (mm)', data.distance_between_bogie_centers)}
                                                                                        ${generateInput('distance_between_axle', 'Distance Between Axle (mm)', data.distance_between_axle)}
                                                                                        ${generateInput('floor_height_from_top_of_rail', 'Floor Height from Top of Rail (mm)', data.floor_height_from_top_of_rail)}
                                                                                            ${generateInput('maximum_design_speed', 'Maximum Design Speed (km/h)', data.maximum_design_speed)}
                                                                                            ${generateInput('maximum_operation_speed', 'Maximum Operation Speed (km/h)', data.maximum_operation_speed)}
                                                                                            ${generateInput('acceleration_rate', 'Acceleration Rate (m/s^2)', data.acceleration_rate)}
                                                                                            ${generateInput('minimum_deceleration_rate', 'Minimum Deceleration Rate (m/s^2)', data.minimum_deceleration_rate)}
                                                                                            ${generateInput('minimum_emergency_deceleration', 'Minimum Emergency Deceleration (m/s^2)', data.minimum_emergency_deceleration)}
                                                                                            ${generateInput('bogie_type', 'Bogie Type', data.bogie_type)}
                                                                                            ${generateInput('brake_system', 'Brake System', data.brake_system)}
                                                                                            ${generateInput('propulsion_system', 'Propulsion System', data.propulsion_system)}
                                                                                            ${generateInput('suspension_system', 'Suspension System', data.suspension_system)}
                                                                                            ${generateInput('carbody_material', 'Carbody Material', data.carbody_material)}
                                                                                            ${generateInput('air_conditioning_system', 'Air Conditioning System', data.air_conditioning_system)}
                                                                                            ${generateInput('other_requirements', 'Other Requirements', data.other_requirements)}




                                                                                                                                                                                                            <h3>Proyek</h3>
                                                                                                                                                                                                            ${projectTypeOptions}
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                `,

                    showCancelButton: true,
                    confirmButtonText: 'Simpan',
                    preConfirm: () => {
                        return {
                            climate: $('#climate').val(),
                            minimum_horizontal_curve_radius: $('#minimum_horizontal_curve_radius')
                                .val(),
                            maximum_sustained_gradient_at_main_line: $(
                                '#maximum_sustained_gradient_at_main_line').val(),
                            maximum_sustained_gradient_at_depot: $(
                                '#maximum_sustained_gradient_at_depot').val(),

                            distance_between_bogie_centers: $('#distance_between_bogie_centers').val(),

                            distance_between_axle: $('#distance_between_axle').val(),
                            wheel_diameter: $('#wheel_diameter').val(),
                            floor_height_from_top_of_rail: $('#floor_height_from_top_of_rail').val(),
                            maximum_design_speed: $('#maximum_design_speed').val(),
                            maximum_operation_speed: $('#maximum_operation_speed').val(),
                            acceleration_rate: $('#acceleration_rate').val(),
                            minimum_deceleration_rate: $('#minimum_deceleration_rate').val(),
                            minimum_emergency_deceleration: $('#minimum_emergency_deceleration').val(),
                            bogie_type: $('#bogie_type').val(),
                            brake_system: $('#brake_system').val(),
                            propulsion_system: $('#propulsion_system').val(),
                            suspension_system: $('#suspension_system').val(),
                            carbody_material: $('#carbody_material').val(),
                            air_conditioning_system: $('#air_conditioning_system').val(),
                            other_requirements: $('#other_requirements').val(),






                            average_temperature: $('#average_temperature').val(),
                            lowest_temperature: $('#lowest_temperature').val(),
                            highest_temperature: $('#highest_temperature').val(),
                            highest_operating_altitude: $('#highest_operating_altitude').val(),
                            rollingstock_type_id: $('#rollingstock_type').val(),
                            rollingstock_designation_id: $('#rollingstock_designation').val(),
                            axle_load_of_rollingstock: $('#axle_load_of_rollingstock').val(),
                            load_capacity: $('#load_capacity').val(),
                            track_gauge: $('#track_gauge').val(),
                            max_height_of_rollingstock: $('#max_height_of_rollingstock').val(),
                            max_width_of_rollingstock: $('#max_width_of_rollingstock').val(),
                            max_length_of_rollingstock_include_coupler: $(
                                '#max_length_of_rollingstock_include_coupler').val(),
                            coupler_height: $('#coupler_height').val(),
                            coupler_type: $('#coupler_type').val(),
                            proyek_type_id: $('#proyek_type').val()
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menyimpan...',
                            allowOutsideClick: false
                        });
                        Swal.showLoading();
                        $.ajax({
                            url: "{{ route('rollingstock.store') }}",
                            type: "POST",
                            data: {
                                ...result.value,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire('Sukses!', response.message, 'success').then(() =>
                                    location.reload());
                            },
                            error: function(xhr) {
                                let errorMessage = xhr.responseJSON?.message || xhr
                                    .responseText || 'Terjadi kesalahan.';
                                Swal.fire({
                                        title: 'Gagal!',
                                        text: errorMessage,
                                        icon: 'error',
                                        confirmButtonText: 'Perbaiki'
                                    })
                                    .then(() => showRollingStockForm(result.value));
                            }
                        });
                    }
                });
            }

            // Fungsi untuk membuat input field
            function generateInput(id, label, value = '') {
                return `
                                                                                                                                                                                                <div class="input-group" style="margin-bottom: 10px;">
                                                                                                                                                                                                    <label style="display: block; font-weight: bold;">${label}:</label>
                                                                                                                                                                                                    <input id="${id}" class="swal2-input full-width" style="width: 100%;" placeholder="${label}" value="${value}">
                                                                                                                                                                                                </div>
                                                                                                                                                                                            `;
            }

            // Fungsi untuk membuat select option
            function generateSelectOptions(options, id, selectedValue) {
                let select =
                    `<div class="input-group" style="margin-bottom: 10px;">
                                                                                                                                                                                                            <label style="display: block; font-weight: bold;">${id.replace('_', ' ')}:</label>
                                                                                                                                                                                                            <select id="${id}" class="swal2-input full-width" style="width: 100%;">`;
                options.forEach(option => {
                    let selected = selectedValue == option.id ? 'selected' : '';
                    select +=
                        `<option value="${option.id}" ${selected}>${option.name || option.title}</option>`;
                });
                select += `</select></div>`;
                return select;
            }




            // Fungsi untuk edit
            $('.btn-edit').click(function() {
                let id = $(this).data('id');
                let rollingstockTypes = @json($rollingstockTypes);
                let rollingstockDesignations = @json($rollingstockDesignations);
                let projectTypes = @json($projectTypes);

                $.ajax({
                    url: `/rollingstock/get/${id}`,
                    type: "GET",
                    success: function(response) {
                        let data = response.data;

                        Swal.fire({
                            title: 'Edit Data Rollingstock',
                            customClass: {
                                popup: 'large-swal'
                            },
                            width: '70%',
                            html: generateEditForm(data, rollingstockTypes,
                                rollingstockDesignations, projectTypes),
                            showCancelButton: true,
                            confirmButtonText: 'Update',
                            preConfirm: collectFormData
                        }).then((result) => {
                            if (result.isConfirmed) {
                                updateRollingStock(id, result.value);
                            }
                        });
                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal mengambil data.', 'error');
                    }
                });
            });

            function generateEditForm(data, rollingstockTypes, rollingstockDesignations, projectTypes) {
                // Generate HTML for existing images
                let filesHtml = '';
                if (data.files && data.files.length > 0) {
                    filesHtml =
                        '<h3>Existing Images</h3><div class="image-container" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 15px;">';
                    data.files.forEach(file => {
                        // Construct image URL (adjust base URL as needed)
                        const imageUrl = `/storage/${file.link}`;
                        filesHtml += `
                <div class="image-item" style="position: relative; width: 150px; height: 150px;">
                    <img src="${imageUrl}" alt="${file.filename}" style="width: 100%; height: 100%; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">
                    <button class="btn btn-danger btn-sm btn-delete-file" data-id="${file.id}" style="position: absolute; top: 5px; right: 5px; padding: 2px 6px;">X</button>
                </div>
            `;
                    });
                    filesHtml += '</div>';
                } else {
                    filesHtml = '<h3>Existing Images</h3><p>No images uploaded.</p>';
                }

                return `
        <div class="form-container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div>
                <h3>Spesifikasi Iklim</h3>
                ${generateInputField('edit-climate', 'Climate', data.climate)}
                ${generateInputField('edit-average-temperature', 'Avg Temperature (degree Celcius)', data.average_temperature)}
                ${generateInputField('edit-lowest-temperature', 'Lowest Temperature (degree Celcius)', data.lowest_temperature)}
                ${generateInputField('edit-highest-temperature', 'Highest Temperature (degree Celcius)', data.highest_temperature)}
                ${generateInputField('edit-highest-operating-altitude', 'Highest Operating Altitude (meter)', data.highest_operating_altitude)}
                ${generateInputField('edit-minimum_horizontal_curve_radius', 'Min Horizontal Curve Radius (meter)', data.minimum_horizontal_curve_radius)}
                ${generateInputField('edit-maximum_sustained_gradient_at_main_line', 'Max Sustained Gradient At Main Line (%)', data.maximum_sustained_gradient_at_main_line)}
                ${generateInputField('edit-maximum_sustained_gradient_at_depot', 'Max Sustained Gradient At Depot (%)', data.maximum_sustained_gradient_at_depot)}

                <h3>Dimensi & Kapasitas</h3>
                ${generateInputField('edit-load-capacity', 'Load Capacity (Ton or Person)', data.load_capacity)}
                ${generateInputField('edit-track-gauge', 'Track Gauge (mm)', data.track_gauge)}
                ${generateInputField('edit-max_height_of_rollingstock', 'Max Height (mm)', data.max_height_of_rollingstock)}
                ${generateInputField('edit-max_width_of_rollingstock', 'Max Width (mm)', data.max_width_of_rollingstock)}
                ${generateInputField('edit-wheel_diameter', 'Wheel Diameter (mm)', data.wheel_diameter)}
            </div>
            <div>
                <h3>Rolling Stock</h3>
                ${generateSelectField('edit-rollingstock-type', rollingstockTypes, data.rollingstock_type_id)}
                ${generateSelectField('edit-rollingstock-designation', rollingstockDesignations, data.rollingstock_designation_id)}
                ${generateInputField('edit-axle-load', 'Axle Load (Ton)', data.axle_load_of_rollingstock)}

                <h3>Dimensi Tambahan</h3>
                ${generateInputField('edit-max-length-of-rollingstock-include-coupler', 'Max Length Coupler (mm)', data.max_length_of_rollingstock_include_coupler, 'number')}
                ${generateInputField('edit-coupler-height', 'Coupler Height (mm)', data.coupler_height, 'number')}
                ${generateInputField('edit-coupler-type', 'Coupler Type', data.coupler_type, 'text')}
                ${generateInputField('edit-distance-between-bogie-centers', 'Distance Between Bogie Centers (mm)', data.distance_between_bogie_centers, 'number')}
                ${generateInputField('edit-distance-between-axle', 'Distance Between Axle (mm)', data.distance_between_axle, 'number')}
                ${generateInputField('edit-floor-height-from-top-of-rail', 'Floor Height from Top of Rail (mm)', data.floor_height_from_top_of_rail, 'number')}
                ${generateInputField('edit-maximum-design-speed', 'Maximum Design Speed (km/h)', data.maximum_design_speed, 'number')}
                ${generateInputField('edit-maximum-operation-speed', 'Maximum Operation Speed (km/h)', data.maximum_operation_speed, 'number')}
                ${generateInputField('edit-acceleration-rate', 'Acceleration Rate (m/s^2)', data.acceleration_rate, 'number')}
                ${generateInputField('edit-minimum-deceleration-rate', 'Minimum Deceleration Rate (m/s^2)', data.minimum_deceleration_rate, 'number')}
                ${generateInputField('edit-minimum-emergency-deceleration', 'Minimum Emergency Deceleration (m/s^2)', data.minimum_emergency_deceleration, 'number')}
                ${generateInputField('edit-bogie-type', 'Bogie Type', data.bogie_type, 'text')}
                ${generateInputField('edit-brake-system', 'Brake System', data.brake_system, 'text')}
                ${generateInputField('edit-propulsion-system', 'Propulsion System', data.propulsion_system, 'text')}
                ${generateInputField('edit-suspension-system', 'Suspension System', data.suspension_system, 'text')}
                ${generateInputField('edit-carbody-material', 'Carbody Material', data.carbody_material, 'text')}
                ${generateInputField('edit-air-conditioning-system', 'Air Conditioning System', data.air_conditioning_system, 'text')}
                ${generateInputField('edit-other-requirements', 'Other Requirements', data.other_requirements, 'text')}

                <h3>Upload Gambar</h3>
                <div class="input-group" style="margin-bottom: 10px;">
                    <label style="display: block; font-weight: bold;">Upload Files (jpg, jpeg, png):</label>
                    <input type="file" id="edit-files" class="swal2-file" multiple accept=".jpg,.jpeg,.png" style="width: 100%;">
                </div>

                ${filesHtml}

                <h3>Proyek</h3>
                ${generateSelectField('edit-proyek-type', projectTypes, data.proyek_type_id)}
            </div>
        </div>
    `;
            }

            // Add event handler for deleting images
            $(document).on('click', '.btn-delete-file', function() {
                let fileId = $(this).data('id');
                Swal.fire({
                    title: 'Yakin hapus gambar?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/rollingstock/file/delete/${fileId}`,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire('Dihapus!', response.message, 'success').then(
                                    () => {
                                        // Refresh the form by re-fetching data
                                        let id = $('.btn-edit').data('id');
                                        $.ajax({
                                            url: `/rollingstock/get/${id}`,
                                            type: "GET",
                                            success: function(response) {
                                                Swal.fire({
                                                    title: 'Edit Data Rollingstock',
                                                    customClass: {
                                                        popup: 'large-swal'
                                                    },
                                                    width: '70%',
                                                    html: generateEditForm(
                                                        response
                                                        .data,
                                                        rollingstockTypes,
                                                        rollingstockDesignations,
                                                        projectTypes
                                                    ),
                                                    showCancelButton: true,
                                                    confirmButtonText: 'Update',
                                                    preConfirm: collectFormData
                                                }).then((
                                                    result) => {
                                                    if (result
                                                        .isConfirmed
                                                    ) {
                                                        updateRollingStock
                                                            (id, result
                                                                .value
                                                            );
                                                    }
                                                });
                                            }
                                        });
                                    });
                            },
                            error: function(xhr) {
                                Swal.fire('Error!', xhr.responseJSON.message, 'error');
                            }
                        });
                    }
                });
            });

            function generateInputField(id, placeholder, value) {
                return `
                                                                                                <div class="input-group" style="margin-bottom: 10px;">
                                                                                                    <label style="display: block; font-weight: bold;">${placeholder}:</label>
                                                                                                    <input id="${id}" class="swal2-input full-width" style="width: 100%;" placeholder="${placeholder}" value="${value}">
                                                                                                </div>
                                                                                            `;
            }

            function generateSelectField(id, options, selectedValue) {
                let select =
                    `<div class="input-group" style="margin-bottom: 10px;">
                                                                                                                                                                                                            <label style="display: block; font-weight: bold;">${id.replace('_', ' ')}:</label>
                                                                                                                                                                                                            <select id="${id}" class="swal2-input full-width" style="width: 100%;">`;
                options.forEach(option => {
                    let selected = selectedValue == option.id ? 'selected' : '';
                    select +=
                        `<option value="${option.id}" ${selected}>${option.name || option.title}</option>`;
                });
                select += `</select></div>`;
                return select;
            }

            function collectFormData() {
                let formData = new FormData();
                formData.append('climate', $('#edit-climate').val());
                formData.append('minimum_horizontal_curve_radius', $('#edit-minimum_horizontal_curve_radius')
                    .val());
                formData.append('maximum_sustained_gradient_at_main_line', $(
                    '#edit-maximum_sustained_gradient_at_main_line').val());
                formData.append('maximum_sustained_gradient_at_depot', $(
                    '#edit-maximum_sustained_gradient_at_depot').val());
                formData.append('max_height_of_rollingstock', $('#edit-max_height_of_rollingstock').val());
                formData.append('max_width_of_rollingstock', $('#edit-max_width_of_rollingstock').val());
                formData.append('wheel_diameter', $('#edit-wheel_diameter').val());
                formData.append('max_length_of_rollingstock_include_coupler', $(
                    '#edit-max-length-of-rollingstock-include-coupler').val());
                formData.append('coupler_height', $('#edit-coupler-height').val());
                formData.append('coupler_type', $('#edit-coupler-type').val());
                formData.append('distance_between_bogie_centers', $('#edit-distance-between-bogie-centers').val());
                formData.append('distance_between_axle', $('#edit-distance-between-axle').val());
                formData.append('floor_height_from_top_of_rail', $('#edit-floor-height-from-top-of-rail').val());
                formData.append('maximum_design_speed', $('#edit-maximum-design-speed').val());
                formData.append('maximum_operation_speed', $('#edit-maximum-operation-speed').val());
                formData.append('acceleration_rate', $('#edit-acceleration-rate').val());
                formData.append('minimum_deceleration_rate', $('#edit-minimum-deceleration-rate').val());
                formData.append('minimum_emergency_deceleration', $('#edit-minimum-emergency-deceleration').val());
                formData.append('bogie_type', $('#edit-bogie-type').val());
                formData.append('brake_system', $('#edit-brake-system').val());
                formData.append('propulsion_system', $('#edit-propulsion-system').val());
                formData.append('suspension_system', $('#edit-suspension-system').val());
                formData.append('carbody_material', $('#edit-carbody-material').val());
                formData.append('air_conditioning_system', $('#edit-air-conditioning-system').val());
                formData.append('other_requirements', $('#edit-other-requirements').val());
                formData.append('rollingstock_type_id', $('#edit-rollingstock-type').val());
                formData.append('rollingstock_designation_id', $('#edit-rollingstock-designation').val());
                formData.append('proyek_type_id', $('#edit-proyek-type').val());
                formData.append('average_temperature', $('#edit-average-temperature').val());
                formData.append('lowest_temperature', $('#edit-lowest-temperature').val());
                formData.append('highest_temperature', $('#edit-highest-temperature').val());
                formData.append('highest_operating_altitude', $('#edit-highest-operating-altitude').val());
                formData.append('axle_load_of_rollingstock', $('#edit-axle-load').val());
                formData.append('load_capacity', $('#edit-load-capacity').val());
                formData.append('track_gauge', $('#edit-track-gauge').val());
                formData.append('_token', "{{ csrf_token() }}");

                // Append files
                let files = $('#edit-files')[0].files;
                for (let i = 0; i < files.length; i++) {
                    formData.append('file[]', files[i]);
                }

                return formData;
            }

            function updateRollingStock(id, formData) {
                $.ajax({
                    url: `/rollingstock/update/${id}`,
                    type: "POST", // Use POST since FormData with files typically requires POST
                    data: formData,
                    processData: false, // Prevent jQuery from processing FormData
                    contentType: false, // Prevent jQuery from setting contentType
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'X-HTTP-Method-Override': 'PUT' // Override to PUT for Laravel routing
                    },
                    success: function(response) {
                        Swal.fire('Updated!', response.message, 'success')
                            .then(() => location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON.message, 'error');
                    }
                });
            }


            // Hapus Data
            $('.btn-delete').click(function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin hapus?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/rollingstock/delete/" + id,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire('Dihapus!', response.success, 'success')
                                    .then(() => location.reload());
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush

@push('style')
    <style>
        .large-swal {
            width: 800px !important;
            /* Lebar yang lebih besar */
            height: auto !important;
            /* Tinggi menyesuaikan isi */
            max-height: 90vh !important;
            /* Batasi agar tidak terlalu tinggi */
            overflow-y: auto !important;
            /* Tambahkan scroll jika isinya terlalu banyak */
        }

        .image-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }

        .image-item {
            position: relative;
            width: 150px;
            height: 150px;
        }

        .image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .image-item button {
            position: absolute;
            top: 5px;
            right: 5px;
            padding: 2px 6px;
            font-size: 12px;
        }
    </style>
@endpush
