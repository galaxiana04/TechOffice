@extends('layouts.universal')

@php
    $authuser = auth()->user();
@endphp

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="{{ route('komatprocesshistory.index') }}">List KomRev</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="card card-danger card-outline">
        <div class="card-header">
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            <h3 class="card-title text-bold">Page Monitoring KomRev <span class="badge badge-info ml-1"></span></h3>
        </div>
        <div class="card-body">
            @if ($projects->isNotEmpty())
                <!-- Bootstrap Dropdown for Projects -->
                <div class="mb-3">
                    <label for="projectSelect" class="form-label">Select Project:</label>
                    <select id="projectSelect" class="form-select" onchange="filterByProject(this.value)">
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" {{ $loop->first ? 'selected' : '' }}>
                                {{ $project->title ?? 'Project ' . $project->id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Project Tables -->
                @foreach ($projects as $project)
                    <div class="project-table" id="project-table-{{ $project->id }}"
                        style="display: {{ $loop->first ? 'block' : 'none' }};">
                        @php
                            // Filter komatProcessHistories by project_type_id
                            $groupedKomatProcesses = $komatProcesses
                                ->groupBy('komat_name')
                                ->map(function ($processes) use ($project) {
                                    return $processes
                                        ->flatMap(function ($process) {
                                            return $process->komatProcessHistories;
                                        })
                                        ->filter(function ($doc) use ($project) {
                                            return $doc->project_type_id == $project->id;
                                        });
                                })
                                ->filter(function ($documents) {
                                    return $documents->isNotEmpty();
                                });
                        @endphp

                        @if ($groupedKomatProcesses->isNotEmpty())
                            <!-- Unit Tabs -->
                            <ul class="nav nav-tabs" id="unitTabs-{{ $project->id }}" role="tablist">
                                <!-- All Tab -->
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="unit-tab-{{ $project->id }}-all"
                                        data-bs-toggle="tab" data-bs-target="#unit-content-{{ $project->id }}-all"
                                        type="button" role="tab" aria-controls="unit-content-{{ $project->id }}-all"
                                        aria-selected="true">
                                        All
                                    </button>
                                </li>
                                <!-- Individual Unit Tabs -->
                                @foreach ($units as $unit)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="unit-tab-{{ $project->id }}-{{ $unit->id }}"
                                            data-bs-toggle="tab"
                                            data-bs-target="#unit-content-{{ $project->id }}-{{ $unit->id }}"
                                            type="button" role="tab"
                                            aria-controls="unit-content-{{ $project->id }}-{{ $unit->id }}"
                                            aria-selected="false">
                                            {{ $unit->name }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            <!-- Unit Tab Content -->
                            <div class="tab-content mt-3" id="unitTabContent-{{ $project->id }}">
                                <!-- All Tab Content -->
                                <div class="tab-pane fade show active" id="unit-content-{{ $project->id }}-all"
                                    role="tabpanel" aria-labelledby="unit-tab-{{ $project->id }}-all">
                                    <div class="mb-3">
                                        <a href="{{ route('komatprocesshistory.showuploaddoc') }}" class="btn btn-primary btn-sm">
                                            Upload Komrev
                                        </a>
                                    </div>
                                    <table class="table table-bordered table-hover table-striped" id="table-{{ $project->id }}-all">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" class="selectAll" onchange="toggleAllCheckboxes(this, '{{ $project->id }}-all')"></th>
                                                <th>No</th>
                                                <th>Komat</th>
                                                <th>Material</th>
                                                <th>Supplier</th>
                                                <th>Req</th>
                                                <th>Rev</th>
                                                <th>Disc No</th>
                                                <th>Status</th>
                                                <th>Posisi Komrev</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $counter = 1; @endphp
                                            @foreach ($groupedKomatProcesses as $komat_name => $documents)
                                                @php
                                                    $supplierGroups = $documents
                                                        ->groupBy(function ($doc) {
                                                            return $doc->supplier->name ?? 'N/A';
                                                        })
                                                        ->values();
                                                @endphp
                                                @foreach ($supplierGroups as $supplierIndex => $spGroup)
                                                    @php
                                                        $rowspanSupplier = $spGroup->count();
                                                        $firstDocInGroup = $spGroup->first();
                                                        $supplierName = $firstDocInGroup->supplier->name ?? 'N/A';
                                                        $requirementGroups = $spGroup
                                                            ->groupBy(function ($doc) {
                                                                return $doc->requirement->name ?? 'N/A';
                                                            })
                                                            ->values();
                                                    @endphp
                                                    @foreach ($requirementGroups as $requirementIndex => $reqGroup)
                                                        @php
                                                            $rowspanRequirement = $reqGroup->count();
                                                            $firstDocInReqGroup = $reqGroup->first();
                                                            $requirementName = $firstDocInReqGroup->requirement->name ?? 'N/A';
                                                        @endphp
                                                        @foreach ($reqGroup as $index => $document)
                                                            @php
                                                                $hasLogistikUpload = false;
                                                                $logistikApproved = false;
                                                                $hasDiscussion = false;
                                                                $allApproved = false;
                                                                $hasResume = false;
                                                                $allResumeApproved = false;
                                                                $hasSMLevel = false;
                                                                $allSMLevelApproved = false;
                                                                $hasMTPRReview = false;
                                                                $allMTPRReviewApproved = false;
                                                                $hasLogistikDone = false;
                                                                $allLogistikDoneApproved = false;
                                                                $hasManagerLogistikNeeded = false;
                                                                $allManagerLogistikNeededApproved = false;
                                                                $hasSeniorManagerLogistikNeeded = false;
                                                                $allSeniorManagerLogistikNeededApproved = false;

                                                                if ($document->komatHistReqs) {
                                                                    $hasLogistikUpload = $document->komatHistReqs->some(
                                                                        function ($req) {
                                                                            return $req->komatPositions &&
                                                                                $req->komatPositions->some(function ($pos) {
                                                                                    return $pos->level === 'logistik_upload';
                                                                                });
                                                                        }
                                                                    );
                                                                    $logistikApproved = $hasLogistikUpload &&
                                                                        $document->komatHistReqs->every(function ($req) {
                                                                            return $req->komatPositions
                                                                                ->filter(function ($pos) {
                                                                                    return $pos->level === 'logistik_upload';
                                                                                })
                                                                                ->every(function ($pos) {
                                                                                    return $pos->status_process === 'done';
                                                                                });
                                                                        });

                                                                    $hasDiscussion = $document->komatHistReqs->some(
                                                                        function ($req) {
                                                                            return $req->komatPositions &&
                                                                                $req->komatPositions->some(function ($pos) {
                                                                                    return $pos->level === 'discussion';
                                                                                });
                                                                        }
                                                                    );

                                                                    $firstReq = $document->komatHistReqs->first();

                                                                    

                                                                    // hitung status per projectpic
                                                                    // hitung status per projectpic
                                                                    $unitStatuses = [];
                                                                    foreach ($projectpics as $name => $meta) {
                                                                        $unitId = $meta['id'];

                                                                        $hasDiscussionUnit = $firstReq
                                                                            && $firstReq->komatPositions
                                                                            && $firstReq->komatPositions->some(fn($pos) =>
                                                                                $pos->level === 'discussion' && $pos->unit_id === $unitId
                                                                            );

                                                                        $allApprovedUnit = $hasDiscussionUnit
                                                                            && $firstReq
                                                                            && $firstReq->komatPositions
                                                                                ->filter(fn($pos) => $pos->level === 'discussion' && $pos->unit_id === $unitId)
                                                                                ->every(fn($pos) => $pos->status_process === 'done');

                                                                        if (!$hasDiscussionUnit) {
                                                                            $status = null; // tidak ikut
                                                                        } elseif ($allApprovedUnit) {
                                                                            $status = 'Aktif'; // semua approve
                                                                        } else {
                                                                            $status = 'Ongoing'; // ada diskusi tapi belum approve semua
                                                                        }

                                                                        $unitStatuses[$name] = $status;
                                                                    }



                                                                    $allApproved = $hasDiscussion &&
                                                                        $document->komatHistReqs->every(function ($req) {
                                                                            return $req->komatPositions
                                                                                ->filter(function ($pos) {
                                                                                    return $pos->level === 'discussion';
                                                                                })
                                                                                ->every(function ($pos) {
                                                                                    return $pos->status_process === 'done';
                                                                                });
                                                                        });

                                                                    

                                                                    
                                                                    $hasResume = $document->komatHistReqs->some(function ($req) {
                                                                        return $req->komatPositions &&
                                                                            $req->komatPositions->some(function ($pos) {
                                                                                return $pos->level === 'resume';
                                                                            });
                                                                    });
                                                                    $allResumeApproved = $hasResume &&
                                                                        $document->komatHistReqs->every(function ($req) {
                                                                            return $req->komatPositions
                                                                                ->filter(function ($pos) {
                                                                                    return $pos->level === 'resume';
                                                                                })
                                                                                ->every(function ($pos) {
                                                                                    return $pos->status_process === 'done';
                                                                                });
                                                                        });

                                                                    $hasSMLevel = $document->komatHistReqs->some(function ($req) {
                                                                        return $req->komatPositions &&
                                                                            $req->komatPositions->some(function ($pos) {
                                                                                return $pos->level === 'sm_level';
                                                                            });
                                                                    });
                                                                    $allSMLevelApproved = $hasSMLevel &&
                                                                        $document->komatHistReqs->every(function ($req) {
                                                                            return $req->komatPositions
                                                                                ->filter(function ($pos) {
                                                                                    return $pos->level === 'sm_level';
                                                                                })
                                                                                ->every(function ($pos) {
                                                                                    return $pos->status_process === 'done';
                                                                                });
                                                                        });

                                                                    $hasMTPRReview = $document->komatHistReqs->some(
                                                                        function ($req) {
                                                                            return $req->komatPositions &&
                                                                                $req->komatPositions->some(function ($pos) {
                                                                                    return $pos->level === 'mtpr_review';
                                                                                });
                                                                        }
                                                                    );
                                                                    $allMTPRReviewApproved = $hasMTPRReview &&
                                                                        $document->komatHistReqs->every(function ($req) {
                                                                            return $req->komatPositions
                                                                                ->filter(function ($pos) {
                                                                                    return $pos->level === 'mtpr_review';
                                                                                })
                                                                                ->every(function ($pos) {
                                                                                    return $pos->status_process === 'done';
                                                                                });
                                                                        });

                                                                    $hasLogistikDone = $document->komatHistReqs->some(
                                                                        function ($req) {
                                                                            return $req->komatPositions &&
                                                                                $req->komatPositions->some(function ($pos) {
                                                                                    return $pos->level === 'logistik_done';
                                                                                });
                                                                        }
                                                                    );
                                                                    $allLogistikDoneApproved = $hasLogistikDone &&
                                                                        $document->komatHistReqs->every(function ($req) {
                                                                            return $req->komatPositions
                                                                                ->filter(function ($pos) {
                                                                                    return $pos->level === 'logistik_done';
                                                                                })
                                                                                ->every(function ($pos) {
                                                                                    return $pos->status_process === 'done';
                                                                                });
                                                                        });

                                                                    $hasManagerLogistikNeeded = $document->komatHistReqs->some(
                                                                        function ($req) {
                                                                            return $req->komatPositions &&
                                                                                $req->komatPositions->some(function ($pos) {
                                                                                    return $pos->level === 'managerlogistikneeded';
                                                                                });
                                                                        }
                                                                    );
                                                                    $allManagerLogistikNeededApproved = $hasManagerLogistikNeeded &&
                                                                        $document->komatHistReqs->every(function ($req) {
                                                                            return $req->komatPositions
                                                                                ->filter(function ($pos) {
                                                                                    return $pos->level === 'managerlogistikneeded';
                                                                                })
                                                                                ->every(function ($pos) {
                                                                                    return $pos->status_process === 'done';
                                                                                });
                                                                        });

                                                                    $hasSeniorManagerLogistikNeeded = $document->komatHistReqs->some(
                                                                        function ($req) {
                                                                            return $req->komatPositions &&
                                                                                $req->komatPositions->some(function ($pos) {
                                                                                    return $pos->level === 'seniormanagerlogistikneeded';
                                                                                });
                                                                        }
                                                                    );
                                                                    $allSeniorManagerLogistikNeededApproved = $hasSeniorManagerLogistikNeeded &&
                                                                        $document->komatHistReqs->every(function ($req) {
                                                                            return $req->komatPositions
                                                                                ->filter(function ($pos) {
                                                                                    return $pos->level === 'seniormanagerlogistikneeded';
                                                                                })
                                                                                ->every(function ($pos) {
                                                                                    return $pos->status_process === 'done';
                                                                                });
                                                                        });
                                                                }

                                                                $logistikIndicator = !$hasLogistikUpload ? 'red' : ($logistikApproved ? 'green' : 'orange');
                                                                $discussionIndicator = !$hasDiscussion ? 'red' : ($allApproved ? 'green' : 'orange');
                                                                $resumeIndicator = !$hasResume ? 'red' : ($allResumeApproved ? 'green' : 'orange');
                                                                $smLevelIndicator = !$hasSMLevel ? 'red' : ($allSMLevelApproved ? 'green' : 'orange');
                                                                $mtprReviewIndicator = !$hasMTPRReview ? 'red' : ($allMTPRReviewApproved ? 'green' : 'orange');
                                                                $logistikDoneIndicator = !$hasLogistikDone ? 'red' : ($allLogistikDoneApproved ? 'green' : 'orange');
                                                                $managerLogistikNeededIndicator = !$hasManagerLogistikNeeded ? 'red' : ($allManagerLogistikNeededApproved ? 'green' : 'orange');
                                                                $seniorManagerLogistikNeededIndicator = !$hasSeniorManagerLogistikNeeded ? 'red' : ($allSeniorManagerLogistikNeededApproved ? 'green' : 'orange');

                                                                $classbox1 = !$hasLogistikUpload ? 'box' : ($logistikApproved ? 'boxblue' : 'boxorange');
                                                                $classbox2 = !$hasDiscussion ? 'box' : ($allApproved ? 'boxblue' : 'boxorange');
                                                                $classbox3 = !$hasResume ? 'box' : ($allResumeApproved ? 'boxblue' : 'boxorange');
                                                                $classbox4 = !$hasSMLevel ? 'box' : ($allSMLevelApproved ? 'boxblue' : 'boxorange');
                                                                $classbox5 = !$hasMTPRReview ? 'box' : ($allMTPRReviewApproved ? 'boxblue' : 'boxorange');
                                                                $classbox6 = !$hasLogistikDone ? 'box' : ($allLogistikDoneApproved ? 'boxblue' : 'boxorange');
                                                                $classbox7 = !$hasManagerLogistikNeeded ? 'box' : ($allManagerLogistikNeededApproved ? 'boxblue' : 'boxorange');
                                                                $classbox8 = !$hasSeniorManagerLogistikNeeded ? 'box' : ($allSeniorManagerLogistikNeededApproved ? 'boxblue' : 'boxorange');

                                                                $positionPercentage = match (true) {
                                                                    $document->logisticauthoritylevel == 'seniormanagerneeded' && $allSeniorManagerLogistikNeededApproved => 100,
                                                                    $document->logisticauthoritylevel == 'seniormanagerneeded' && $allManagerLogistikNeededApproved => 87.5,
                                                                    $document->logisticauthoritylevel == 'managerneeded' && $allManagerLogistikNeededApproved => 100,
                                                                    $allLogistikDoneApproved => 75,
                                                                    $allMTPRReviewApproved => 62.5,
                                                                    $allSMLevelApproved => 50,
                                                                    $allResumeApproved => 37.5,
                                                                    $allApproved => 25,
                                                                    $logistikApproved => 12.5,
                                                                    default => 0,
                                                                };
                                                            @endphp
                                                            <tr>
                                                                @if ($index === 0 && $spGroup === $supplierGroups->first() && $reqGroup === $requirementGroups->first())
                                                                    <td rowspan="{{ $documents->count() }}">
                                                                        <div class="icheck-primary">
                                                                            <input type="checkbox" value="{{ $document->id }}"
                                                                                name="document_ids_{{ $document->id }}[]"
                                                                                id="checkbox{{ $document->id }}">
                                                                            <label for="checkbox{{ $document->id }}"></label>
                                                                        </div>
                                                                    </td>
                                                                    <td rowspan="{{ $documents->count() }}">
                                                                        {{ $counter++ }}
                                                                    </td>
                                                                    <td rowspan="{{ $documents->count() }}">
                                                                        <div style="display: flex; flex-direction: column; gap: 5px;">
                                                                            <span>{{ $komat_name ?? 'N/A' }}</span>
                                                                        </div>
                                                                    </td>
                                                                    <td rowspan="{{ $documents->count() }}">
                                                                        <div style="display: flex; flex-direction: column; gap: 5px;">
                                                                            <span>{{ $firstDocInGroup->newbomkomat->material ?? 'N/A' }}</span>
                                                                        </div>
                                                                    </td>
                                                                @endif
                                                                @if ($index === 0 && $reqGroup === $requirementGroups->first())
                                                                    <td rowspan="{{ $rowspanSupplier }}">
                                                                        <div style="display: flex; flex-direction: column; gap: 5px;">
                                                                            <span>{{ $supplierName }}</span>
                                                                        </div>
                                                                    </td>
                                                                @endif
                                                                @if ($index === 0)
                                                                    <td rowspan="{{ $rowspanRequirement }}">
                                                                        <div style="display: flex; flex-direction: column; gap: 5px;">
                                                                            <span>{{ $requirementName }}</span>
                                                                        </div>
                                                                    </td>
                                                                @endif
                                                                <td>
                                                                    <span style="padding: 3px;">{{ $document->revision ?? 'N/A' }}</span>
                                                                </td>
                                                                <td>
                                                                    <span style="padding: 3px;">{{ $document->discussion_number ?? 'N/A' }}</span>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex flex-column align-items-center">
                                                                        <span class="badge 
                                                                            {{ ($document->status ?? '') === 'Terbuka' ? 'bg-danger' : 
                                                                            (($document->status ?? '') === 'Tertutup' ? 'bg-success' : 'bg-secondary') }} w-100 text-center py-2">
                                                                            {{ $document->status ?? 'N/A' }}
                                                                        </span>

                                                                        <span class="badge bg-info w-100 text-center py-2">
                                                                            {{ $document->documentstatus ?? 'N/A' }}
                                                                        </span>

                                                                        <span class="badge bg-warning w-100 text-center py-2">
                                                                            {{ $document->logisticauthoritylevel ?? 'N/A' }}
                                                                        </span>

                                                                        <span class="badge bg-{{ $positionPercentage === 100 ? 'success' : 'warning' }} w-100 text-center py-2">
                                                                            {{ $positionPercentage }}% Completed
                                                                        </span>
                                                                    </div>
                                                                </td>

                                                                <!-- Status dokumen -->
                                                                <style>
                                                                    body {
                                                                        font-family: Arial, sans-serif;
                                                                        margin: 0;
                                                                        padding: 0;
                                                                        background-color: #f0f2f5;
                                                                        /* Warna latar belakang yang lembut */
                                                                    }

                                                                    .project-actionkus {
                                                                        display: flex;
                                                                        align-items: center;
                                                                        justify-content: center;
                                                                        padding: 20px;
                                                                    }

                                                                    .action-group {
                                                                        display: flex;
                                                                        align-items: center;
                                                                        margin: 0 10px;
                                                                    }

                                                                    .arrow {
                                                                        margin: 0 5px;
                                                                        font-size: 24px;
                                                                        color: #00b0ff;
                                                                        /* Warna biru yang futuristik */
                                                                    }

                                                                    .container {
                                                                        display: flex;
                                                                        align-items: center;
                                                                    }

                                                                    .boxblue {
                                                                        margin-right: 5px;
                                                                        border: 1px solid #00b0ff;
                                                                        border-radius: 10px;
                                                                        padding: 10px;
                                                                        /* Tambahkan sedikit padding */
                                                                        background-color: #e1f5fe;
                                                                        /* Warna biru muda */
                                                                        box-shadow: 0 2px 4px rgba(0, 176, 255, 0.2);
                                                                    }

                                                                    .box {
                                                                        margin-right: 5px;
                                                                        border: 1px solid #ccc;
                                                                        border-radius: 10px;
                                                                        padding: 10px;
                                                                        /* Tambahkan sedikit padding */
                                                                        background-color: #ffffff;
                                                                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                                                                    }

                                                                    h2 {
                                                                        font-size: 20px;
                                                                        margin-bottom: 10px;
                                                                        color: #333;
                                                                    }

                                                                    ul {
                                                                        list-style-type: none;
                                                                        margin: 0;
                                                                        padding: 0;
                                                                    }

                                                                    li {
                                                                        margin-bottom: 10px;
                                                                    }

                                                                    .keterangan {
                                                                        margin-left: 5px;
                                                                        font-size: 16px;
                                                                        color: #555;
                                                                    }

                                                                    .indicator {
                                                                        width: 20px;
                                                                        height: 20px;
                                                                        border-radius: 50%;
                                                                        margin-right: 5px;
                                                                    }

                                                                    .green {
                                                                        background-color: #4caf50;
                                                                        /* Warna hijau */
                                                                    }

                                                                    .red {
                                                                        background-color: #f44336;
                                                                        /* Warna merah */
                                                                    }

                                                                    .yellow {
                                                                        background-color: #ffeb3b;
                                                                        /* Warna kuning */
                                                                    }

                                                                    .blue {
                                                                        background-color: #2196f3;
                                                                        /* Warna biru */
                                                                    }

                                                                    .orange {
                                                                        background-color: #ff9800;
                                                                        /* Warna orange */
                                                                    }

                                                                    .black {
                                                                        background-color: #212121;
                                                                        /* Warna hitam */
                                                                    }
                                                                </style>
                                                                <td class="project-actionkus text-right">



                                                                    <div style="position: relative;">
                                                                        <div class="container">
                                                                            <span class="arrow" style="color: rgba(255, 255, 255, 0);">→</span>
                                                                            <a class="{{ $classbox1 }}" href="#">
                                                                                <div class="container">
                                                                                    <div class="indicator {{ $logistikIndicator }}"
                                                                                        title="{{ !$hasLogistikUpload ? 'Logistik upload belum ada' : ($logistikApproved ? 'Logistik upload selesai' : 'Logistik upload sedang berlangsung') }}">
                                                                                    </div>
                                                                                    <span class="keterangan">Logistik</span>
                                                                                </div>
                                                                            </a>
                                                                            <span class="arrow">→</span>
                                                                            <a class="{{ $classbox2 }}" href="#">
                                                                                <div class="container">
                                                                                    <div class="indicator {{ $discussionIndicator }}"
                                                                                        title="{{ !$hasDiscussion ? 'Diskusi belum ada' : ($allApproved ? 'Semua unit diskusi selesai' : 'Diskusi sedang berlangsung') }}">
                                                                                    </div>
                                                                                    <span class="keterangan">Diskusi</span>
                                                                                </div>
                                                                            </a>
                                                                            <span class="arrow">→</span>
                                                                            <a class="{{ $classbox3 }}" href="#">
                                                                                <div class="container">
                                                                                    <div class="indicator {{ $resumeIndicator }}"
                                                                                        title="{{ !$hasResume ? 'Resume belum ada' : ($allResumeApproved ? 'Semua resume selesai' : 'Resume sedang berlangsung') }}">
                                                                                    </div>
                                                                                    <span class="keterangan">Resume</span>
                                                                                </div>
                                                                            </a>
                                                                            <span class="arrow">→</span>
                                                                            <a class="{{ $classbox4 }}" href="#">
                                                                                <div class="container">
                                                                                    <div class="indicator {{ $smLevelIndicator }}"
                                                                                        title="{{ !$hasSMLevel ? 'SM Level belum ada' : ($allSMLevelApproved ? 'Semua SM Level selesai' : 'SM Level sedang berlangsung') }}">
                                                                                    </div>
                                                                                    <span class="keterangan">SM Level</span>
                                                                                </div>
                                                                            </a>
                                                                            @if ($document->documentstatus !== 'rejectedbysm')
                                                                                <span class="arrow">→</span>
                                                                                <a class="{{ $classbox5 }}" href="#">
                                                                                    <div class="container">
                                                                                        <div class="indicator {{ $mtprReviewIndicator }}"
                                                                                            title="{{ !$hasMTPRReview ? 'MTPR Review belum ada' : ($allMTPRReviewApproved ? 'Semua MTPR Review selesai' : 'MTPR Review sedang berlangsung') }}">
                                                                                        </div>
                                                                                        <span class="keterangan">MTPR Review</span>
                                                                                    </div>
                                                                                </a>
                                                                                <span class="arrow">→</span>
                                                                                <a class="{{ $classbox6 }}" href="#">
                                                                                    <div class="container">
                                                                                        <div class="indicator {{ $logistikDoneIndicator }}"
                                                                                            title="{{ !$hasLogistikDone ? 'Logistik belum ada' : ($allLogistikDoneApproved ? 'Logistik selesai' : 'Logistik sedang berlangsung') }}">
                                                                                        </div>
                                                                                        <span class="keterangan">Purchaser</span>
                                                                                    </div>
                                                                                </a>
                                                                                @if ($document->logisticauthoritylevel == 'managerneeded' || $document->logisticauthoritylevel == 'seniormanagerneeded')
                                                                                    <span class="arrow">→</span>
                                                                                    <a class="{{ $classbox7 }}" href="#">
                                                                                        <div class="container">
                                                                                            <div class="indicator {{ $managerLogistikNeededIndicator }}"
                                                                                                title="{{ !$hasManagerLogistikNeeded ? 'Manager Logistik belum ada' : ($allManagerLogistikNeededApproved ? 'Manager Logistik selesai' : 'Manager Logistik sedang berlangsung') }}">
                                                                                            </div>
                                                                                            <span class="keterangan">Manager Logistik</span>
                                                                                        </div>
                                                                                    </a>
                                                                                @endif
                                                                                @if ($document->logisticauthoritylevel == 'seniormanagerneeded')
                                                                                    <span class="arrow">→</span>
                                                                                    <a class="{{ $classbox8 }}" href="#">
                                                                                        <div class="container">
                                                                                            <div class="indicator {{ $seniorManagerLogistikNeededIndicator }}"
                                                                                                title="{{ !$hasSeniorManagerLogistikNeeded ? 'Senior Manager Logistik belum ada' : ($allSeniorManagerLogistikNeededApproved ? 'Senior Manager Logistik selesai' : 'Senior Manager Logistik sedang berlangsung') }}">
                                                                                            </div>
                                                                                            <span class="keterangan">Senior Manager Logistik</span>
                                                                                        </div>
                                                                                    </a>
                                                                                @endif
                                                                                <span class="arrow" style="color: rgba(255, 255, 255, 0);">→</span>
                                                                            @endif
                                                                            
                                                                        </div>
                                                                    </div>



                                                                   
                                                                </td>
                                                                <td class="project-actions text-right">
                                                                    <div class="col-md-12 text-right column-layout">
                                                                        <a class="btn btn-primary btn-sm" href="/komatprocesshistory/show/{{ $document->id }}">
                                                                            <i class="fas fa-folder"></i> Detail
                                                                        </a> 
                                                                    </div> 
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endforeach
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Individual Unit Tab Content -->
                                @foreach ($units as $unit)
                                    <div class="tab-pane fade" id="unit-content-{{ $project->id }}-{{ $unit->id }}"
                                        role="tabpanel"
                                        aria-labelledby="unit-tab-{{ $project->id }}-{{ $unit->id }}">
                                        @php
                                            $unitDocuments = $groupedKomatProcesses
                                                ->flatMap(function ($documents) use ($unit) {
                                                    return $documents->filter(function ($doc) use ($unit) {
                                                        return $doc->komatHistReqs->some(function ($req) use ($unit) {
                                                            return $req->komatPositions->some(function ($pos) use ($unit) {
                                                                return $pos->unit_id === $unit->id;
                                                            });
                                                        });
                                                    });
                                                })
                                                ->groupBy(function ($doc) {
                                                    return $doc->komatProcess->komat_name ?? 'N/A';
                                                })
                                                ->filter(function ($documents) {
                                                    return $documents->isNotEmpty();
                                                });
                                        @endphp

                                        @if ($unitDocuments->isNotEmpty())
                                            <table class="table table-bordered table-hover table-striped" id="table-{{ $project->id }}-{{ $unit->id }}">
                                                <thead>
                                                    <tr>
                                                        <th><input type="checkbox" class="selectAll" onchange="toggleAllCheckboxes(this, '{{ $project->id }}-{{ $unit->id }}')"></th>
                                                        <th>No</th>
                                                        <th>Komat</th>
                                                        <th>Material</th>
                                                        <th>Supplier</th>
                                                        <th>Req</th>
                                                        <th>Rev</th>
                                                        <th>Disc No</th>
                                                        <th>Status</th>
                                                        <th>Posisi Komrev</th>
                                                        <th>Keterangan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $counter = 1; @endphp
                                                    @foreach ($unitDocuments as $komat_name => $documents)
                                                        @php
                                                            $supplierGroups = $documents
                                                                ->groupBy(function ($doc) {
                                                                    return $doc->supplier->name ?? 'N/A';
                                                                })
                                                                ->values();
                                                        @endphp
                                                        @foreach ($supplierGroups as $supplierIndex => $spGroup)
                                                            @php
                                                                $rowspanSupplier = $spGroup->count();
                                                                $firstDocInGroup = $spGroup->first();
                                                                $supplierName = $firstDocInGroup->supplier->name ?? 'N/A';
                                                                $requirementGroups = $spGroup
                                                                    ->groupBy(function ($doc) {
                                                                        return $doc->requirement->name ?? 'N/A';
                                                                    })
                                                                    ->values();
                                                            @endphp
                                                            @foreach ($requirementGroups as $requirementIndex => $reqGroup)
                                                                @php
                                                                    $rowspanRequirement = $reqGroup->count();
                                                                    $firstDocInReqGroup = $reqGroup->first();
                                                                    $requirementName = $firstDocInReqGroup->requirement->name ?? 'N/A';
                                                                @endphp
                                                                @foreach ($reqGroup as $index => $document)
                                                                    @php
                                                                        $hasLogistikUpload = false;
                                                                        $logistikApproved = false;
                                                                        $hasDiscussion = false;
                                                                        $allApproved = false;
                                                                        $hasResume = false;
                                                                        $allResumeApproved = false;
                                                                        $hasSMLevel = false;
                                                                        $allSMLevelApproved = false;
                                                                        $hasMTPRReview = false;
                                                                        $allMTPRReviewApproved = false;
                                                                        $hasLogistikDone = false;
                                                                        $allLogistikDoneApproved = false;
                                                                        $hasManagerLogistikNeeded = false;
                                                                        $allManagerLogistikNeededApproved = false;
                                                                        $hasSeniorManagerLogistikNeeded = false;
                                                                        $allSeniorManagerLogistikNeededApproved = false;

                                                                        if ($document->komatHistReqs) {
                                                                            $hasLogistikUpload = $document->komatHistReqs->some(
                                                                                function ($req) {
                                                                                    return $req->komatPositions &&
                                                                                        $req->komatPositions->some(function ($pos) {
                                                                                            return $pos->level === 'logistik_upload';
                                                                                        });
                                                                                }
                                                                            );
                                                                            $logistikApproved = $hasLogistikUpload &&
                                                                                $document->komatHistReqs->every(function ($req) {
                                                                                    return $req->komatPositions
                                                                                        ->filter(function ($pos) {
                                                                                            return $pos->level === 'logistik_upload';
                                                                                        })
                                                                                        ->every(function ($pos) {
                                                                                            return $pos->status_process === 'done';
                                                                                        });
                                                                                });

                                                                            $hasDiscussion = $document->komatHistReqs->some(
                                                                                function ($req) {
                                                                                    return $req->komatPositions &&
                                                                                        $req->komatPositions->some(function ($pos) {
                                                                                            return $pos->level === 'discussion';
                                                                                        });
                                                                                }
                                                                            );
                                                                            $allApproved = $hasDiscussion &&
                                                                                $document->komatHistReqs->every(function ($req) {
                                                                                    return $req->komatPositions
                                                                                        ->filter(function ($pos) {
                                                                                            return $pos->level === 'discussion';
                                                                                        })
                                                                                        ->every(function ($pos) {
                                                                                            return $pos->status_process === 'done';
                                                                                        });
                                                                                });

                                                                            $hasResume = $document->komatHistReqs->some(function ($req) {
                                                                                return $req->komatPositions &&
                                                                                    $req->komatPositions->some(function ($pos) {
                                                                                        return $pos->level === 'resume';
                                                                                    });
                                                                            });
                                                                            $allResumeApproved = $hasResume &&
                                                                                $document->komatHistReqs->every(function ($req) {
                                                                                    return $req->komatPositions
                                                                                        ->filter(function ($pos) {
                                                                                            return $pos->level === 'resume';
                                                                                        })
                                                                                        ->every(function ($pos) {
                                                                                            return $pos->status_process === 'done';
                                                                                        });
                                                                                });

                                                                            $hasSMLevel = $document->komatHistReqs->some(function ($req) {
                                                                                return $req->komatPositions &&
                                                                                    $req->komatPositions->some(function ($pos) {
                                                                                        return $pos->level === 'sm_level';
                                                                                    });
                                                                            });
                                                                            $allSMLevelApproved = $hasSMLevel &&
                                                                                $document->komatHistReqs->every(function ($req) {
                                                                                    return $req->komatPositions
                                                                                        ->filter(function ($pos) {
                                                                                            return $pos->level === 'sm_level';
                                                                                        })
                                                                                        ->every(function ($pos) {
                                                                                            return $pos->status_process === 'done';
                                                                                        });
                                                                                });

                                                                            $hasMTPRReview = $document->komatHistReqs->some(
                                                                                function ($req) {
                                                                                    return $req->komatPositions &&
                                                                                        $req->komatPositions->some(function ($pos) {
                                                                                            return $pos->level === 'mtpr_review';
                                                                                        });
                                                                                }
                                                                            );
                                                                            $allMTPRReviewApproved = $hasMTPRReview &&
                                                                                $document->komatHistReqs->every(function ($req) {
                                                                                    return $req->komatPositions
                                                                                        ->filter(function ($pos) {
                                                                                            return $pos->level === 'mtpr_review';
                                                                                        })
                                                                                        ->every(function ($pos) {
                                                                                            return $pos->status_process === 'done';
                                                                                        });
                                                                                });

                                                                            $hasLogistikDone = $document->komatHistReqs->some(
                                                                                function ($req) {
                                                                                    return $req->komatPositions &&
                                                                                        $req->komatPositions->some(function ($pos) {
                                                                                            return $pos->level === 'logistik_done';
                                                                                        });
                                                                                }
                                                                            );
                                                                            $allLogistikDoneApproved = $hasLogistikDone &&
                                                                                $document->komatHistReqs->every(function ($req) {
                                                                                    return $req->komatPositions
                                                                                        ->filter(function ($pos) {
                                                                                            return $pos->level === 'logistik_done';
                                                                                        })
                                                                                        ->every(function ($pos) {
                                                                                            return $pos->status_process === 'done';
                                                                                        });
                                                                                });

                                                                            $hasManagerLogistikNeeded = $document->komatHistReqs->some(
                                                                                function ($req) {
                                                                                    return $req->komatPositions &&
                                                                                        $req->komatPositions->some(function ($pos) {
                                                                                            return $pos->level === 'managerlogistikneeded';
                                                                                        });
                                                                                }
                                                                            );
                                                                            $allManagerLogistikNeededApproved = $hasManagerLogistikNeeded &&
                                                                                $document->komatHistReqs->every(function ($req) {
                                                                                    return $req->komatPositions
                                                                                        ->filter(function ($pos) {
                                                                                            return $pos->level === 'managerlogistikneeded';
                                                                                        })
                                                                                        ->every(function ($pos) {
                                                                                            return $pos->status_process === 'done';
                                                                                        });
                                                                                });

                                                                            $hasSeniorManagerLogistikNeeded = $document->komatHistReqs->some(
                                                                                function ($req) {
                                                                                    return $req->komatPositions &&
                                                                                        $req->komatPositions->some(function ($pos) {
                                                                                            return $pos->level === 'seniormanagerlogistikneeded';
                                                                                        });
                                                                                }
                                                                            );
                                                                            $allSeniorManagerLogistikNeededApproved = $hasSeniorManagerLogistikNeeded &&
                                                                                $document->komatHistReqs->every(function ($req) {
                                                                                    return $req->komatPositions
                                                                                        ->filter(function ($pos) {
                                                                                            return $pos->level === 'seniormanagerlogistikneeded';
                                                                                        })
                                                                                        ->every(function ($pos) {
                                                                                            return $pos->status_process === 'done';
                                                                                        });
                                                                                });
                                                                        }

                                                                                $firstReq = $document->komatHistReqs->first();

                                                                                    // daftar projectpic dengan unit_id + singkatan
                                                                                    // daftar projectpic dengan unit_id + singkatan
                                                                    

                                                                                    // hitung status per projectpic
                                                                                    $unitStatuses = [];
                                                                                    foreach ($projectpics as $name => $meta) {
                                                                                        $unitId = $meta['id'];

                                                                                        $hasDiscussion = $firstReq
                                                                                            && $firstReq->komatPositions
                                                                                            && $firstReq->komatPositions->some(fn($pos) =>
                                                                                                $pos->level === 'discussion' && $pos->unit_id === $unitId
                                                                                            );

                                                                                        $allApproved = $hasDiscussion
                                                                                            && $firstReq
                                                                                            && $firstReq->komatPositions
                                                                                                ->filter(fn($pos) => $pos->level === 'discussion' && $pos->unit_id === $unitId)
                                                                                                ->every(fn($pos) => $pos->status_process === 'done');

                                                                                        if (!$hasDiscussion) {
                                                                                            $status = null; // tidak ikut
                                                                                        } elseif ($allApproved) {
                                                                                            $status = 'Aktif'; // semua approve
                                                                                        } else {
                                                                                            $status = 'Ongoing'; // ada diskusi tapi belum approve semua
                                                                                        }

                                                                                        $unitStatuses[$name] = $status;
                                                                                    }

                                                                                $logistikIndicator = !$hasLogistikUpload ? 'red' : ($logistikApproved ? 'green' : 'orange');
                                                                                $discussionIndicator = !$hasDiscussion ? 'red' : ($allApproved ? 'green' : 'orange');
                                                                                $resumeIndicator = !$hasResume ? 'red' : ($allResumeApproved ? 'green' : 'orange');
                                                                                $smLevelIndicator = !$hasSMLevel ? 'red' : ($allSMLevelApproved ? 'green' : 'orange');
                                                                                $mtprReviewIndicator = !$hasMTPRReview ? 'red' : ($allMTPRReviewApproved ? 'green' : 'orange');
                                                                                $logistikDoneIndicator = !$hasLogistikDone ? 'red' : ($allLogistikDoneApproved ? 'green' : 'orange');
                                                                                $managerLogistikNeededIndicator = !$hasManagerLogistikNeeded ? 'red' : ($allManagerLogistikNeededApproved ? 'green' : 'orange');
                                                                                $seniorManagerLogistikNeededIndicator = !$hasSeniorManagerLogistikNeeded ? 'red' : ($allSeniorManagerLogistikNeededApproved ? 'green' : 'orange');

                                                                                $classbox1 = !$hasLogistikUpload ? 'box' : ($logistikApproved ? 'boxblue' : 'boxorange');
                                                                                $classbox2 = !$hasDiscussion ? 'box' : ($allApproved ? 'boxblue' : 'boxorange');
                                                                                $classbox3 = !$hasResume ? 'box' : ($allResumeApproved ? 'boxblue' : 'boxorange');
                                                                                $classbox4 = !$hasSMLevel ? 'box' : ($allSMLevelApproved ? 'boxblue' : 'boxorange');
                                                                                $classbox5 = !$hasMTPRReview ? 'box' : ($allMTPRReviewApproved ? 'boxblue' : 'boxorange');
                                                                                $classbox6 = !$hasLogistikDone ? 'box' : ($allLogistikDoneApproved ? 'boxblue' : 'boxorange');
                                                                                $classbox7 = !$hasManagerLogistikNeeded ? 'box' : ($allManagerLogistikNeededApproved ? 'boxblue' : 'boxorange');
                                                                                $classbox8 = !$hasSeniorManagerLogistikNeeded ? 'box' : ($allSeniorManagerLogistikNeededApproved ? 'boxblue' : 'boxorange');

                                                                                $positionPercentage = match (true) {
                                                                                    $document->logisticauthoritylevel == 'seniormanagerneeded' && $allSeniorManagerLogistikNeededApproved => 100,
                                                                                    $document->logisticauthoritylevel == 'seniormanagerneeded' && $allManagerLogistikNeededApproved => 87.5,
                                                                                    $document->logisticauthoritylevel == 'managerneeded' && $allManagerLogistikNeededApproved => 100,
                                                                                    $allLogistikDoneApproved => 75,
                                                                                    $allMTPRReviewApproved => 62.5,
                                                                                    $allSMLevelApproved => 50,
                                                                                    $allResumeApproved => 37.5,
                                                                                    $allApproved => 25,
                                                                                    $logistikApproved => 12.5,
                                                                                    default => 0,
                                                                                };
                                                                            @endphp
                                                                            <tr>
                                                                                @if ($index === 0 && $spGroup === $supplierGroups->first() && $reqGroup === $requirementGroups->first())
                                                                                    <td rowspan="{{ $documents->count() }}">
                                                                                        <div class="icheck-primary">
                                                                                            <input type="checkbox" value="{{ $document->id }}"
                                                                                                name="document_ids_{{ $document->id }}[]"
                                                                                                id="checkbox{{ $document->id }}">
                                                                                            <label for="checkbox{{ $document->id }}"></label>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td rowspan="{{ $documents->count() }}">
                                                                                        {{ $counter++ }}
                                                                                    </td>
                                                                                    <td rowspan="{{ $documents->count() }}">
                                                                                        <div style="display: flex; flex-direction: column; gap: 5px;">
                                                                                            <span>{{ $komat_name ?? 'N/A' }}</span>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td rowspan="{{ $documents->count() }}">
                                                                                        <div style="display: flex; flex-direction: column; gap: 5px;">
                                                                                            <span>{{ $firstDocInGroup->newbomkomat->material ?? 'N/A' }}</span>
                                                                                        </div>
                                                                                    </td>
                                                                                @endif
                                                                                
                                                                                @if ($index === 0 && $reqGroup === $requirementGroups->first())
                                                                                    <td rowspan="{{ $rowspanSupplier }}">
                                                                                        <div style="display: flex; flex-direction: column; gap: 5px;">
                                                                                            <span>{{ $supplierName }}</span>
                                                                                        </div>
                                                                                    </td>
                                                                                @endif
                                                                                @if ($index === 0)
                                                                                    <td rowspan="{{ $rowspanRequirement }}">
                                                                                        <div style="display: flex; flex-direction: column; gap: 5px;">
                                                                                            <span>{{ $requirementName }}</span>
                                                                                        </div>
                                                                                    </td>
                                                                                @endif
                                                                                <td>
                                                                                    <span style="padding: 3px;">{{ $document->revision ?? 'N/A' }}</span>
                                                                                </td>
                                                                                <td>
                                                                                    <span style="padding: 3px;">{{ $document->discussion_number ?? 'N/A' }}</span>
                                                                                </td>
                                                                                <td>
                                                                                    <div class="d-flex flex-column align-items-center">
                                                                                        <span class="badge 
                                                                                            {{ ($document->status ?? '') === 'Terbuka' ? 'bg-danger' : 
                                                                                            (($document->status ?? '') === 'Tertutup' ? 'bg-success' : 'bg-secondary') }} w-100 text-center py-2">
                                                                                            {{ $document->status ?? 'N/A' }}
                                                                                        </span>

                                                                                        <span class="badge bg-info w-100 text-center py-2">
                                                                                            {{ $document->documentstatus ?? 'N/A' }}
                                                                                        </span>

                                                                                        <span class="badge bg-warning w-100 text-center py-2">
                                                                                            {{ $document->logisticauthoritylevel ?? 'N/A' }}
                                                                                        </span>

                                                                                        <span class="badge bg-{{ $positionPercentage === 100 ? 'success' : 'warning' }} w-100 text-center py-2">
                                                                                            {{ $positionPercentage }}% Completed
                                                                                        </span>
                                                                                    </div>
                                                                                </td>
                                                                                <!-- Status dokumen -->
                                                                                <style>
                                                                                    body {
                                                                                        font-family: Arial, sans-serif;
                                                                                        margin: 0;
                                                                                        padding: 0;
                                                                                        background-color: #f0f2f5;
                                                                                        /* Warna latar belakang yang lembut */
                                                                                    }

                                                                                    .project-actionkus {
                                                                                        display: flex;
                                                                                        align-items: center;
                                                                                        justify-content: center;
                                                                                        padding: 20px;
                                                                                    }

                                                                                    .action-group {
                                                                                        display: flex;
                                                                                        align-items: center;
                                                                                        margin: 0 10px;
                                                                                    }

                                                                                    .arrow {
                                                                                        margin: 0 5px;
                                                                                        font-size: 24px;
                                                                                        color: #00b0ff;
                                                                                        /* Warna biru yang futuristik */
                                                                                    }

                                                                                    .container {
                                                                                        display: flex;
                                                                                        align-items: center;
                                                                                    }

                                                                                    .boxblue {
                                                                                        margin-right: 5px;
                                                                                        border: 1px solid #00b0ff;
                                                                                        border-radius: 10px;
                                                                                        padding: 10px;
                                                                                        /* Tambahkan sedikit padding */
                                                                                        background-color: #e1f5fe;
                                                                                        /* Warna biru muda */
                                                                                        box-shadow: 0 2px 4px rgba(0, 176, 255, 0.2);
                                                                                    }

                                                                                    .box {
                                                                                        margin-right: 5px;
                                                                                        border: 1px solid #ccc;
                                                                                        border-radius: 10px;
                                                                                        padding: 10px;
                                                                                        /* Tambahkan sedikit padding */
                                                                                        background-color: #ffffff;
                                                                                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                                                                                    }

                                                                                    h2 {
                                                                                        font-size: 20px;
                                                                                        margin-bottom: 10px;
                                                                                        color: #333;
                                                                                    }

                                                                                    ul {
                                                                                        list-style-type: none;
                                                                                        margin: 0;
                                                                                        padding: 0;
                                                                                    }

                                                                                    li {
                                                                                        margin-bottom: 10px;
                                                                                    }

                                                                                    .keterangan {
                                                                                        margin-left: 5px;
                                                                                        font-size: 16px;
                                                                                        color: #555;
                                                                                    }

                                                                                    .indicator {
                                                                                        width: 20px;
                                                                                        height: 20px;
                                                                                        border-radius: 50%;
                                                                                        margin-right: 5px;
                                                                                    }

                                                                                    .green {
                                                                                        background-color: #4caf50;
                                                                                        /* Warna hijau */
                                                                                    }

                                                                                    .red {
                                                                                        background-color: #f44336;
                                                                                        /* Warna merah */
                                                                                    }

                                                                                    .yellow {
                                                                                        background-color: #ffeb3b;
                                                                                        /* Warna kuning */
                                                                                    }

                                                                                    .blue {
                                                                                        background-color: #2196f3;
                                                                                        /* Warna biru */
                                                                                    }

                                                                                    .orange {
                                                                                        background-color: #ff9800;
                                                                                        /* Warna orange */
                                                                                    }

                                                                                    .black {
                                                                                        background-color: #212121;
                                                                                        /* Warna hitam */
                                                                                    }
                                                                                </style>
                                                                                <td class="project-actionkus text-right">
                                                                                    <div style="position: relative;">
                                                                                        
                                                                                    <div class="container">

                                                                                        <a class="{{ $classbox1 }}" href="#">
                                                                                            <div class="container">
                                                                                                <div class="indicator {{ $logistikIndicator }}"
                                                                                                    title="{{ !$hasLogistikUpload ? 'Logistik upload belum ada' : ($logistikApproved ? 'Logistik upload selesai' : 'Logistik upload sedang berlangsung') }}">
                                                                                                </div>
                                                                                                <span class="keterangan">Logistik</span>
                                                                                            </div>
                                                                                        </a>

                                                                                        <span class="arrow">→</span>

                                                                                        <div class="{{ $classbox2 }}" style="height: 300px;">
                                                                                            <h2>Eng</h2>
                                                                                            <ul>
                                                                                                @foreach (['Product Engineering', 'Mechanical Engineering System', 'Electrical Engineering System', 'Quality Engineering', 'RAMS'] as $projectpic)
                                                                                                    @php $status = $unitStatuses[$projectpic] ?? null; @endphp
                                                                                                    <li>
                                                                                                        <div class="indicator 
                                                                                                            {{ $status === 'Aktif'
                                                                                                                ? 'green'
                                                                                                                : ($status === 'Ongoing'
                                                                                                                    ? 'orange'
                                                                                                                    : ($status === null
                                                                                                                        ? 'black'
                                                                                                                        : 'red')) }}"
                                                                                                            title="{{ $status === 'Aktif'
                                                                                                                ? $projectpic . ' sudah approve'
                                                                                                                : ($status === 'Ongoing'
                                                                                                                    ? $projectpic . ' sudah melakukan feedback dan belum approve'
                                                                                                                    : ($status === null
                                                                                                                        ? $projectpic . ' tidak terlibat'
                                                                                                                        : $projectpic . ' belum dikerjakan')) }}">
                                                                                                        </div>
                                                                                                        <span class="keterangan">{{ $projectpics[$projectpic]['singkatan'] }}</span>
                                                                                                    </li>
                                                                                                @endforeach
                                                                                            </ul>
                                                                                        </div>

                                                                                        <div class="{{ $classbox2 }}" style="height: 300px;">
                                                                                            <h2>Des</h2>
                                                                                            <ul>
                                                                                                @foreach (['Desain Mekanik & Interior', 'Desain Bogie & Wagon', 'Desain Carbody', 'Desain Elektrik'] as $projectpic)
                                                                                                    @php $status = $unitStatuses[$projectpic] ?? null; @endphp
                                                                                                    <li>
                                                                                                        <div class="indicator 
                                                                                                            {{ $status === 'Aktif'
                                                                                                                ? 'green'
                                                                                                                : ($status === 'Ongoing'
                                                                                                                    ? 'orange'
                                                                                                                    : ($status === null
                                                                                                                        ? 'black'
                                                                                                                        : 'red')) }}"
                                                                                                            title="{{ $status === 'Aktif'
                                                                                                                ? $projectpic . ' sudah approve'
                                                                                                                : ($status === 'Ongoing'
                                                                                                                    ? $projectpic . ' sudah melakukan feedback dan belum approve'
                                                                                                                    : ($status === null
                                                                                                                        ? $projectpic . ' tidak terlibat'
                                                                                                                        : $projectpic . ' belum dikerjakan')) }}">
                                                                                                        </div>
                                                                                                        <span class="keterangan">{{ $projectpics[$projectpic]['singkatan'] }}</span>
                                                                                                    </li>
                                                                                                @endforeach
                                                                                            </ul>
                                                                                        </div>

                                                                                        <div class="{{ $classbox2 }}" style="height: 300px;">
                                                                                            <h2>TP</h2>
                                                                                            <ul>
                                                                                                @foreach (['Preparation & Support', 'Welding Technology', 'Shop Drawing', 'Teknologi Proses'] as $projectpic)
                                                                                                    @php $status = $unitStatuses[$projectpic] ?? null; @endphp
                                                                                                    <li>
                                                                                                        <div class="indicator 
                                                                                                            {{ $status === 'Aktif'
                                                                                                                ? 'green'
                                                                                                                : ($status === 'Ongoing'
                                                                                                                    ? 'orange'
                                                                                                                    : ($status === null
                                                                                                                        ? 'black'
                                                                                                                        : 'red')) }}"
                                                                                                            title="{{ $status === 'Aktif'
                                                                                                                ? $projectpic . ' sudah approve'
                                                                                                                : ($status === 'Ongoing'
                                                                                                                    ? $projectpic . ' sudah melakukan feedback dan belum approve'
                                                                                                                    : ($status === null
                                                                                                                        ? $projectpic . ' tidak terlibat'
                                                                                                                        : $projectpic . ' belum dikerjakan')) }}">
                                                                                                        </div>
                                                                                                        <span class="keterangan">{{ $projectpics[$projectpic]['singkatan'] }}</span>
                                                                                                    </li>
                                                                                                @endforeach
                                                                                            </ul>
                                                                                        </div>

                                                                                        <span class="arrow">→</span>
                                                                                        
                                                                                        <a class="{{ $classbox3 }}" href="#">
                                                                                            <div class="container">
                                                                                                <div class="indicator {{ $resumeIndicator }}"
                                                                                                    title="{{ !$hasResume ? 'Resume belum ada' : ($allResumeApproved ? 'Semua resume selesai' : 'Resume sedang berlangsung') }}">
                                                                                                </div>
                                                                                                <span class="keterangan">Resume</span>
                                                                                            </div>
                                                                                            <div class="container">
                                                                                                <span class="arrow">↓</span>
                                                                                            </div>
                                                                                            <div class="container">
                                                                                                <div class="indicator {{ $smLevelIndicator }}"
                                                                                                    title="{{ !$hasSMLevel ? 'SM Level belum ada' : ($allSMLevelApproved ? 'Semua SM Level selesai' : 'SM Level sedang berlangsung') }}">
                                                                                                </div>
                                                                                                <span class="keterangan">SM Level</span>
                                                                                            </div>
                                                                                            <div class="container">
                                                                                                <span class="arrow">↓</span>
                                                                                            </div>
                                                                                            <div class="container">
                                                                                                <div class="indicator {{ $mtprReviewIndicator }}"
                                                                                                    title="{{ !$hasMTPRReview ? 'Review belum ada' : ($allMTPRReviewApproved ? 'Semua Review selesai' : 'Review sedang berlangsung') }}">
                                                                                                </div>
                                                                                                <span class="keterangan">Review</span>
                                                                                            </div>
                                                                                            <div class="container">
                                                                                                <span class="arrow">↓</span>
                                                                                            </div>
                                                                                            <div class="container">
                                                                                                <div class="indicator {{ $logistikDoneIndicator }}"
                                                                                                    title="{{ !$hasLogistikDone ? 'Purchaser belum ada' : ($allLogistikDoneApproved ? 'Purchaser selesai' : 'Purchaser sedang berlangsung') }}">
                                                                                                </div>
                                                                                                <span class="keterangan">Purchaser</span>
                                                                                            </div>
                                                                                            <div class="container">
                                                                                                <span class="arrow">↓</span>
                                                                                            </div>
                                                                                            <div class="container">
                                                                                                <div class="indicator {{ $managerLogistikNeededIndicator }}"
                                                                                                    title="{{ !$hasManagerLogistikNeeded ? 'M. Logistik belum ada' : ($allManagerLogistikNeededApproved ? 'M. Logistik selesai' : 'M. Logistik sedang berlangsung') }}">
                                                                                                </div>
                                                                                                <span class="keterangan">M. Logistik</span>
                                                                                            </div>
                                                                                            <div class="container">
                                                                                                <span class="arrow">↓</span>
                                                                                            </div>
                                                                                            <div class="container">
                                                                                                <div class="indicator {{ $seniorManagerLogistikNeededIndicator }}"
                                                                                                    title="{{ !$hasSeniorManagerLogistikNeeded ? 'SM. Logistik belum ada' : ($allSeniorManagerLogistikNeededApproved ? 'SM. Logistik selesai' : 'SM. Logistik sedang berlangsung') }}">
                                                                                                </div>
                                                                                                <span class="keterangan">SM. Logistik</span>
                                                                                            </div>
                                                                                        </a>

                                                                                        

                                                                                        

                                                                                        


                                                                                            
                                                                                    </div>
                                                                                </td>
                                                                                <td class="project-actions text-right">
                                                                                    <div class="col-md-12 text-right column-layout">
                                                                                        <a class="btn btn-primary btn-sm" href="/komatprocesshistory/show/{{ $document->id }}">
                                                                                            <i class="fas fa-folder"></i> Detail
                                                                                        </a> 
                                                                                    </div> 
                                                                                </td>
                                                                    </tr>
                                                                @endforeach
                                                            @endforeach
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <div class="alert alert-info">
                                                Tidak ada dokumen yang tersedia untuk unit {{ $unit->name }} di proyek
                                                {{ $project->title ?? 'Project ' . $project->id }}.
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                Tidak ada dokumen yang tersedia untuk proyek
                                {{ $project->title ?? 'Project ' . $project->id }}.
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="alert alert-info">
                    Tidak ada proyek yang tersedia.
                </div>
            @endif
        </div>
    </div>
@endsection



@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTables for all tables
            $('.table').each(function() {
                $(this).DataTable({
                    paging: true,
                    lengthChange: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    autoWidth: false,
                    responsive: true,
                    destroy: true // Allow re-initialization when switching projects or units
                });
            });
        });

        function filterByProject(projectId) {
            // Hide all project tables
            $('.project-table').hide();
            // Show the selected project table
            $('#project-table-' + projectId).show();
            // Re-initialize DataTables for all visible unit tables in the selected project
            $('#project-table-' + projectId + ' .table').each(function() {
                $(this).DataTable({
                    paging: true,
                    lengthChange: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    autoWidth: false,
                    responsive: true,
                    destroy: true
                });
            });
        }

        function toggleAllCheckboxes(source, tableId) {
            $(`input[name^="document_ids_"][id*="checkbox"]`, `#table-${tableId}`).prop('checked', source.checked);
        }
    </script>
@endpush
