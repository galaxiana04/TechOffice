<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
        <!-- <img src="{{ asset('images/INKAICON.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> -->
        <span class="brand-text font-weight-light">Technology Office</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('images/usericon1.png') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            @guest
            @else
                <div class="info">
                    <a href="{{ url('update-informasi') }}" class="d-block">{{ $userdef->name }}</a>
                    <a href="{{ url('update-informasi') }}" class="d-block">{{ $userdef->username }}</a>
                    <a href="{{ url('update-informasi') }}" class="d-block">{{ $unitsingkatan }}</a>
                </div>
            @endguest
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                @guest

                    <!-- Tidak ada yang ditampilkan ketika pengguna tidak masuk -->
                @else
                    @can('Internal Teknologi')
                        <li class="nav-item">
                            <a href="{{ url('') }}" class="nav-link {{ Request::is('/') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <li class="nav-item menu-close {{ Request::is('notification') ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ Request::is('search') || Request::is('mail/receive/' . $userdefrule) || Request::is('all-users') || Request::is('massuploaduser') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user"></i>
                                <p>
                                    Inbox
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ url('notification/receive/' . $userdefrule) }}"
                                        class="nav-link {{ Request::is('notification/receive/' . $userdefrule) ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-envelope"></i>
                                        <p>Mailbox</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('notification/viewsendwa') }}"
                                        class="nav-link {{ Request::is('notification/viewsendwa') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-envelope"></i>
                                        <p>Broadcast Whatsapp</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li
                            class="nav-item menu-close {{ Request::is('search') || Request::is('mail/receive/' . $userdefrule) || Request::is('users') || Request::is('massuploaduser') ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ Request::is('search') || Request::is('mail/receive/' . $userdefrule) || Request::is('all-users') || Request::is('massuploaduser') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user"></i>
                                <p>
                                    User Tech Office
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">

                                <li class="nav-item">
                                    <a href="{{ url('users') }}" class="nav-link {{ Request::is('users') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-users"></i>
                                        <p>Anggota Divisi Teknologi</p>
                                    </a>
                                </li>
                                @can('adminsetting')
                                    <li class="nav-item">
                                        <a href="{{ url('massuploaduser') }}"
                                            class="nav-link {{ Request::is('massuploaduser') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-file-upload"></i>
                                            <p>Upload Massal Anggota</p>
                                        </a>
                                    </li>
                                @endcan

                                <li class="nav-item">
                                    <a href="{{ url('innovation-progress') }}"
                                        class="nav-link {{ Request::is('innovation-progress') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-search"></i>
                                        <p>Dokumentasi</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endcan




                    @can('Approval')
                        <li
                            class="nav-item menu-close {{ Request::is('new-memo/upload') || Request::is('new-memo') ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ Request::is('new-memo/upload') || Request::is('new-memo') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-calendar-check"></i>
                                <p>
                                    Approval
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">

                                @can('Memo')
                                    <li class="nav-item">
                                        <a href="{{ route('new-memo.index') }}"
                                            class="nav-link {{ Request::is('new-memo/terbuka') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-list"></i>
                                            <p>Memo Approval</p>
                                        </a>
                                    </li>
                                @endcan
                                @can('Memo Sekdiv')
                                    <li class="nav-item">
                                        <a href="{{ route('memosekdivs.index') }}"
                                            class="nav-link {{ Request::is('memosekdivs') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-list"></i> <!-- Icon untuk list RBD -->
                                            <i class="right fas fa-angle-left"></i>
                                            <span class="right badge badge-info">Trial</span>
                                            <i class="right fas fa-angle-left"></i>
                                            <p>Memo Sekdiv</p>
                                        </a>
                                    </li>
                                @endcan
                                @can('KOMREV')
                                    <li class="nav-item">
                                        <a href="{{ route('komatprocesshistory.index') }}"
                                            class="nav-link {{ Request::is('memosekdivs') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-list"></i> <!-- Icon untuk list RBD -->
                                            <i class="right fas fa-angle-left"></i>
                                            <span class="right badge badge-info">Trial</span>
                                            <i class="right fas fa-angle-left"></i>
                                            <p>KOMREV</p>
                                        </a>
                                    </li>
                                @endcan
                                <li class="nav-item">
                                    <a href="{{ route('monitoring.unit') }}"
                                        class="nav-link {{ request()->routeIs('monitoring.unit') ? 'active' : '' }}">
                                        <i class="fas fa-chart-line nav-icon"></i> {{-- Icon Monitoring --}}
                                        <p>Monitoring Unit</p>
                                    </a>
                                </li>

                            </ul>
                        </li>
                    @endcan


                    @can('NewbomkOMAT')
                        <li
                            class="nav-item menu-close {{ Request::is('newboms') || Request::is('newboms/upload/excel') ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ Request::is('newboms') || Request::is('newboms/upload/excel') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>
                                    BOM & KOMAT
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">

                                <li class="nav-item">
                                    <a href="{{ route('katalogkomat.index') }}"
                                        class="nav-link {{ Request::is('katalogkomat') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-list"></i>
                                        <p>KOMAT SAP</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('newboms') }}"
                                        class="nav-link {{ Request::is('newboms') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-sitemap"></i>
                                        <p>Mapping BOM</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endcan


                    @can('Progress')
                        <li
                            class="nav-item menu-close {{ Request::is('newprogressreports/upload') || Request::is('ekspedisi') || Request::is('newreports') ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ Request::is('newprogressreports/upload') || Request::is('newreports') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tasks"></i> <!-- Ikon untuk Progres Dokumen -->
                                <p>
                                    Progres Dokumen
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('newprogressreports.searchform') }}"
                                        class="nav-link {{ Request::is('newprogressreports/search') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-search"></i> <!-- Ikon pencarian -->
                                        <p>Cari Dokumen</p>
                                    </a>
                                </li>

                                @if ($userdefrule == 'superuser' || $userdefrule == 'MTPR')
                                    <li class="nav-item">
                                        <a href="{{ route('newprogressreports.document-kindindex') }}"
                                            class="nav-link {{ Request::is('newprogressreports/document-kind') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-file-alt"></i> <!-- Ikon untuk Jenis Dokumen -->
                                            <p>Jenis Dokumen</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('newprogressreports.index-notif-harian-units') }}"
                                            class="nav-link {{ Request::is('newprogressreports/notif-harian-units') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-file-alt"></i> <!-- Ikon untuk Jenis Dokumen -->
                                            <p>Notif Dokumen</p>
                                        </a>
                                    </li>
                                    @can('MTPR')
                                        <li class="nav-item">
                                            <a href="{{ url('newprogressreports/upload') }}"
                                                class="nav-link {{ Request::is('newprogressreports/upload') ? 'active' : '' }}">
                                                <i class="nav-icon fas fa-file-upload"></i>
                                                <!-- Ikon untuk Upload Progres Dokumen -->
                                                <p>Upload Progres Dokumen</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('newprogressreports.create_dynamic') }}"
                                                class="nav-link {{ request()->routeIs('newprogressreports.create_dynamic') ? 'active' : '' }}">
                                                <i class="nav-icon fas fa-edit "></i>
                                                <p>Input Progres Baru</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ url('ekspedisi') }}"
                                                class="nav-link {{ Request::is('ekspedisi') ? 'active' : '' }}">
                                                <i class="nav-icon fas fa-truck"></i> <!-- Ganti ikon dengan yang lebih sesuai -->
                                                <p>Ekspedisi</p>
                                            </a>
                                        </li>
                                    @endcan
                                @endif

                                <li class="nav-item">
                                    <a href="{{ url('newreports') }}"
                                        class="nav-link {{ Request::is('newreports') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-map"></i> <!-- Ikon untuk Mapping Progres Dokumen -->
                                        <p>Mapping Progres Dokumen</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('newreports/level') }}"
                                        class="nav-link {{ Request::is('newreports/level') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-map"></i> <!-- Ikon untuk Mapping Progres Dokumen -->
                                        <p>Phase Progres Dokumen</p>
                                    </a>
                                </li>
                                @can('Internal Teknologi')
                                    <li class="nav-item">
                                        <a href="{{ route('newreports.target') }}"
                                            class="nav-link {{ Request::is('newreports/ganttchart/target/chart') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-map"></i> <!-- Ikon untuk Mapping Progres Dokumen -->
                                            <p>Schedule</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('newreports.jamorang') }}"
                                            class="nav-link {{ Request::is('newreports/areachart/jamorang/chart') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-map"></i> <!-- Ikon untuk Mapping Progres Dokumen -->
                                            <p>Jam Orang</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('monitoring.dokumen') }}"
                                            class="nav-link {{ request()->routeIs('monitoring.dokumen') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-file-invoice"></i>
                                            <p> Monitoring Dokumen </p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('monitoring.user') }}"
                                            class="nav-link {{ Request::is('monitoring-user') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-chart-pie text-info"></i>
                                            <p>
                                                Laporan Per User
                                            </p>
                                        </a>
                                    </li>
                                @endcan

                            </ul>
                        </li>
                    @endcan

                    @can('Technology Management')
                        <li class="nav-item menu-close {{ Request::is('inventories*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ Request::is('inventories*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-boxes"></i> <!-- Updated icon to 'boxes' for inventory -->
                                <p>
                                    Tech Management
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                @can('Inventaris')
                                    <li class="nav-item">
                                        <a href="{{ route('inventories.index') }}"
                                            class="nav-link {{ Request::is('inventories') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-list"></i>
                                            <p>Inventaris</p>
                                        </a>
                                    </li>
                                @endcan
                                @can('Library')
                                    <li class="nav-item">
                                        <a href="{{ route('library.index') }}"
                                            class="nav-link {{ Request::is('file-management*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-list"></i>
                                            <p>Library</p>
                                        </a>
                                    </li>
                                @endcan
                                @can('Proker & LPK')
                                    <li class="nav-item">
                                        <a href="{{ route('proker.index') }}"
                                            class="nav-link {{ Request::is('prokerlpk*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-list"></i> <!-- Icon untuk list RBD -->
                                            <i class="right fas fa-angle-left"></i>
                                            <span class="right badge badge-warning">Develop</span>
                                            <i class="right fas fa-angle-left"></i>
                                            <p>Proker & LPK</p>
                                        </a>
                                    </li>
                                @endcan
                                @can('AI Custom')
                                    <li class="nav-item">
                                        <a href="{{ url('aicustom') }}"
                                            class="nav-link {{ Request::is('aicustom') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-robot"></i>
                                            <i class="right fas fa-angle-left"></i>
                                            <span class="right badge badge-info">Trial</span>
                                            <i class="right fas fa-angle-left"></i>
                                            <p>AI Custom</p>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcan


                    @can('Jobticket')
                        <li
                            class="nav-item menu-close {{ Request::is('jobticket/uploadexcel') || Request::is('jobticket') ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ Request::is('jobticket/uploadexcel') || Request::is('jobticket') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-clipboard-list"></i> <!-- Ikon untuk Jobticket -->
                                <p>
                                    Jobticket
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('jobticket.jobticket-document-kindindex') }}"
                                        class="nav-link {{ Request::is('jobticket/jobticket-document-kind') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-file-alt"></i> <!-- Ikon untuk Jenis Dokumen -->
                                        <p>Jenis Dokumen</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('jobticket.uploadexcel') }}"
                                        class="nav-link {{ Request::is('jobticket/uploadexcel') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-file-upload"></i> <!-- Ikon untuk Upload Progres Dokumen -->
                                        <p>Upload Progres Dokumen</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('jobticket.showdocumentselfterbuka') }}"
                                        class="nav-link {{ Request::is('jobticket/showself/terbuka') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-folder-open"></i> <!-- Ikon untuk Self Drafter (Terbuka) -->
                                        <p>Self Jobticket (Terbuka)</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('jobticket.showdocumentselftertutup') }}"
                                        class="nav-link {{ Request::is('jobticket/showself/tertutup') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-folder"></i> <!-- Ikon untuk Self Drafter (Tertutup) -->
                                        <p>Self Jobticket (Tertutup)</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('jobticket.showunit') }}"
                                        class="nav-link {{ Request::is('jobticket/unit') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-sitemap"></i> <!-- Ikon untuk Mapping Jobticket -->
                                        <p>Mapping Jobticket</p>
                                    </a>
                                </li>
                                @if (auth()->user()->rule && str_contains(auth()->user()->rule, 'Manager'))
                                    <li class="nav-item">
                                        <a href="{{ route('jobticket.managershow') }}"
                                            class="nav-link {{ Request::is('jobticket/manager/terbuka') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-tasks"></i> <!-- Ikon baru untuk Manager Task -->
                                            <p>Manager Task</p>
                                        </a>
                                    </li>
                                @endif







































                                @php
                                    // Ambil rule pengguna
                                    $userRule = auth()->user()->rule;

                                    // Modifikasi rule berdasarkan kondisi
                                    if (Str::contains($userRule, 'Manager')) {
                                        $userRule = Str::replace('Manager', '', $userRule);
                                    }
                                    if ($userRule === 'Senior Manager Engineering') {
                                        $userRule = 'Quality Engineering';
                                    }
                                @endphp

                                <li class="nav-item">
                                    <a href="{{ route('jobticket.rank', ['unit' => trim($userRule)]) }}"
                                        class="nav-link {{ Request::is('jobticket/rank') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-chart-line"></i> <!-- Ikon untuk Performa -->
                                        <p>Performa</p>
                                    </a>
                                </li>



                            </ul>
                        </li>
                    @endcan






                    @can('Rapat')
                        <li class="nav-item menu-close {{ Request::is('events*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ Request::is('events*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-calendar-alt"></i>
                                <p>
                                    Ruang Rapat
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('meetingrooms.index') }}"
                                        class="nav-link {{ Request::is('meetingrooms') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-calendar-plus"></i>
                                        <p>List Nama Ruang Rapat</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="https://drive.google.com/drive/u/0/folders/13sCkaK6ZIxwooWbEbQnBNSFkIJwturye?ths=true"
                                        class="nav-link">
                                        <i class="nav-icon fas fa-book"></i>
                                        <p>User Manual</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('events.create') }}"
                                        class="nav-link {{ Request::is('events/create') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-calendar-plus"></i>
                                        <p>Buat Jadwal</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('events.all') }}"
                                        class="nav-link {{ Request::is('events/all') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-map-marker-alt"></i>
                                        <p>Mapping Rapat</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endcan

                    @can('RAMS')
                        <li class="nav-item menu-close {{ Request::is('newrbd*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ Request::is('newrbd*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-project-diagram"></i> <!-- Icon untuk RBD -->
                                <p>
                                    RAMS
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                @can('Ramsdocument')
                                    <li class="nav-item">
                                        <a href="{{ url('rams/terbuka') }}"
                                            class="nav-link {{ Request::is('rams/terbuka') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-list"></i> <!-- Icon untuk list RBD -->
                                            <i class="right fas fa-angle-left"></i>
                                            <span class="right badge badge-info">Trial</span>
                                            <i class="right fas fa-angle-left"></i>
                                            <p>RAMS Document</p>
                                        </a>
                                    </li>
                                @endcan
                                @can('RAMSParameter')
                                    <li class="nav-item">
                                        <a href="{{ route('weibull.dashboard') }}"
                                            class="nav-link {{ Request::is('failurerate') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-list"></i> <!-- Icon untuk list RBD -->
                                            <i class="right fas fa-angle-left"></i>
                                            <span class="right badge badge-info">Trial</span>
                                            <i class="right fas fa-angle-left"></i>
                                            <p>RAMS Parameter</p>
                                        </a>
                                    </li>
                                @endcan
                                @can('RBD')
                                    <li class="nav-item">
                                        <a href="{{ route('newrbd.index') }}"
                                            class="nav-link {{ Request::is('newrbd') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-list"></i> <!-- Icon untuk list RBD -->
                                            <i class="right fas fa-angle-left"></i>
                                            <span class="right badge badge-warning">Develop</span>
                                            <i class="right fas fa-angle-left"></i>
                                            <p>RBD</p>
                                        </a>
                                    </li>
                                @endcan
                                @can('FTA')
                                    <li class="nav-item">
                                        <a href="{{ route('fta.index') }}"
                                            class="nav-link {{ Request::is('fta*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-list"></i> <!-- Icon untuk list RBD -->
                                            <i class="right fas fa-angle-left"></i>
                                            <span class="right badge badge-warning">Develop</span>
                                            <i class="right fas fa-angle-left"></i>
                                            <p>Faul Tree Analysis</p>
                                        </a>
                                    </li>
                                @endcan
                                @can('PBS')
                                    <li class="nav-item">
                                        <a href="{{ route('product-breakdown-structure.index') }}"
                                            class="nav-link {{ Request::is('product-breakdown-structure*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-list"></i> <!-- Icon untuk list RBD -->
                                            <i class="right fas fa-angle-left"></i>
                                            <span class="right badge badge-warning">Develop</span>
                                            <i class="right fas fa-angle-left"></i>
                                            <p>Product Breakdown Structure</p>
                                        </a>
                                    </li>
                                @endcan
                                @can('FMECA')
                                    <li class="nav-item">
                                        <a href="{{ url('fmeca') }}" class="nav-link {{ Request::is('fmeca') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-list"></i> <!-- Icon untuk list RBD -->
                                            <i class="right fas fa-angle-left"></i>
                                            <span class="right badge badge-warning">Develop</span>
                                            <i class="right fas fa-angle-left"></i>
                                            <p>FMECA</p>
                                        </a>
                                    </li>
                                @endcan
                                @can('Hazardlog')
                                    <li class="nav-item">
                                        <a href="{{ route('hazard_logs.index') }}"
                                            class="nav-link {{ Request::is('hazard_logs') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-list"></i> <!-- Icon untuk list RBD -->
                                            <i class="right fas fa-angle-left"></i>
                                            <span class="right badge badge-info">Trial</span>
                                            <i class="right fas fa-angle-left"></i>
                                            <p>Hazard Log</p>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcan

                    @can('Product Engineering')
                        <li
                            class="nav-item menu-close {{ Request::is('rollingstock') || Request::is('notulen*') ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ Request::is('rollingstock') || Request::is('notulen*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-project-diagram"></i> <!-- Icon untuk RBD -->
                                <p>
                                    Product Engineering
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">

                                @can('Rolling Stock')
                                    <li class="nav-item">
                                        <a href="{{ url('rollingstock') }}"
                                            class="nav-link {{ Request::is('rollingstock') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-train"></i>
                                            <i class="right fas fa-angle-left"></i>
                                            <span class="right badge badge-info">Trial</span>
                                            <i class="right fas fa-angle-left"></i>
                                            <p>Rolling Stock</p>
                                        </a>
                                    </li>
                                @endcan
                                @can('Notulen')
                                    <li class="nav-item">
                                        <a href="{{ route('notulen.index') }}"
                                            class="nav-link {{ request()->is('notulen/dashboard') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-clipboard-list"></i>
                                            <i class="right fas fa-angle-left"></i>
                                            <span class="right badge badge-info">Trial</span>
                                            <i class="right fas fa-angle-left"></i>
                                            <p>Notulen</p>
                                        </a>
                                    </li>
                                @endcan
                                @can('TACK')
                                    <li class="nav-item">
                                        <a href="{{ route('tack.index') }}"
                                            class="nav-link {{ Request::is('tack*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-clipboard-list"></i>
                                            <i class="right fas fa-angle-left"></i>
                                            <span class="right badge badge-warning">Develop</span>
                                            <i class="right fas fa-angle-left"></i>
                                            <p>TACK</p>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcan

                    @can('innovation')
                        <li class="nav-item menu-open">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-building"></i>
                                <p>
                                    Inovasi
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="http://147.93.103.168:2727/" class="nav-link">
                                        <i class="nav-icon fas fa-file"></i>
                                        <p>Welding Spot Detection</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endcan


                    @can('adminsetting')
                        <li
                            class="nav-item menu-close {{ Request::is('document') || Request::is('file') || Request::is('categories') ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ Request::is('document') || Request::is('file') || Request::is('categories') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-building"></i>
                                <p>
                                    Data Kantor
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ url('document') }}"
                                        class="nav-link {{ Request::is('document') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-file"></i>
                                        <p>Dokumen</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('file') }}" class="nav-link {{ Request::is('file') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-file"></i>
                                        <p>File</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('categories') }}"
                                        class="nav-link {{ Request::is('categories') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-list"></i>
                                        <p>Kategori</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('zoom') }}" class="nav-link {{ Request::is('zoom') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-list"></i>
                                        <p>Zoom</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endcan

                @endguest
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>