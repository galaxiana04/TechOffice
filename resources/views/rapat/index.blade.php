<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Technology Office | Meeting Mapping</title>
  
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
            // Kita menggunakan palet warna bawaan Tailwind (red-*)
        }
      },
      // Penting: corePlugins preflight false agar tidak merusak style AdminLTE/Bootstrap bawaan
      corePlugins: {
        preflight: false,
      }
    }
  </script>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="{{ asset('adminlte3/plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte3/dist/css/adminlte.min.css') }}">
  <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/INKAICON.png') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('adminlte3/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte3/plugins/fullcalendar/main.css') }}">

  <script src="{{ asset('schedulerdaypilot/js/jquery/jquery-1.9.1.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('schedulerdaypilot/js/daypilot/daypilot-all.min.js') }}" type="text/javascript"></script>

  <style type="text/css">
      /* Mempercantik Scrollbar dengan tema kemerahan */
      ::-webkit-scrollbar {
          width: 8px;
          height: 8px;
      }
      ::-webkit-scrollbar-track {
          background: #6b7280; 
      }
      ::-webkit-scrollbar-thumb {
          background: #6b7280; /* Rose-300 */
          border-radius: 4px;
      }
      ::-webkit-scrollbar-thumb:hover {
          background: #6b7280; /* Rose-500 */
      }

      /* DayPilot Styles Override (Red Theme) */
      .scheduler_default_rowheader {
          background: #fff1f2 !important; /* Rose-50 */
          border-right: 1px solid #6b7280 !important; /* Rose-300 */
      }
      .scheduler_default_rowheader_inner {
          border-right: 1px solid #fda4af;
      }
      .scheduler_default_rowheadercol2 {
          background: White;
      }
      .scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner {
          top: 2px;
          bottom: 2px;
          left: 2px;
          background-color: transparent;
          border-left: 5px solid #dc2626; /* Red-600 */
          border-right: 0px none;
      }
      
      /* Bootstrap Tab Overrides for cleaner look (Red Theme) */
      .nav-tabs .nav-link {
          border: none !important;
          color: #6b7280; /* Gray-500 */
          font-weight: 600;
          padding: 10px 20px;
          transition: all 0.3s ease;
          border-radius: 0.5rem !important;
      }
      .nav-tabs .nav-link:hover {
          color: #dc2626; /* Red-600 */
          background-color: #fef2f2; /* Red-50 */
      }
      .nav-tabs .nav-link.active {
          color: #ffffff !important;
          background-color: #dc2626 !important; /* Red-600 */
          box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.5); /* Red shadow */
      }
      .nav-tabs {
          border-bottom: 2px solid #f3f4f6;
          margin-bottom: 1.5rem;
          padding-bottom: 0.5rem;
      }

      /* Card Modernization */
      .modern-card {
          border: none !important;
          box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
          border-radius: 1rem !important;
          overflow: hidden;
      }
      .modern-card-header {
          background: white !important;
          border-bottom: 1px solid #f3f4f6;
          padding: 1.5rem !important;
      }
  </style>
</head>

<body class="hold-transition sidebar-mini bg-gray-50 font-sans text-gray-800">
    @php
        use Carbon\Carbon;
    @endphp

    <div class="modal fade" id="updateInfoModal" tabindex="-1" aria-labelledby="updateInfoModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-xl shadow-2xl border-0">
                <div class="modal-header bg-gradient-to-r from-red-700 to-red-500 text-white rounded-t-xl">
                    <h5 class="modal-title font-bold" id="updateInfoModalLabel">Update Informasi</h5>
                </div>
                <div class="modal-body p-6 text-gray-600 text-lg">
                    Nomor WhatsApp Anda belum terdaftar. Silakan perbarui informasi Anda untuk melanjutkan.
                </div>
                <div class="modal-footer bg-gray-50 rounded-b-xl border-t-0">
                    <a href="{{ route('updateInformasiForm') }}" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition shadow-lg font-semibold">Perbarui Informasi</a>
                </div>
            </div>
        </div>
    </div>

    <div class="wrapper">
        @include('partials.navbaradminlte3')
        
        <div class="content-wrapper bg-gray-50">
            <div class="content-header pt-6 pb-4">
                <div class="container-fluid">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-gray-800">Mapping Meeting</h1>
                        <ol class="breadcrumb bg-transparent p-0 m-0 text-sm">
                            <li class="breadcrumb-item"><a href="/" class="text-red-600 hover:text-red-800">Meeting</a></li>
                            <li class="breadcrumb-item active text-gray-500">Mapping</li>
                        </ol>
                    </div>
                </div>
            </div>

            <section class="content pb-10">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            
                            <div class="card modern-card bg-white">
                                <div class="h-2 w-full bg-gradient-to-r from-red-800 via-red-600 to-red-400"></div>
                                
                                <div class="modern-card-header flex justify-between items-center">
                                    <h3 class="card-title text-xl font-bold text-gray-800 flex items-center">
                                        <i class="fas fa-calendar-alt mr-2 text-red-700"></i> Jadwal Ruang Rapat
                                    </h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool text-gray-400 hover:text-gray-600" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="card-body p-6">
                                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                        @foreach ($revisiall as $key => $revisi)
                                            <li class="nav-item">
                                                <a class="nav-link @if($loop->first) active @endif" id="custom-tabs-one-{{ $key }}-tab" data-toggle="pill" href="#custom-tabs-one-{{ $key }}" role="tab" aria-controls="custom-tabs-one-{{ $key }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                                    {{ $key }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div class="tab-content" id="custom-tabs-one-tabContent">
                                        @foreach ($revisiall as $key => $revisi)
                                            <div class="tab-pane fade @if($loop->first) show active @endif" id="custom-tabs-one-{{ $key }}" role="tabpanel" aria-labelledby="custom-tabs-one-{{ $key }}-tab">
                                                
                                              <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-4">
                                                    <div class="flex items-center justify-between p-4">
                                                        <!-- Navigation Tabs -->
                                                         <ul class="nav nav-pills flex gap-2" id="nested-tabs-one-tab" role="tablist">
                                                            <li class="nav-item">
                                                                <a class="nav-link active flex items-center px-4 py-2 rounded-lg transition-colors duration-200 bg-red-600 text-white hover:bg-red-700" 
                                                                id="nested-tabs-one-schedule-tab" 
                                                                data-toggle="pill" 
                                                                href="#nested-tabs-one-schedule-{{ $key }}" 
                                                                role="tab" 
                                                                aria-controls="nested-tabs-one-schedule-{{ $key }}" 
                                                                aria-selected="true">
                                                                <i class="far fa-calendar-check mr-2"></i>
                                                                <span>Kalender</span>
                                                            </a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link flex items-center px-4 py-2 rounded-lg transition-colors duration-200 text-gray-700 hover:bg-gray-100" 
                                                            id="nested-tabs-one-display-tab" 
                                                            data-toggle="pill" 
                                                            href="#nested-tabs-one-display-{{ $key }}" 
                                                            role="tab" 
                                                            aria-controls="nested-tabs-one-display-{{ $key }}" 
                                                            aria-selected="false">
                                                            <i class="fas fa-list mr-2"></i>
                                                            <span>List Data</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                                <!-- Create Button -->
                                                 <a href="{{ route('events.create') }}" 
                                                 class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm">
                                                 <i class="fas fa-plus mr-2"></i>
                                                 Buat Jadwal
                                                </a>
                                            </div>
                                        </div>


                                                <div class="tab-content" id="nested-tabs-one-tabContent">
                                                    
                                                    <div class="tab-pane fade show active" id="nested-tabs-one-schedule-{{ $key }}" role="tabpanel" aria-labelledby="nested-tabs-one-schedule-tab">
                                                        <div class="container-fluid px-0">
                                                            <div class="row">
                                                                  <div class="col-md-12">
                                                                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                                                                        @if($key != "All")
                                                                            <div id="calendar-{{ $key }}" data-calendar-key="{{ $key }}" class="fc-theme-bootstrap"></div>
                                                                        @else
                                                                            <div id="calendar-{{ $key }}" data-calendar-key="{{ $key }}" style="display: none;"></div>
                                                                            <div id="scheduler" style="height: 600px; width: 100%;"></div>
                                                                            
                                                                            <script>
                                                                                var dp = new DayPilot.Scheduler("scheduler");
                                                                                dp.startDate = DayPilot.Date.today();
                                                                                dp.days = 1;
                                                                                dp.businessBeginsHour = 6;
                                                                                dp.businessEndsHour = 23;
                                                                                dp.businessWeekends = true;
                                                                                dp.showNonBusiness = false;
                                                                                dp.timeHeaders = [
                                                                                    { groupBy: "Month", format: "dd/MM/yyyy", height: 40 },
                                                                                    { groupBy: "Day", format: "dd/MM/yyyy", height: 40 },
                                                                                    { groupBy: "Hour", format: "H:mm", height: 40 }
                                                                                ];
                                                                                dp.eventHeight = 100;
                                                                                dp.cellWidth = 120;
                                                                                dp.cellWidthMin = 120;
                                                                                dp.cellHeight = 100;
                                                                                dp.resources = [
                                                                                    @foreach($ruangrapat as $room)
                                                                                        @if($room != "All")
                                                                                            { name: "{{ $room }}", id: "{{ str_replace(['.', ' '], ['-', '_'], $room) }}" },
                                                                                        @endif
                                                                                    @endforeach
                                                                                ];
                                                                                dp.events.list = @json($eventsdaypilot);
                                                                                dp.onTimeRangeSelected = function(args) {
                                                                                    var name = prompt("New event name:", "Event");
                                                                                    dp.clearSelection();
                                                                                    if (!name) return;
                                                                                    var e = {
                                                                                        start: args.start,
                                                                                        end: args.end,
                                                                                        id: DayPilot.guid(),
                                                                                        text: name,
                                                                                        resource: args.resource
                                                                                    };
                                                                                    dp.events.add(e);
                                                                                    DayPilot.Http.ajax({
                                                                                        url: "/events/create",
                                                                                        data: e,
                                                                                        success: function(ajax) {
                                                                                            var response = ajax.data;
                                                                                            if (response && response.result) {
                                                                                                e.id = response.id;
                                                                                                dp.message("Created: " + response.message);
                                                                                            }
                                                                                        },
                                                                                        error: function(ajax) {
                                                                                            dp.message("Saving failed");
                                                                                        }
                                                                                    });
                                                                                };
                                                                                dp.onEventClick = function(args) {
                                                                                    var eventId = args.e.id;
                                                                                    var url = "{{ route('events.show', ':id') }}".replace(':id', eventId);
                                                                                    window.location.href = url;
                                                                                };
                                                                                dp.bubble = new DayPilot.Bubble({
                                                                                    onLoad: function(args) {
                                                                                        var ev = args.source;
                                                                                        args.async = true;
                                                                                        var eventUrl = "{{ route('events.show', ':id') }}".replace(':id', ev.id());
                                                                                        setTimeout(function() {
                                                                                            args.html = `
                                                                                                <div style='font-weight:bold; font-family:sans-serif; color:#b91c1c;'>${ev.text()}</div>
                                                                                                <div>Start: ${ev.start().toString("MM/dd/yyyy HH:mm")}</div>
                                                                                                <div>End: ${ev.end().toString("MM/dd/yyyy HH:mm")}</div>
                                                                                                <div style='margin-top:5px'><a href='${eventUrl}' target='_blank' style='color:#dc2626; font-weight:bold;'>View Event</a></div>`;
                                                                                            args.loaded();
                                                                                        }, 500);
                                                                                    }
                                                                                });
                                                                                dp.onBeforeEventRender = function(args) {
                                                                                    var start = new DayPilot.Date(args.e.start);
                                                                                    var end = new DayPilot.Date(args.e.end);
                                                                                    var eventUrl = "{{ route('events.show', ':id') }}".replace(':id', args.e.id);
                                                                                    // Red Theme for Scheduler Events
                                                                                    args.e.html = `
                                                                                        <div style='background-color: #fef2f2; padding: 8px; border-radius: 6px; height:100%; border-left: 4px solid #dc2626;'>
                                                                                            <div style='font-weight:bold; color: #991b1b; font-size:13px;'>${args.e.text}</div>
                                                                                            <div style='color: #7f1d1d; font-size:11px;'>${start.toString("HH:mm")} - ${end.toString("HH:mm")}</div>
                                                                                            <div style='color: #7f1d1d; font-size:11px; margin-top:2px;'>Pic: ${args.e.pic}</div>
                                                                                        </div>
                                                                                    `;
                                                                                    args.e.barColor = "transparent"; 
                                                                                    args.e.toolTip = "Event from " + start.toString("HH:mm") + " to " + end.toString("HH:mm");
                                                                                };
                                                                                dp.init();
                                                                            </script>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @if($key!="All")
                                                        <div class="tab-pane fade" id="nested-tabs-one-display-{{ $key }}" role="tabpanel" aria-labelledby="nested-tabs-one-display-tab">  
                                                            <div class="container-fluid px-0">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm mt-3">
                                                                            <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                                                                                <h3 class="font-bold text-gray-700 text-lg">Page Monitoring Meeting</h3> 
                                                                                
                                                                                <div class="flex space-x-2">
                                                                                    @if(in_array(auth()->user()->rule, ["Product Engineering","superuser"]))
                                                                                        <button type="button" class="px-4 py-2 bg-red-800 text-white hover:bg-red-900 rounded-lg text-sm font-semibold transition shadow-sm" onclick="handleDeleteMultipleItems()">
                                                                                            <i class="fas fa-trash mr-1"></i> Hapus Terpilih
                                                                                        </button>
                                                                                    @endif
                                                                                    <a href="{{ url('calendar/events/create') }}" class="px-4 py-2 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg text-sm font-semibold transition">
                                                                                        <i class="fas fa-plus mr-1"></i> Buat Jadwal
                                                                                    </a>
                                                                                </div>
                                                                            </div>

                                                                            <div class="p-4">
                                                                                <div class="table-responsive">
                                                                                    <table id="example2-{{ $key }}" class="table table-hover w-100 text-sm">
                                                                                        @php
                                                                                            $keyanku1 = str_replace('.', '-', $key);
                                                                                            $keyanku = str_replace(' ', '_', $keyanku1);
                                                                                            $events = $revisiall[$keyanku]['events'];
                                                                                        @endphp
                                                                                        <thead class="bg-red-50 text-red-800 uppercase font-semibold">
                                                                                            <tr>
                                                                                                <th class="rounded-tl-lg">
                                                                                                    <span class="checkbox-toggle cursor-pointer text-red-600" id="checkAll"><i class="far fa-square"></i></span>
                                                                                                </th>
                                                                                                <th>No</th>
                                                                                                <th>Nama Rapat</th>
                                                                                                <th>Waktu Awal</th>
                                                                                                <th>Waktu Akhir</th>
                                                                                                <th class="rounded-tr-lg">Aksi</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            @php $counter = 1; @endphp
                                                                                            @foreach ($events as $event)
                                                                                                <tr class="hover:bg-red-50 transition duration-150 border-b border-gray-100">
                                                                                                    <td class="align-middle">
                                                                                                        <div class="icheck-danger">
                                                                                                            <input type="checkbox" value="{{ $event->id }}" name="document_ids[]" id="checkbox{{ $event->id }}">
                                                                                                            <label for="checkbox{{ $event->id }}"></label>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                    <td class="align-middle font-medium text-gray-600">{{ $counter++ }}</td>
                                                                                                    <td class="align-middle font-bold text-gray-800">{{ $event->title }}</td>
                                                                                                    <td class="align-middle text-red-700 bg-red-50 rounded px-2 py-1 font-mono text-xs font-semibold">{{ Carbon::parse($event->start)->format('d-m-Y H:i') }}</td>
                                                                                                    <td class="align-middle text-white bg-red-600 rounded px-2 py-1 font-mono text-xs font-semibold">{{ Carbon::parse($event->end)->format('d-m-Y H:i') }}</td>
                                                                                                    <td class="align-middle">
                                                                                                        <a href='{{ route('events.show', $event->id) }}' class="inline-block px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded-md shadow transition font-semibold">
                                                                                                            Detail
                                                                                                        </a>
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
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div> </div>
                                        @endforeach
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <footer class="main-footer bg-white border-t border-gray-200 text-sm text-gray-500 py-4">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> 3.2.0
            </div>
            <strong>Copyright &copy; 2024 <a href="https://adminlte.io" class="text-red-600 hover:underline">Technology Office</a>.</strong>
            All rights reserved.
        </footer>

        <aside class="control-sidebar control-sidebar-dark">
            </aside>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('adminlte3/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('adminlte3/dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/fullcalendar/main.js') }}"></script>

    <script>
        $(document).ready(function() {
            @if($alert === "yes")
                $('#updateInfoModal').modal('show');
            @endif
        });
    </script>

    <script>
        $(function () {
            function ini_events(ele) {
                ele.each(function () {
                    var eventObject = {
                        title: $.trim($(this).text())
                    };
                    $(this).data('eventObject', eventObject);
                    $(this).draggable({
                        zIndex: 1070,
                        revert: true,
                        revertDuration: 0
                    });
                });
            }

            function initializeCalendar(key, events) {
                var calendarEl = document.querySelector(`#calendar-${key}`);
                var calendarKey = calendarEl.getAttribute('data-calendar-key');

                if (!calendarEl) {
                    console.error('Calendar element not found for key:', key);
                    return;
                }

                if (calendarEl._fullCalendar) {
                    calendarEl._fullCalendar.destroy();
                }

                var calendar = new FullCalendar.Calendar(calendarEl, {
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    themeSystem: 'bootstrap',
                    events: events,
                    editable: true,
                    droppable: true,
                    drop: function(info) {
                        var checkbox = document.getElementById('drop-remove');
                        if (checkbox && checkbox.checked) {
                            info.draggedEl.parentNode.removeChild(info.draggedEl);
                        }
                    },
                    slotLabelFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    },
                    eventTimeFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    },
                    eventContent: function(arg) {
                        // Tailwind styled event render (Red Theme)
                        return {
                            html: `<div class="p-1 rounded bg-red-50 border-l-4 border-red-600 text-xs shadow-sm cursor-pointer hover:bg-red-100 transition overflow-hidden">
                                    <div class="font-bold text-red-900">${arg.event.extendedProps.starttime} - ${arg.event.extendedProps.endtime ? arg.event.extendedProps.endtime : ''}</div>
                                    <div class="truncate text-gray-700">${arg.event.title}</div>
                                    <div class="text-red-400 italic" style="font-size: 10px;">${arg.event.extendedProps.pic}</div>
                                </div>`
                        }
                    }
                });

                calendarEl._fullCalendar = calendar;
                calendar.render();
            }

            function formatEventTime(dateTimeStr) {
                var date = new Date(dateTimeStr);
                var hours = date.getHours().toString().padStart(2, '0');
                var minutes = date.getMinutes().toString().padStart(2, '0');
                return hours + ':' + minutes;
            }
            
            function convertListToString(agenda_unit) {
                try {
                    var list = JSON.parse(agenda_unit);
                    function abbreviate(phrase) {
                        return phrase.split(' ').map(word => word.charAt(0)).join('').toUpperCase();
                    }
                    var abbreviatedList = list.map(function(item) {
                        return item.length > 10 ? abbreviate(item) : item;
                    });
                    var resultString = abbreviatedList.join(', ');
                    if (resultString.length > 10) {
                        resultString = resultString.substring(0, 8) + '....';
                    }
                    return resultString;
                } catch (error) {
                    return "...";
                }
            }

            function formatEventTime(dateTimeStr) {
                var parts = dateTimeStr.split(' ');
                var timePart = parts[1];
                var timeParts = timePart.split(':');
                var hours = timeParts[0];
                var minutes = timeParts[1];
                return hours + ':' + minutes;
            }

            function renderCalendarAndTable(key) {
                var events = @json($revisiall)[key].events.map(function(event) {
                    return {
                        title: event.title,
                        start: event.start,
                        starttime: formatEventTime(event.start),
                        endtime: formatEventTime(event.end),
                        end: event.end ? event.end : null,
                        color: event.color,
                        unit: convertListToString(event.agenda_unit),
                        pic: event.pic,
                        room: event.room,
                        url: '{{ route('events.show', ':id') }}'.replace(':id', event.id)
                    };
                });

                initializeCalendar(key, events);
                ini_events($(`#external-events-${key} div.external-event`));

                if ($.fn.DataTable.isDataTable(`#example2-${key}`)) {
                    $(`#example2-${key}`).DataTable().destroy();
                }

                $(`#example2-${key}`).DataTable({
                    paging: true,
                    lengthChange: false,
                    searching: true,
                    ordering: true,
                    info: true,
                    autoWidth: false,
                    responsive: true,
                    // Styling wrapper datatable agar match dengan tailwind container
                    dom: '<"flex justify-between items-center mb-2"f>t<"flex justify-between items-center mt-4"ip>',
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Cari jadwal..."
                    }
                });
                
                // Tailwind input styling for datatable search (Red Focus)
                $('.dataTables_filter input').addClass('border border-gray-300 rounded-lg px-3 py-1 focus:outline-none focus:ring-2 focus:ring-red-500 text-sm transition duration-200');
            }

            renderCalendarAndTable("{{ array_key_first($revisiall) }}");

            $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
                var key = $(e.target).attr('aria-controls').replace('custom-tabs-one-', '');
                setTimeout(function() {
                    renderCalendarAndTable(key);
                }, 50);
            });
        });
    </script>
</body>
</html>