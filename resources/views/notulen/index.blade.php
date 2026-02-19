@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="/">Notulen</a></li>
                        <li class="breadcrumb-item active text-bold">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card card-danger card-outline">
                <div class="card-header">
                    <h3>Notulen</h3>
                </div>
                <div class="card-body">

                    @if (auth()->id() == 1)
                        <div class="mb-3">
                            <button class="btn btn-success me-2" onclick="showAddAgendaModal()">Tambah Agenda</button>
                            <button class="btn btn-primary me-2" onclick="showAddNotulenModal()">Tambah Notulen</button>
                            <button class="btn btn-primary me-2" onclick="showAddTopicModal()">Tambah Topic</button>
                            <button class="btn btn-warning me-2" onclick="showAddIssueModal()">Tambah Issue</button>
                            <button class="btn btn-info me-2" onclick="showAddSolutionModal()">Tambah Solution</button>
                        </div>
                    @endif

                    <div class="dropdown mb-3">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Pilih Project Type
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" id="notulenDropdown">
                            @foreach ($projectTypes as $index => $projectType)
                                <li>
                                    <a class="dropdown-item {{ $index == 0 ? 'active' : '' }}"
                                        href="#content-{{ $projectType->id }}" data-bs-toggle="tab"
                                        data-id="{{ $projectType->id }}">
                                        {{ $projectType->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('notulen.upload') }}" class="btn btn-info ms-2">Upload Excel Notulen</a>
                    </div>

                    <div class="tab-content mt-3" id="notulenTabContent">
                        @foreach ($projectTypes as $index => $projectType)
                            <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}"
                                id="content-{{ $projectType->id }}" role="tabpanel">
                                <table class="table table-bordered my-2">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Nomor Notulen</th>
                                            <th>Tanggal</th>
                                            <th>Nama Agenda</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="notulen-body-{{ $projectType->id }}">
                                        <!-- Data akan dimuat melalui AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownItems = document.querySelectorAll('#notulenDropdown .dropdown-item');
            const currentUserId = "{{ auth()->id() }}"; // Ambil ID user yang login dari Laravel

            // Fungsi untuk memuat data notulen berdasarkan project type
            function loadNotulens(projectTypeId) {
                // Tampilkan loading dengan SweetAlert
                Swal.fire({
                    title: 'Mohon Tunggu',
                    text: 'Memuat data notulen...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                fetch(`/notulen/project-type/${projectTypeId}`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.close(); // Tutup loading Swal setelah data selesai diambil

                        const tbody = document.getElementById(`notulen-body-${projectTypeId}`);
                        tbody.innerHTML = ''; // Clear previous content

                        if (data.length > 0) {
                            data.forEach((notulen, index) => {
                                const status = calculateNotulenStatus(notulen);
                                const isOwner = notulen.user_id == currentUserId || currentUserId == 1;
                                const deleteButton = isOwner ? `
                                                                                        <button class="btn-sm bg-maroon delete-notulen" data-id="${notulen.id}">
                                                                                            Delete
                                                                                        </button>
                                                                                    ` : '';

                                const row = `
                                                                                        <tr>
                                                                                            <td>${index + 1}</td>
                                                                                            <td>${notulen.number || '-'}</td>
                                                                                            <td>${notulen.created_at ? new Date(notulen.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '-'}</td>
                                                                                            <td>
                                                                                                <u onclick='showNotulenModal(this)' data-notulen-id='${notulen.id}'>
                                                                                                    ${notulen.agenda_notulen?.name || '-'}
                                                                                                </u>
                                                                                            </td>
                                                                                            <td id="status-notulen-${notulen.id}">
                                                                                                <button class="btn btn-sm ${notulen.status === 'closed' ? 'btn-success' : 'btn-danger'}">
                                                                                                    ${notulen.status ? notulen.status.charAt(0).toUpperCase() + notulen.status.slice(1).toLowerCase() : '-'}
                                                                                                </button>
                                                                                                ${deleteButton}
                                                                                            </td>
                                                                                        </tr>
                                                                                    `;
                                tbody.insertAdjacentHTML('beforeend', row);
                            });
                        } else {
                            tbody.innerHTML =
                                '<tr><td colspan="5" class="text-center">Tidak ada data.</td></tr>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error!', 'Gagal memuat data notulen.', 'error');
                    });
            }


            // Fungsi untuk menghapus notulen
            function deleteNotulen(notulenId) {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Notulen ini akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/notulen/${notulenId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Deleted!', 'Notulen berhasil dihapus.', 'success');
                                    const activeItem = document.querySelector(
                                        '#notulenDropdown .dropdown-item.active');
                                    if (activeItem) {
                                        const projectTypeId = activeItem.getAttribute('data-id');
                                        loadNotulens(projectTypeId);
                                    }
                                } else {
                                    Swal.fire('Error!', data.message || 'Gagal menghapus notulen.',
                                        'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire('Error!', 'Gagal menghapus notulen.', 'error');
                            });
                    }
                });
            }

            // Event delegation untuk tombol delete
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('delete-notulen')) {
                    const notulenId = e.target.getAttribute('data-id');
                    deleteNotulen(notulenId);
                }
            });

            // Event listener untuk dropdown
            dropdownItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    dropdownItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');

                    const dropdownButton = document.getElementById('dropdownMenuButton');
                    dropdownButton.textContent = this.textContent;

                    const targetId = this.getAttribute('href');
                    const projectTypeId = this.getAttribute('data-id');
                    const allPanes = document.querySelectorAll('.tab-pane');

                    allPanes.forEach(pane => {
                        pane.classList.remove('show', 'active');
                    });

                    const targetPane = document.querySelector(targetId);
                    if (targetPane) {
                        targetPane.classList.add('show', 'active');
                        loadNotulens(projectTypeId);
                    }
                });
            });

            // Muat data untuk tab aktif pertama saat halaman dimuat
            const activeItem = document.querySelector('#notulenDropdown .dropdown-item.active');
            if (activeItem) {
                document.getElementById('dropdownMenuButton').textContent = activeItem.textContent;
                const projectTypeId = activeItem.getAttribute('data-id');
                loadNotulens(projectTypeId);
            }
        });

        function calculateNotulenStatus(notulen) {
            let allTopicsClosed = true;

            if (notulen.topicnotulens && notulen.topicnotulens.length > 0) {
                notulen.topicnotulens.forEach((topic) => {
                    let allIssuesClosed = true;
                    if (topic.issue_notulens && topic.issue_notulens.length > 0) {
                        topic.issue_notulens.forEach((issue) => {
                            let allSolutionsClosed = issue.solutions.every(solution => solution.status ===
                                'closed');
                            if (!allSolutionsClosed) allIssuesClosed = false;
                        });
                    }
                    if (!allIssuesClosed) allTopicsClosed = false;
                });
                return allTopicsClosed ? 'Closed' : 'Open';
            }
            return 'Open'; // Default jika tidak ada topik
        }
    </script>
    <script>
        // Fungsi untuk memformat tanggal
        function formatDate(dateString) {
            if (!dateString || dateString === "-") return "-";
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Bulan dimulai dari 0
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }

        function escapeHtml(text) {
            if (!text) return "-"; // Mengembalikan tanda "-" jika teks kosong atau undefined
            return text.replace(/\n/g, "<br>").replace(/\r\n/g, "<br>"); // Hanya ganti newline dengan <br>
        }

        function showNotulenModal(button) {
            const notulenId = button.getAttribute('data-notulen-id');

            // Show loading state
            Swal.fire({
                title: 'Memuat data...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Fetch data from the server
            fetch(`/notulen/show/${notulenId}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 404) {
                            throw new Error('Notulen tidak ditemukan.');
                        }
                        throw new Error('Terjadi kesalahan di server.');
                    }
                    return response.json();
                })
                .then(notulen => {
                    // Wrap single notulen object in an array for consistency with previous code
                    const data = [notulen];
                    console.log('Data fetched from server:', data); // Debugging

                    let content = `
                                                                            <div style="max-height: 80vh; min-height: 50vh; overflow-y: auto; padding: 10px;">
                                                                        `;

                    if (data.length > 0) {
                        data.forEach((notulen, index) => {
                            content += `
                                                                                    <div style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 8px; background: #f8f9fa;">
                                                                                        <h5 style="margin: 0 0 10px; text-align: center;">
                                                                                            <strong>${index + 1}. ${escapeHtml(notulen?.number)}</strong>
                                                                                        </h5>
                                                                                        <p style="margin: 5px 0; text-align: center;">
                                                                                            <strong>Tanggal Awal:</strong> ${formatDate(notulen?.notulen_time_start)}
                                                                                        </p>
                                                                                        <p style="margin: 5px 0; text-align: center;">
                                                                                            <strong>Tanggal Akhir:</strong> ${formatDate(notulen?.notulen_time_end)}
                                                                                        </p>
                                                                                        <p style="margin: 5px 0; text-align: center;">
                                                                                            <strong>Tempat:</strong> ${escapeHtml(notulen?.place)}
                                                                                        </p>
                                                                                        <div style="text-align: center; margin-top: 10px;">
                                                                                            <a href="/notulen/export/${notulen.id}" class="btn btn-primary btn-sm" target="_blank">
                                                                                                <i class="fas fa-download"></i> Download Excel
                                                                                            </a>
                                                                                        </div>
                                                                                `;

                            if (notulen.topicnotulens && notulen.topicnotulens.length > 0) {
                                content += `
                                                                                        <p style="margin: 10px 0 5px; text-align: left;"><strong>Topics:</strong></p>
                                                                                        <table style="width: 100%; border-collapse: collapse; margin-top: 10px; text-align: left;">
                                                                                            <thead>
                                                                                                <tr style="background: #e9ecef; border-bottom: 2px solid #ddd;">
                                                                                                    <th style="padding: 8px; border: 1px solid #ddd; width: 10%; text-align: left;">Topic</th>
                                                                                                    <th style="padding: 8px; border: 1px solid #ddd; width: 50%; text-align: left;">Bahasan</th>
                                                                                                    <th style="padding: 8px; border: 1px solid #ddd; width: 5%; text-align: left;">Status</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                    `;

                                notulen.topicnotulens.forEach((topic) => {
                                    let hasIssues = topic.issue_notulens && topic.issue_notulens
                                        .length > 0;
                                    if (!hasIssues) {
                                        content += `
                                                                                                <tr style="border-bottom: 1px solid #ddd;">
                                                                                                    <td style="padding: 8px; border: 1px solid #ddd;">${escapeHtml(topic.title)}</td>
                                                                                                    <td style="padding: 8px; border: 1px solid #ddd;"><span style="color: #999;">No Issues</span></td>
                                                                                                    <td style="padding: 8px; border: 1px solid #ddd;">
                                                                                                        <button class="btn btn-sm btn-danger" style="font-size: 16px;">Open</button>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            `;
                                    } else {
                                        topic.issue_notulens.forEach((issue, issueIndex) => {
                                            let encodedIssue = encodeURIComponent(JSON
                                                .stringify(issue));
                                            let encodedData = encodeURIComponent(JSON.stringify(
                                                data));
                                            content += `
                                                                                                    <tr style="border-bottom: 1px solid #ddd;">
                                                                                                        ${issueIndex === 0 ? `<td style="padding: 8px; border: 1px solid #ddd;" rowspan="${topic.issue_notulens.length}">${escapeHtml(topic.title)}</td>` : ""}
                                                                                                        <td style="padding: 8px; border: 1px solid #ddd;">
                                                                                                            <span style="cursor: pointer; color: rgb(0, 0, 0);" 
                                                                                                                  onclick="showIssueDetail('${encodedIssue}', '${encodedData}')">
                                                                                                                ${escapeHtml(issue.issue)}
                                                                                                            </span>
                                                                                                        </td>
                                                                                                        <td style="padding: 8px; border: 1px solid #ddd;">
                                                                                                            <button class="btn btn-sm ${issue.status === 'closed' ? 'btn-success' : 'btn-danger'}" style="font-size: 16px;">
                                                                                                                ${issue.status ? issue.status.charAt(0).toUpperCase() + issue.status.slice(1).toLowerCase() : '-'}
                                                                                                            </button>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                `;
                                        });
                                    }
                                });

                                content += `</tbody></table>`;
                            } else {
                                content += `<p style="color: #999; text-align: left;">No Topics Available.</p>`;
                            }

                            content += `</div>`;
                        });
                    } else {
                        content += `<p class="text-center" style="color: #999;">Tidak ada notulen.</p>`;
                    }

                    content += `</div>`;

                    let backButton = `
                                                                            <button id="back-btn" style="position: absolute; left: 10px; top: 10px; background: #6c757d; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;">Back</button>
                                                                        `;

                    Swal.fire({
                        title: "<h3 class='mb-0'>Daftar Notulen</h3>",
                        html: `
                                                                                <div style="position: relative;">
                                                                                    ${backButton}
                                                                                    ${content}
                                                                                </div>
                                                                            `,
                        width: '70vw',
                        heightAuto: false,
                        padding: '20px',
                        showCloseButton: true,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'swal-wide'
                        },
                        footer: `<button id="close-modal-btn" style="background: #dc3545; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer;">Tutup</button>`,
                        didRender: () => {
                            document.getElementById("close-modal-btn").addEventListener("click", () => Swal
                                .close());
                            document.getElementById("back-btn").addEventListener("click", () => Swal
                                .close());
                        }
                    });
                })
                .catch(error => {
                    console.error('Error fetching notulen:', error);
                    Swal.fire('Error!', error.message || 'Gagal memuat data notulen dari server.', 'error');
                });
        }


        // Contoh fungsi formatDate (jika belum ada)
        function formatDate(dateStr) {
            if (!dateStr) return "-";
            return new Date(dateStr).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });
        }


        function showIssueDetail(encodedIssue, encodedNotulenData) {
            const issue = JSON.parse(decodeURIComponent(encodedIssue));
            const notulenData = JSON.parse(decodeURIComponent(encodedNotulenData));
            let allSolutionsClosed = issue.solutions.every(solution => solution.status === 'closed');
            let issueStatus = allSolutionsClosed ? 'Closed' : 'Open';

            let solutionContent = "";
            if (issue.solutions && issue.solutions.length > 0) {
                solutionContent = `
                                                                                        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                                                                                            <thead>
                                                                                                <tr style="background: #e9ecef; border-bottom: 2px solid #ddd;">
                                                                                                    <th style="padding: 8px; border: 1px solid #ddd; width: 40%; text-align: left;">Tindak Lanjut</th>
                                                                                                    <th style="padding: 8px; border: 1px solid #ddd; width: 17%; text-align: left;">Pic</th>
                                                                                                    <th style="padding: 8px; border: 1px solid #ddd; width: 7%; text-align: left;">Status</th>
                                                                                                    <th style="padding: 8px; border: 1px solid #ddd; width: 6%; text-align: left;">Deadline</th>
                                                                                                    <th style="padding: 8px; border: 1px solid #ddd; width: 20%; text-align: left;">Update</th>
                                                                                                    <th style="padding: 8px; border: 1px solid #ddd; width: 10%; text-align: left;">Aksi</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                    `;

                issue.solutions.forEach(solution => {
                    const statusText = solution.status === 'open' ? 'Open' : 'Closed';
                    const buttonClass = solution.status === 'open' ? 'btn-danger' : 'btn-success';

                    solutionContent += `
                                                                                            <tr style="border-bottom: 1px solid #ddd;">
                                                                                                <td style="padding: 8px; border: 1px solid #ddd;" id="followup-text-${solution.id}">${escapeHtml(solution.followup)}</td>
                                                                                                <td style="padding: 8px; border: 1px solid #ddd;" id="pic-text-${solution.id}">${escapeHtml(solution.pic)}</td>
                                                                                                <td style="padding: 8px; border: 1px solid #ddd;">${statusText}</td>
                                                                                                <td style="padding: 8px; border: 1px solid #ddd;" id="deadline-text-${solution.id}">${escapeHtml(solution.formatted_deadlinedate)}</td>
                                                                                                <td style="padding: 8px; border: 1px solid #ddd;" id="update-text-${solution.id}">
                                                                                                    <span id="update-span-${solution.id}">${escapeHtml(solution.update || '-')}</span>
                                                                                                    <textarea id="update-input-${solution.id}" class="d-none" style="width: 100%;">${escapeHtml(solution.update || '')}</textarea>
                                                                                                </td>
                                                                                                <td style="padding: 8px; border: 1px solid #ddd;">
                                                                                                    <button class="btn btn-sm btn-warning" onclick="editSolution(${solution.id})">
                                                                                                        <i class="fas fa-pencil-alt"></i>
                                                                                                    </button>
                                                                                                    <button class="btn btn-sm btn-success d-none" id="save-btn-closed-${solution.id}" onclick="saveSolution(${solution.id}, '${encodeURIComponent(JSON.stringify(issue))}', '${encodeURIComponent(JSON.stringify(notulenData))}', 'closed')">
                                                                                                        <i class="fas fa-save"></i> Closed
                                                                                                    </button>
                                                                                                    <button class="btn btn-sm btn-danger d-none" id="save-btn-open-${solution.id}" onclick="saveSolution(${solution.id}, '${encodeURIComponent(JSON.stringify(issue))}', '${encodeURIComponent(JSON.stringify(notulenData))}', 'open')">
                                                                                                        <i class="fas fa-save"></i> Open
                                                                                                    </button>
                                                                                                    <span class="spinner-border spinner-border-sm d-none" id="loading-${solution.id}"></span>
                                                                                                </td>
                                                                                            </tr>
                                                                                        `;
                });

                solutionContent += `</tbody></table>`;
            } else {
                solutionContent = `<p style="color: #999; text-align: left;">Belum ada tindak lanjut.</p>`;
                issueStatus = 'Open';
            }

            let backButton = `
                                                                                    <button id="back-btn" style="position: absolute; left: 10px; top: 10px; background: #6c757d; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;">Back</button>
                                                                                `;

            Swal.fire({
                title: "Detail Issue",
                html: `
                                                                                        <div style="position: relative;">
                                                                                            ${backButton}
                                                                                            <div style="text-align: left; padding: 10px;">
                                                                                                <h5 style="margin-bottom: 10px;">Issue:</h5>
                                                                                                <p style="margin-bottom: 20px;">${escapeHtml(issue.issue)}</p>
                                                                                                <h5 style="margin-bottom: 10px;" id="issue-status">Status Issue: ${issueStatus}</h5>
                                                                                                ${solutionContent}
                                                                                            </div>
                                                                                        </div>
                                                                                    `,
                width: '70vw',
                heightAuto: false,
                padding: '20px',
                showCloseButton: true,
                showConfirmButton: false,
                customClass: {
                    popup: 'swal-wide'
                },
                footer: `<button id="close-modal-btn" style="background: #dc3545; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer;">Tutup</button>`,
                didRender: () => {
                    // Event listener untuk tombol Close
                    document.getElementById("close-modal-btn").addEventListener("click", () => Swal.close());
                    // Event listener untuk tombol Back
                    document.getElementById("back-btn").addEventListener("click", () => {
                        Swal.close(); // Tutup SweetAlert saat ini
                        // Kembali ke modal notulen dengan memanggil showNotulenModal
                        const notulen = notulenData[
                            0]; // Ambil notulen pertama (sesuaikan logika jika ada banyak notulen)
                        showNotulenModal({
                            getAttribute: () => notulen.id
                        }); // Panggil kembali modal notulen
                    });
                }
            });
        }

        function editSolution(id) {
            $(`#update-span-${id}`).addClass("d-none");
            $(`#update-input-${id}`).removeClass("d-none").focus();
            $(`#save-btn-closed-${id}`).removeClass("d-none");
            $(`#save-btn-open-${id}`).removeClass("d-none");
        }

        function saveSolution(id, encodedIssue, encodedNotulenData, targetStatus) {
            let newUpdate = $(`#update-input-${id}`).val();
            $(`#save-btn-closed-${id}`).addClass("d-none");
            $(`#save-btn-open-${id}`).addClass("d-none");
            $(`#loading-${id}`).removeClass("d-none");

            $.ajax({
                url: `/notulen/solution/update/${id}`,
                type: 'PUT',
                data: {
                    update: newUpdate,
                    status: targetStatus,
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    $(`#loading-${id}`).addClass("d-none");

                    if (response.success) {
                        // Update UI tanpa reload
                        $(`#update-span-${id}`).text(newUpdate || '-').removeClass("d-none");
                        $(`#update-input-${id}`).addClass("d-none");

                        // Update status di kolom tabel
                        const statusText = targetStatus === 'open' ? 'Open' : 'Closed';
                        const statusCell = $(`#followup-text-${id}`).parent().siblings().eq(2);
                        statusCell.text(statusText);

                        // Update data di memori
                        let issue = JSON.parse(decodeURIComponent(encodedIssue));
                        let notulenData = JSON.parse(decodeURIComponent(encodedNotulenData));

                        // Perbarui solution di dalam issue
                        issue.solutions = issue.solutions.map(solution => {
                            if (solution.id === id) {
                                solution.update = newUpdate || solution.update;
                                solution.status = targetStatus;
                            }
                            return solution;
                        });

                        // Perbarui status issue
                        const allSolutionsClosed = issue.solutions.every(solution => solution.status ===
                            'closed');
                        const newIssueStatus = allSolutionsClosed ? 'Closed' : 'Open';
                        $('#issue-status').text(`Status Issue: ${newIssueStatus}`);

                        // Perbarui notulenData dengan issue yang sudah diupdate
                        notulenData.forEach(notulen => {
                            if (notulen.topicnotulens) {
                                notulen.topicnotulens.forEach(topic => {
                                    if (topic.issue_notulens) {
                                        topic.issue_notulens.forEach(i => {
                                            if (i.id === issue.id) {
                                                i.solutions = issue.solutions;
                                                i.status = newIssueStatus.toLowerCase();
                                            }
                                        });
                                    }
                                });
                            }
                        });
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses!',
                            text: response.message || 'Terjadi kesalahan saat menyimpan.'
                        }).then(() => {
                            // Kembali ke showNotulenModal setelah error ditutup
                            const notulen = JSON.parse(decodeURIComponent(encodedNotulenData))[0];
                            Swal.close(); // Tutup SweetAlert error
                            showNotulenModal({
                                getAttribute: () => notulen.id
                            });
                        });

                        console.log('Updated notulenData:', notulenData);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message || 'Terjadi kesalahan saat menyimpan.'
                        }).then(() => {
                            // Kembali ke showNotulenModal setelah error ditutup
                            const notulen = JSON.parse(decodeURIComponent(encodedNotulenData))[0];
                            Swal.close(); // Tutup SweetAlert error
                            showNotulenModal({
                                getAttribute: () => notulen.id
                            });
                        });
                    }
                },
                error: function(xhr, status, error) {
                    $(`#loading-${id}`).addClass("d-none");
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Gagal menyimpan data. Coba lagi nanti.'
                    }).then(() => {
                        // Kembali ke showNotulenModal setelah error ditutup
                        const notulen = JSON.parse(decodeURIComponent(encodedNotulenData))[0];
                        Swal.close(); // Tutup SweetAlert error
                        showNotulenModal({
                            getAttribute: () => notulen.id
                        });
                    });
                }
            });
        }
    </script>

    <script>
        function saveNotulen(id) {
            let newText = $(`#issue-input-${id}`).val();
            $(`#save-btn-${id}`).addClass("d-none");
            $(`#loading-${id}`).removeClass("d-none");

            $.ajax({
                url: '/notulen/update',
                type: 'POST',
                data: {
                    id: id,
                    issue: newText,
                    _token: '{{ csrf_token() }}',
                },
                success: function(data) {
                    $(`#loading-${id}`).addClass("d-none");

                    if (data.status === 'success') {
                        $(`#issue-text-${id}`).text(newText).removeClass("d-none");
                        $(`#issue-input-${id}`).addClass("d-none");
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat menyimpan.'
                        });
                    }
                },
                error: function() {
                    $(`#loading-${id}`).addClass("d-none");
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Coba lagi nanti.'
                    });
                }
            });
        }
    </script>

    <script>
        function showAddAgendaModal() {
            let projectTypeOptions = `@foreach ($projectTypes as $projectType)
                <option value="{{ $projectType->id }}">{{ $projectType->title }}</option>
            @endforeach`;

            Swal.fire({
                title: "Tambah Agenda",
                icon: 'info',
                html: `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <form id="agendaForm">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div style="display: flex; flex-direction: column; gap: 10px; align-items: stretch;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <input type="text" id="agenda_name" class="swal2-input" placeholder="Nama Agenda" required>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <select id="project_type_id" class="swal2-select">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <option value="">-- Pilih Project Type --</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            ${projectTypeOptions}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </select>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </form>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            `,
                showCancelButton: true,
                confirmButtonText: "Simpan",
                preConfirm: () => {
                    let agendaName = document.getElementById("agenda_name").value.trim();
                    let projectTypeId = document.getElementById("project_type_id").value;

                    if (!agendaName) {
                        Swal.showValidationMessage("Nama Agenda wajib diisi!");
                        return false;
                    }

                    if (!projectTypeId) {
                        Swal.showValidationMessage("Project Type wajib dipilih!");
                        return false;
                    }

                    let formData = new FormData();
                    formData.append("name", agendaName);
                    formData.append("project_type_id", projectTypeId);



                    return fetch("{{ route('agenda.store') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error("Gagal menyimpan agenda!");
                            }
                            return response.json();
                        })
                        .then(data => {
                            Swal.fire("Sukses!", data.success, "success").then(() => {
                                location.reload();
                            });
                        })
                        .catch(error => {
                            Swal.fire("Error!", error.message, "error");
                        });
                }
            });
        }
    </script>


    <script>
        function showAddTopicModal() {
            let notulenOptions = `@foreach ($notulens as $notulen)
                <option value="{{ $notulen->id }}">{{ $notulen->number }}</option>
            @endforeach`;

            Swal.fire({
                title: "Tambah Topic Notulen",
                icon: 'info',
                html: `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <form id="topicForm">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div style="display: flex; flex-direction: column; gap: 10px; align-items: stretch;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <input type="text" id="title" class="swal2-input" placeholder="Judul Topik" required>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <select id="notulen_id" class="swal2-select">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <option value="">-- Pilih Notulen --</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            ${notulenOptions}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </select>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </form>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            `,
                showCancelButton: true,
                confirmButtonText: "Simpan",
                preConfirm: () => {
                    let title = document.getElementById("title").value.trim();
                    let notulenId = document.getElementById("notulen_id").value;


                    if (!title) {
                        Swal.showValidationMessage("Judul Topik wajib diisi!");
                        return false;
                    }
                    if (!notulenId) {
                        Swal.showValidationMessage("Notulen wajib dipilih!");
                        return false;
                    }

                    let formData = new FormData();
                    formData.append("title", title);
                    formData.append("notulen_id", notulenId);
                    formData.append("_token", "{{ csrf_token() }}");


                    return fetch("{{ route('notulen.storetopicnotulen') }}", {
                            method: 'POST',
                            body: formData,
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error("Gagal menyimpan topic notulen!");
                            }
                            return response.json();
                        })
                        .then(data => {
                            Swal.fire("Sukses!", data.success, "success").then(() => {
                                location.reload();
                            });
                        })
                        .catch(error => {
                            Swal.fire("Error!", error.message, "error");
                        });
                }
            });
        }
    </script>

    <script>
        let extractedData = {};

        function showAddNotulenModal() {
            Swal.fire({
                title: "Tambah Notulen",
                icon: 'info',
                width: '50em',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showCancelButton: true,
                confirmButtonText: "Simpan",
                html: `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <form id="notulenForm">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div style="display: flex; flex-direction: column; gap: 10px; align-items: stretch;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <label>Upload PDF untuk AI:</label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <input type="file" id="pdf_file" class="swal2-input" accept="application/pdf">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <button type="button" id="extractPdfBtn" class="swal2-confirm swal2-styled">Ekstrak dari PDF</button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <hr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <label>Number:</label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <input type="text" id="number" class="swal2-input" placeholder="Masukkan Number" value="${extractedData.number || ''}" required>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <label>Tempat:</label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <input type="text" id="place" class="swal2-input" placeholder="Masukkan tempat" value="${extractedData.place || ''}" required>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <label>Waktu Pelaksanaan Mulai:</label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <input type="datetime-local" id="notulen_time_start" class="swal2-input" value="${extractedData.notulen_time_start || ''}" required>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <label>Waktu Pelaksanaan Akhir:</label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <input type="datetime-local" id="notulen_time_end" class="swal2-input" value="${extractedData.notulen_time_end || ''}" required>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <label>Agenda Notulen:</label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <select id="agenda_notulen_id" class="swal2-select">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <option value="">Pilih Agenda Notulen</option>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            @foreach ($agendas as $agenda)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <option value="{{ $agenda->id }}" ${extractedData.agenda_notulen_id == {{ $agenda->id }} ? 'selected' : ''}> {{ $agenda->name }}</option >
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            @endforeach
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </select >
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <label>Upload File (Opsional):</label>

                                                                                                                                                                                                                                                                                                                                                                                                                                                        <input type="file" id="files" multiple>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </form >
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            `,
                preConfirm: () => {
                    saveNotulen();
                },
                didOpen: () => {
                    document.getElementById("extractPdfBtn").addEventListener("click", extractPdf);
                }
            });
        }

        function saveNotulen() {
            const formData = new FormData();
            formData.append('number', document.getElementById("number").value);
            formData.append('place', document.getElementById("place").value);
            formData.append('notulen_time_start', document.getElementById("notulen_time_start").value);
            formData.append('notulen_time_end', document.getElementById("notulen_time_end").value);
            formData.append('agenda_notulen_id', document.getElementById("agenda_notulen_id").value);
            formData.append('_token', "{{ csrf_token() }}");

            const files = document.getElementById("files").files;
            for (let i = 0; i < files.length; i++) {
                formData.append("files[]", files[i]);
            }

            return fetch("{{ route('notulen.store') }}", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Toast.fire({
                            icon: 'success',
                            title: 'Notulen berhasil disimpan!'
                        });
                        Swal.close();
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Gagal menyimpan Notulen.'
                        });
                    }
                })
                .catch(() => {
                    Toast.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan saat menyimpan Notulen.'
                    });
                });
        }



        async function extractPdf() {
            let pdfFile = document.getElementById("pdf_file").files[0];
            if (!pdfFile) {
                Toast.fire({
                    icon: 'error',
                    title: 'Silakan pilih file PDF terlebih dahulu.'
                });
                return;
            }

            let formData = new FormData();
            formData.append("pdf", pdfFile);

            fetch("{{ route('notulen.extract') }}", {
                    method: "POST",
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        extractedData = {
                            number: data.data.number || "",
                            place: data.data.place || "",
                            notulen_time_start: data.data.notulen_time_start || "",
                            notulen_time_end: data.data.notulen_time_end || "",
                            agenda_notulen_id: data.data.agenda_notulen_id || "",
                        };
                        Toast.fire({
                            icon: 'success',
                            title: 'Data berhasil diekstrak!'
                        });
                        showAddNotulenModal();
                    } else {
                        Toast.fire({
                            icon: 'warning',
                            title: 'AI tidak bisa mengenali PDF. Silakan isi manual.'
                        });
                    }
                })
                .catch(() => {
                    Toast.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan saat memproses PDF.'
                    });
                });
        }


        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    </script>

    <script>
        function showAddIssueModal() {
            let topicOptions = `@foreach ($notulens as $notulen)
                @foreach ($notulen->topicnotulens as $topic)
                    <option value="{{ $topic->id }}">{{ $notulen->number }} - {{ $topic->title }}</option>
                @endforeach
            @endforeach`;

            Swal.fire({
                title: "Tambah Issue untuk Topic",
                icon: 'info',
                html: `
                                                                        <form id="issueForm">
                                                                            <div style="display: flex; flex-direction: column; gap: 10px; align-items: stretch;">
                                                                                <select id="topic_notulen_id" class="swal2-select" required>
                                                                                    <option value="">-- Pilih Topic --</option>
                                                                                    ${topicOptions}
                                                                                </select>
                                                                                <textarea id="issue_description" class="swal2-textarea" placeholder="Deskripsi Issue" required></textarea>
                                                                            </div>
                                                                        </form>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            `,
                showCancelButton: true,
                confirmButtonText: "Simpan",
                preConfirm: () => {
                    let topicId = document.getElementById("topic_notulen_id").value;
                    let description = document.getElementById("issue_description").value.trim();

                    if (!topicId) {
                        Swal.showValidationMessage("Topic wajib dipilih!");
                        return false;
                    }
                    if (!description) {
                        Swal.showValidationMessage("Deskripsi Issue wajib diisi!");
                        return false;
                    }

                    return fetch("{{ route('notulen.storeissue') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                topic_notulen_id: topicId,
                                issue: description
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error("Gagal menyimpan issue!");
                            }
                            return response.json();
                        })
                        .then(data => {
                            Swal.fire("Sukses!", data.success, "success").then(() => {
                                location.reload();
                            });
                        })
                        .catch(error => {
                            Swal.fire("Error!", error.message, "error");
                        });
                }
            });
        }
    </script>

    <script>
        function showAddSolutionModal() {
            let issueOptions = `@foreach ($notulens as $notulen)
                @foreach ($notulen->topicnotulens as $topic)
                    @foreach ($topic->issueNotulens as $issue)
                        <option value="{{ $issue->id }}">{{ $notulen->number }} - {{ $topic->title }} - {{ Str::limit($issue->issue, 30) }}</option>
                    @endforeach
                @endforeach
            @endforeach`;

            Swal.fire({
                title: "Tambah Solution untuk Issue",
                icon: 'info',
                html: `
                                                                        <form id="solutionForm">
                                                                            <div style="display: flex; flex-direction: column; gap: 10px; align-items: stretch;">
                                                                                <select id="issue_notulen_id" class="swal2-select" required>
                                                                                    <option value="">-- Pilih Issue --</option>
                                                                                    ${issueOptions}
                                                                                </select>
                                                                                <textarea id="solution_description" class="swal2-textarea" placeholder="Deskripsi Solusi" required></textarea>
                                                                                <textarea id="solution_pic" class="swal2-textarea" placeholder="Pic" required></textarea>
                                                                                <select id="status" class="swal2-select" required>
                                                                                    <option value="">-- Pilih Status --</option>
                                                                                    <option value="open">Open</option>
                                                                                    <option value="closed">Closed</option>
                                                                                </select>
                                                                                <label>Tanggal Deadline:</label>
                                                                                <input type="date" id="deadlinedate" class="swal2-input">
                                                                            </div>
                                                                        </form>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            `,
                showCancelButton: true,
                confirmButtonText: "Simpan",
                preConfirm: () => {
                    let issueNotulenId = document.getElementById("issue_notulen_id").value;
                    let description = document.getElementById("solution_description").value.trim();
                    let pic = document.getElementById("solution_pic").value.trim();
                    let status = document.getElementById("status").value;
                    let deadlinedate = document.getElementById("deadlinedate").value;

                    if (!issueNotulenId) {
                        Swal.showValidationMessage("Issue wajib dipilih!");
                        return false;
                    }
                    if (!description) {
                        Swal.showValidationMessage("Deskripsi Solusi wajib diisi!");
                        return false;
                    }
                    if (!status) {
                        Swal.showValidationMessage("Status wajib dipilih!");
                        return false;
                    }

                    return fetch("{{ route('notulen.storesolution') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                issue_notulen_id: issueNotulenId,
                                followup: description,
                                pic: pic,
                                status: status,
                                deadlinedate: deadlinedate || null
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error("Gagal menyimpan solution!");
                            }
                            return response.json();
                        })
                        .then(data => {
                            Swal.fire("Sukses!", data.success, "success").then(() => {
                                location.reload();
                            });
                        })
                        .catch(error => {
                            Swal.fire("Error!", error.message, "error");
                        });
                }
            });
        }
    </script>

    <script>
        function confirmClose(notulenId) {
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Status notulen akan ditutup!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Tutup!"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/ notulen / ${notulenId}`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({})
                        }).then(response => response.json())
                        .then(data => {
                            Swal.fire("Sukses!", data.success, "success").then(() => {
                                location.reload();
                            });
                        });
                }
            });
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownItems = document.querySelectorAll('#notulenDropdown .dropdown-item');

            dropdownItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();

                    dropdownItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');

                    const dropdownButton = document.getElementById('dropdownMenuButton');
                    dropdownButton.textContent = this.textContent;

                    const targetId = this.getAttribute('href');
                    const allPanes = document.querySelectorAll('.tab-pane');

                    allPanes.forEach(pane => {
                        pane.classList.remove('show', 'active');
                    });

                    const targetPane = document.querySelector(targetId);
                    if (targetPane) {
                        targetPane.classList.add('show', 'active');
                    }
                });
            });

            const activeItem = document.querySelector('#notulenDropdown .dropdown-item.active');
            if (activeItem) {
                document.getElementById('dropdownMenuButton').textContent = activeItem.textContent;
            }
        });
    </script>
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endpush
