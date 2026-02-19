@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="/">Cari Kode Material</a></li>
                        <li class="breadcrumb-item active text-bold">Tracking Kode Material</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container-fluid h-100">
        <div class="row h-100">
            {{-- Sidebar Sessions --}}
            <div class="col-md-3 border-end bg-light p-3 overflow-auto" style="height: calc(100vh - 70px);">
                <a href="{{ route('chat.new') }}" class="btn btn-primary w-100 mb-3">+ New Session</a>
                <h5>Sessions</h5>
                <ul class="list-group">
                    @foreach ($sessions as $s)
                        <li class="list-group-item {{ $activeSession && $activeSession->id === $s->id ? 'active' : '' }}">
                            <a href="{{ route('chat.switch', $s->session_id) }}"
                                class="{{ $activeSession && $activeSession->id === $s->id ? 'text-white' : 'text-dark' }}">
                                {{ $s->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Chat Window --}}
            <div class="col-md-9 d-flex flex-column p-3" style="height: calc(100vh - 70px);">
                <div id="chatWindow" class="flex-grow-1 overflow-auto mb-3 p-2 bg-white rounded border">
                    @forelse($messages as $m)
                        <div
                            class="d-flex mb-3 {{ $m->sender === 'user' ? 'justify-content-end' : 'justify-content-start' }}">
                            <div class="d-flex align-items-end">
                                @if ($m->sender === 'bot')
                                    <div class="me-2">
                                        <i class="fas fa-robot fa-2x text-secondary"></i>
                                    </div>
                                @endif
                                <div class="card shadow-sm {{ $m->sender === 'user' ? 'bg-primary text-white' : 'bg-light' }}"
                                    style="max-width:70%; border-radius: 1rem;">
                                    <div class="card-body p-2">
                                        <p class="mb-0" style="white-space: pre-wrap; word-wrap: break-word;">
                                            {{ $m->message }}
                                        </p>
                                    </div>
                                </div>
                                @if ($m->sender === 'user')
                                    <div class="ms-2">
                                        <i class="fas fa-user-circle fa-2x text-primary"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center mt-4">Belum ada pesan.</p>
                    @endforelse
                </div>

                {{-- Input Form --}}
                <form id="chatForm" class="d-flex">
                    @csrf
                    <input type="text" name="message" id="messageInput" class="form-control me-2"
                        placeholder="Ketik pesan..." required>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>

    <script>
        const chatWindow = document.getElementById('chatWindow');

        function scrollToBottom() {
            chatWindow.scrollTop = chatWindow.scrollHeight;
        }

        document.getElementById('chatForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const message = document.getElementById('messageInput').value.trim();
            const token = document.querySelector('input[name=_token]').value;

            if (message === '') return;

            // Display user message immediately
            const userDiv = document.createElement('div');
            userDiv.className = 'd-flex mb-3 justify-content-end';
            userDiv.innerHTML = `
                <div class="d-flex align-items-end">
                    <div class="card shadow-sm bg-primary text-white" style="max-width:70%; border-radius:1rem;">
                        <div class="card-body p-2">
                            <p class="mb-0">${message}</p>
                        </div>
                    </div>
                    <div class="ms-2"><i class="fas fa-user-circle fa-2x text-primary"></i></div>
                </div>`;
            chatWindow.appendChild(userDiv);
            scrollToBottom();

            // Display bot loading
            const botLoadingDiv = document.createElement('div');
            botLoadingDiv.id = 'botLoading';
            botLoadingDiv.className = 'd-flex mb-3 justify-content-start';
            botLoadingDiv.innerHTML = `
                <div class="d-flex align-items-end">
                    <div class="me-2"><i class="fas fa-robot fa-2x text-secondary"></i></div>
                    <div class="card shadow-sm bg-light" style="max-width:70%; border-radius:1rem;">
                        <div class="card-body p-2">
                            <p class="mb-0"><em>Bot sedang mengetik...</em></p>
                        </div>
                    </div>
                </div>`;
            chatWindow.appendChild(botLoadingDiv);
            scrollToBottom();

            $.ajax({
                url: "{{ route('chat.send') }}",
                type: "POST",
                data: {
                    _token: token,
                    message
                },
                success: function(data) {
                    const loadingDiv = document.getElementById('botLoading');
                    if (loadingDiv) loadingDiv.remove();

                    if (data.success) {
                        const botDiv = document.createElement('div');
                        botDiv.className = 'd-flex mb-3 justify-content-start';
                        botDiv.innerHTML = `
                            <div class="d-flex align-items-end">
                                <div class="me-2"><i class="fas fa-robot fa-2x text-secondary"></i></div>
                                <div class="card shadow-sm bg-light" style="max-width:70%; border-radius:1rem;">
                                    <div class="card-body p-2">
                                        <p class="mb-0">${data.botReply}</p>
                                    </div>
                                </div>
                            </div>`;
                        chatWindow.appendChild(botDiv);
                        scrollToBottom();

                        document.getElementById('messageInput').value = '';
                    } else {
                        Swal.fire('Error', data.message || 'Terjadi kesalahan', 'error');
                    }
                },
                error: function(err) {
                    console.error(err);
                    Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                }
            });
        });

        scrollToBottom();
    </script>
@endpush
